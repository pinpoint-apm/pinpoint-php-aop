<?php

declare(strict_types=1);

// Keep this check independent of native extensions and integration services.
error_reporting(E_ALL);
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
});

if ($argc !== 2) {
    fwrite(STDERR, "Usage: php php_parser_compatibility.php PARSER_AUTOLOAD_PATH\n");
    exit(2);
}
require $argv[1];
spl_autoload_register(function (string $class): void {
    if (strpos($class, 'Pinpoint\\') === 0) {
        require dirname(__DIR__) . '/lib/' . str_replace('\\', '/', $class) . '.php';
    }
});

// This monitor runs the generated wrapper without native tracing dependencies.
class ParserCompatibilityMonitor
{
    public static array $events = [];
    public static ?Exception $exception = null;
    private string $method;

    public function __construct(string $method, $object, ...$arguments)
    {
        $this->method = substr($method, strrpos($method, '::') + 2);
    }

    public function onBefore(): void
    {
        self::$events[] = 'before:' . $this->method;
    }

    public function onEnd(&$value): void
    {
        self::$events[] = 'end:' . $this->method;
        if (is_int($value)) {
            ++$value;
        }
    }

    public function onException(Exception $error): void
    {
        self::$events[] = 'exception:' . $this->method;
        self::$exception = $error;
    }
}

$sourceFile = tempnam(sys_get_temp_dir(), 'pinpoint-parser-');
$mappingFile = tempnam(sys_get_temp_dir(), 'pinpoint-map-');
try {
    // Parse PHP 8.2 readonly classes and PHP 8.3 typed constants on every host.
    file_put_contents($sourceFile, <<<'SOURCE'
<?php
namespace Compatibility;
readonly class ParserFixture {
    public const string NAME = 'fixture';
    public function name(): string { return self::NAME; }
}
SOURCE
    );
    $plugins = [];
    $pluginParser = new Pinpoint\Common\PluginParser($sourceFile, $plugins);
    if ($pluginParser->getNamespace() !== 'Compatibility'
        || $pluginParser->getClassName() !== 'ParserFixture') {
        throw new RuntimeException('Plugin parser lost the class identity');
    }

    $handler = new Pinpoint\Common\AspectClassHandle('Compatibility\\ParserFixture');
    $visitor = new Pinpoint\Common\OriginFileVisitor();
    $visitor->runAllVisitor($sourceFile, $handler);

    // Exercise the project's AST builder and pretty printer with a real file.
    $expected = ['Compatibility\\ParserFixture' => $sourceFile];
    $generator = new Pinpoint\Common\GenClassIndexMap($expected);
    $generator->save($mappingFile);
    $actual = include $mappingFile;
    if ($actual !== $expected) {
        throw new RuntimeException('Generated class mapping changed');
    }
    // Generate a real intercepted class, then execute its wrappers.
    Pinpoint\Common\Utils::$CLS_DIR = sys_get_temp_dir();
    $className = 'ParserCompatibility\\InterceptionFixture';
    $handler = new Pinpoint\Common\AspectClassHandle($className);
    foreach (['value', 'staticValue', 'setValue', 'protectedValue', 'privateValue', 'fail', 'alias'] as $method) {
        $handler->addJoinPoint($method, ParserCompatibilityMonitor::class);
    }
    $handler->addFunctionAlias('strrev', 'ParserCompatibility\\rewritten');
    $visitor = new Pinpoint\Common\OriginFileVisitor();
    $visitor->runAllVisitor(__DIR__ . '/fixtures/parser_interception.php', $handler);
    $classMap = Pinpoint\Common\MonitorClass::getInstance()->getJointClassMap();
    $generatedFile = $classMap[$className];
    require $generatedFile;

    $instance = new $className();
    if ($instance->value(3) !== 5 || $className::staticValue(4) !== 9) {
        throw new RuntimeException('Return-value interception changed');
    }
    $instance->setValue(7);
    if ($instance->state !== 7 || $instance->invokeHidden(3) !== 21) {
        throw new RuntimeException('Void or protected/private interception changed');
    }
    $original = new RuntimeException('original');
    try {
        $instance->fail($original);
        throw new LogicException('Generated wrapper swallowed the exception');
    } catch (RuntimeException $caught) {
        if ($caught !== $original || ParserCompatibilityMonitor::$exception !== $original) {
            throw new LogicException('Exception identity changed');
        }
    }
    if ($instance->alias('abc') !== 'aliased:ABC') {
        throw new RuntimeException('Function alias rewriting changed');
    }
    $expectedEvents = [
        'before:value', 'end:value',
        'before:staticValue', 'end:staticValue',
        'before:setValue', 'end:setValue',
        'before:protectedValue', 'end:protectedValue',
        'before:privateValue', 'end:privateValue',
        'before:fail', 'exception:fail',
        'before:alias', 'end:alias',
    ];
    if (ParserCompatibilityMonitor::$events !== $expectedEvents) {
        throw new RuntimeException('Generated callback order changed');
    }
    echo 'Parser and generated interception behavior passed on PHP ', PHP_VERSION, "\n";
} finally {
    if (isset($generatedFile)) {
        unlink($generatedFile);
    }
    unlink($sourceFile);
    unlink($mappingFile);
}

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
    echo 'Project parser APIs passed on PHP ', PHP_VERSION, "\n";
} finally {
    unlink($sourceFile);
    unlink($mappingFile);
}

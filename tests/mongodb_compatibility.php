<?php

declare(strict_types=1);

error_reporting(E_ALL);
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
});

if ($argc !== 2) {
    fwrite(STDERR, "Usage: php mongodb_compatibility.php MONGODB_AUTOLOAD_PATH\n");
    exit(2);
}
require $argv[1];

// Construction and collection selection are lazy and need no MongoDB server.
$client = new MongoDB\Client('mongodb://127.0.0.1:27017');
$collection = $client->selectCollection('compatibility', 'smoke');
if (!$collection instanceof MongoDB\Collection) {
    throw new RuntimeException('Client did not create a collection');
}

// These are the methods registered by the MongoDB instrumentation plugin.
foreach (['insertOne', 'updateOne', 'deleteMany', 'find'] as $method) {
    $reflection = new ReflectionMethod($collection, $method);
    if (!$reflection->isPublic() || $reflection->isStatic()) {
        throw new RuntimeException("MongoDB plugin target changed: $method");
    }
}
echo 'MongoDB library and extension ', phpversion('mongodb'),
    ' passed on PHP ', PHP_VERSION, "\n";

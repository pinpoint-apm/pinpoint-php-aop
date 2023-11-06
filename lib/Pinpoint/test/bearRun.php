<?php

declare(strict_types=1);

require_once './bootstrap.php';

define('AOP_CACHE_DIR', __DIR__ . '/Cache');
define('APPLICATION_NAME', 'ci-test');

require_once __DIR__ . '/../../../auto_pinpointed.php';
require_once __DIR__ . '/../Plugins/__init__.php';

use Pinpoint\test\Bear;

$bear = new Bear(1, 3, 4);
$ar = [];


for ($x = 0; $x <= 100; $x += 10) {
    $bear->output("a", 2, $ar);
}

assert($bear->output("a", 2, $ar) == 1011);

$bear->noreturn("a", 2);

try {
    $bear->pdoNamespaceAlias();
} catch (PDOException $e) {
}
$bear->curlAlias();
$bear->callInternal();
$bear->callstaticFuncFoo("a", 2, $ar);

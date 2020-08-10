<?php
require_once './bootstrap.php';


//use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
$filename='test.php';
$code = file_get_contents($filename);


$parser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
$ast = $parser->parse($code);
$prettyPrinter = new PrettyPrinter\Standard();
echo $prettyPrinter->prettyPrintFile($ast);
file_put_contents($filename.'_6.ast', print_r($ast,true));

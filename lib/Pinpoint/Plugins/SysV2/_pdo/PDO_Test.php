<?php
namespace Pinpoint\Plugins\SysV2\_pdo;

require_once __DIR__ . "/__init__.php";

use PHPUnit\Framework\TestCase;


/**
 * Class OrgClassParseTest
 * Test  convert user  class to dst AOP class
 * @package pinpoint\test
 */
class PDO_Test extends TestCase
{
    public function test_FileVisitor()
    {
        $mysql_host = "10.23.45.69";
        $dbname = "abc";
        $dsn = "mysql:host=$mysql_host;port=33060;dbname=$dbname";
        $dbinfo = parse_connect_string($dsn);
        $this->assertEquals($dbinfo["host"], "10.23.45.69");
        $dsn = "sqlite:/opt/databases/mydb.sq3
;dbname=abd";
        $dbinfo = parse_connect_string($dsn);
        $this->assertEquals($dbinfo["host"], "localhost");
    }
}
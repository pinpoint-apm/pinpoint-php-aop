<?php

namespace Pinpoint\test;

/**
 * I'm the placeholder
 */
class Bear
{
    public function __construct($a, $b, $c)
    {
    }

    public $pdo;
    /**
     * @output @output I'm annotation
     * @parameter: 
     * @return
     */
    public function output(string $_1, int $_2, array $_3)
    {
        return 1010;
    }

    public function noreturn(string $_1, int $_2)
    {
        $this->output('1', 3, [4]);
    }

    /**
     * $joinClass->addClassNameAlias('PDO', \Pinpoint\Plugins\Sys\PDO\PDO::class);
     */
    public function pdoNamespaceAlias($driver_options = NULL)
    {
        $this->pdo = new \PDO('sqlite:/tmp/foo.db', 'user', 'pass', $driver_options);
    }

    /**
     * $joinClass->addFunctionAlias('curl_init', 'Pinpoint\Plugins\Sys\curl\curl_init');
     */
    public function curlAlias($host = "www.example.com")
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $host);

        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $head = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
    }

    public function callInternal()
    {
        $this->checkProtected();
        $this->checkPrivate();
    }

    /**
     * test protected function
     */
    protected function checkProtected()
    {
        return __METHOD__;
    }
    /**
     * test private function
     */
    private function checkPrivate()
    {
        $this->checkProtected();
        return __METHOD__;
    }

    public static function staticFuncFoo(string $_1, int $_2, array $_3)
    {
    }

    public static function callstaticFuncFoo(string $_1, int $_2, array $_3)
    {
        self::staticFuncFoo("1", 2, [3]);
    }
}

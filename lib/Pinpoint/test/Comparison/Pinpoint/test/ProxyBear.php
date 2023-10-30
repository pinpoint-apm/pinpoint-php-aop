<?php

namespace Pinpoint\test;

/**
 * I'm the placeholder
 */
class ProxyBear
{
    public $pdo;
    /**
     * @output
     * @parameter: 
     * @return
     */
    public function output(string $_1, int $_2, array &$_3)
    {
        return 1010;
    }
    public function noreturn(string $_1, int $_2, array &$_3, $a, $b, $c)
    {
    }
    /**
     * $joinClass->addClassNameAlias('PDO', \Pinpoint\Plugins\Sys\PDO\PDO::class);
     */
    public function pdoNamespaceAlias($driver_options = NULL)
    {
        $this->pdo = new \Pinpoint\Plugins\Sys\PDO\PDO('dsn', 'user', 'pass', $driver_options);
    }
    /**
     * $joinClass->addFunctionAlias('curl_init', 'Pinpoint\Plugins\Sys\curl\curl_init');
     */
    public function curlAlias($host = "www.example.com")
    {
        $ch = \Pinpoint\Plugins\Sys\curl\curl_init();
        \Pinpoint\Plugins\Sys\curl\curl_setopt($ch, CURLOPT_URL, $host);
        \Pinpoint\Plugins\Sys\curl\curl_setopt($ch, CURLOPT_HEADER, TRUE);
        \Pinpoint\Plugins\Sys\curl\curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        // remove body
        \Pinpoint\Plugins\Sys\curl\curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = \Pinpoint\Plugins\Sys\curl\curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        \Pinpoint\Plugins\Sys\curl\curl_close($ch);
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
    protected function checkPrivate()
    {
        $this->checkProtected();
        return __METHOD__;
    }
}
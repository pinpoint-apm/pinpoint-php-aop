<?php

namespace pinpoint\test;


class AccessRemote
{
    public function gotoUrl($url)
    {
        $ch = \pinpoint\test\curl_init();

        $ch = call_user_func('curl_init');

        curl_setopt($ch, CURLOPT_URL, $url);

        $headers = array(
            'Content-type: application/xml',
            'Authorization: gfhjui',
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        echo curl_exec($ch);

        curl_close($ch);
//        $this->callExample();
    }

    private function callExample()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "exmaple.com");

        echo curl_exec($ch);

        curl_close($ch);
    }
}
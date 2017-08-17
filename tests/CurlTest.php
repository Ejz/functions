<?php

class CurlTest extends PHPUnit_Framework_TestCase {
    public function testCurl() {
        $ua = get_user_agent();
        $content = curl('https://ejz.ru/ua', array(
            CURLOPT_USERAGENT => $ua
        ));
        $_ua = trim($content);
        $this->assertTrue($ua === $_ua);
        //
        $result = curl($_ = 'https://ejz.ru', array('format' => 'array'));
        $this->assertTrue(isset($result[$_]));
    }
}

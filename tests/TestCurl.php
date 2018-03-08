<?php

use PHPUnit\Framework\TestCase;

class TestCurl extends TestCase {
    /**
     */
    public function testCurlCommon() {
        $ua = __FUNCTION__;
        $content = curl('https://ejz.ru/ua', [
            CURLOPT_USERAGENT => $ua,
        ]);
        $this->assertTrue($ua === trim($content));
        //
        $result = curl($_ = 'https://ejz.ru', ['format' => 'array']);
        $this->assertTrue(isset($result[$_]));
    }
}

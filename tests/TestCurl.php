<?php

use PHPUnit\Framework\TestCase;

class TestCurl extends TestCase {
    /**
     */
    public function testCurlCommon() {
        $ua = __FUNCTION__;
        $content = curl('https://httpbin.org/headers', [
            CURLOPT_USERAGENT => $ua,
        ]);
        $json = json_decode(trim($content), true);
        $this->assertTrue($ua === $json['headers']['User-Agent']);
        //
        $result = curl($_ = 'https://ejz.io', ['format' => 'array']);
        $this->assertTrue(isset($result[$_]));
    }
}

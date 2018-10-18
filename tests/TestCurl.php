<?php

use PHPUnit\Framework\TestCase;

class TestCurl extends TestCase {
    /**
     */
    public function testCurlCommon() {
        $ua = __FUNCTION__;
        $content = iterator_to_array(curl([10 => 'https://httpbin.org/headers'], [
            CURLOPT_USERAGENT => $ua,
        ]));
        $json = json_decode(trim($content[10]['content']), true);
        $this->assertTrue($ua === $json['headers']['User-Agent']);
        //
        $result = iterator_to_array(curl(['https://ejz.io']));
        $this->assertTrue(isset($result[0]['content']));
    }
}

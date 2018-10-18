<?php

use PHPUnit\Framework\TestCase;

class TestCurl extends TestCase {
    /**
     */
    public function testCurlCommon() {
        $ua = __FUNCTION__;
        $content = iterator_to_array(curl([$key = 'https://httpbin.org/headers'], [
            CURLOPT_USERAGENT => $ua,
        ]), true);
        $json = json_decode(trim($content[$key]['content']), true);
        $this->assertTrue($ua === $json['headers']['User-Agent']);
        //
        $result = iterator_to_array(curl([$_ = 'https://ejz.io']), true);
        $this->assertTrue(isset($result[$_]['content']));
    }
}

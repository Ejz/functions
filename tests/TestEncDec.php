<?php

use PHPUnit\Framework\TestCase;

class TestEncDec extends TestCase {
    /**
     */
    public function testEncDecCommon() {
        $string = url_base64_encode('1234567890');
        $this->assertTrue($string === 'MTIzNDU2Nzg5MA');
        //
        $string = url_base64_decode($string);
        $this->assertTrue($string === '1234567890');
        //
        $key = '111';
        $one = xencrypt('1234567890', $key);
        $two = xencrypt('1234567890', $key);
        $this->assertTrue($one and $two and $one != $two);
        $this->assertTrue(xdecrypt($one, $key) === '1234567890');
        $this->assertTrue(xdecrypt($two, $key) === '1234567890');
        $this->assertTrue(!xdecrypt($two, $key . '1'));
        //
        $string = base32_encode('1234567890');
        $this->assertTrue(!!preg_match('~^[ABCDEFGHIJKLMNOPQRSTUVWXYZ234567]+$~', $string));
        //
        $string = base32_decode($string);
        $this->assertTrue($string === '1234567890');
        //
        $key = '111';
        $one = oencrypt('1234567890', $key);
        $two = oencrypt('1234567890', $key);
        $this->assertTrue($one and $two and $one != $two);
        $this->assertTrue(odecrypt($one, $key) === '1234567890');
        $this->assertTrue(odecrypt($two, $key) === '1234567890');
        $this->assertTrue(!odecrypt($two, $key . '1'));
    }
}

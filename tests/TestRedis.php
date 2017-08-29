<?php

class TestRedis extends PHPUnit_Framework_TestCase {
    public function testRedis() {
        R('SET', 'a', '7')
        $this->assertTrue(R('GET', 'a') === '7');
    }
}

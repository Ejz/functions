<?php

class TestRedis extends PHPUnit_Framework_TestCase {
    public function testRedis() {
        R('SET', 'a', '7');
        $this->assertTrue(R('GET', 'a') === '7');
        $this->assertTrue(!!R('DEL', 'a'));
        R('DEL', 'list');
        $ret = R('LPUSH', 'list', "4", "7");
        $this->assertTrue($ret === 2);
        $ret = R('LPUSH', 'list', "2", "3");
        $this->assertTrue($ret === 4);
        $ret = R('LRANGE', 'list', "0", "-1");
        $this->assertTrue(count($ret) === 4);
        R('SET', 'b', 'value');
        $this->assertTrue(R('GET', 'b') === 'value');
        R('EXPIRE', 'b', 2);
        $this->assertTrue(R('GET', 'b') === 'value');
        sleep(3);
        $this->assertTrue(R('GET', 'b') != 'value');
    }
}

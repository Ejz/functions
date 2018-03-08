<?php

use PHPUnit\Framework\TestCase;

class TestToStorage extends TestCase {
    /**
     */
    public function testToStorageCommon() {
        $foo = rtrim(`mktemp`);
        $foo2 = rtrim(`mktemp`);
        $bar = rtrim(`mktemp`);
        file_put_contents($foo, 'foo');
        file_put_contents($foo2, 'foo');
        file_put_contents($bar, 'bar');
        $result = to_storage($foo, ['shards' => 2, 'ext' => 'txt']);
        exec('rm -rf ' . dirname($result));
        $result = to_storage($foo, ['shards' => 2, 'ext' => 'txt']);
        $this->assertTrue(md5_file($result) === md5_file($foo));
        $this->assertTrue(file_get_ext($result) === 'txt');
        unlink($foo);
        unlink($foo2);
        unlink($bar);
    }
}

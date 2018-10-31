<?php

use PHPUnit\Framework\TestCase;

class TestParseSshConfig extends TestCase {
    /**
     */
    public function testParseSshConfigCommon() {
        $content = "# comment\nHost *\nServerAliveInterval 15\n\nHost test\nHostname 127.0.0.1\nUser root";
        $this->assertEquals([
            '*' => ['ServerAliveInterval' => '15'],
            'test' => ['Hostname' => '127.0.0.1', 'User' => 'root'],
        ], parse_ssh_config($content));
    }
}

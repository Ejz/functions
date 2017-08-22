<?php

class TestGetopts extends PHPUnit_Framework_TestCase {
    public function testGetopts() {
        $opts = getopts(array(
            'c' => false,
            'b' => false,
            'a' => false,
            'long-arg' => false,
            'long-arg-2' => true,
            'long-arg-3' => true,
        ), explode(' ', './execute -abc arg --long-arg --long-arg-2 arg2 --long-arg-3=arg3 final'));
        $this->assertEquals('./execute', $opts[0]);
        $this->assertEquals('arg', $opts[1]);
        $this->assertEquals(true, $opts['c']);
        $this->assertEquals(true, $opts['b']);
        $this->assertEquals(true, $opts['a']);
        $this->assertEquals(true, $opts['long-arg']);
        $this->assertEquals('arg2', $opts['long-arg-2']);
        $this->assertEquals('arg3', $opts['long-arg-3']);
        $this->assertEquals('final', $opts[2]);
        //
        $opts = getopts(array(
            'a' => false,
            'F' => true,
            'b' => true,
            'c' => false,
        ), explode(' ', './execute -a mmm -F fff'));
        $this->assertEquals('fff', $opts['F']);
        $this->assertTrue($opts['a']);
        $this->assertTrue(!isset($opts['b']));
        $this->assertTrue(!isset($opts['c']));
        //
        $opts = getopts(array(), explode(' ', './execute -a final'));
        $this->assertTrue(is_string($opts));
        //
        $opts = getopts(array(), explode(' ', './execute --long final'));
        $this->assertTrue(is_string($opts));
        //
        $opts = getopts(array('a' => true), explode(' ', './execute -a1'));
        $this->assertEquals(1, $opts['a']);
        //
        $opts = getopts(array('a' => true, 'b' => false), explode(' ', './execute -ba 1'));
        $this->assertTrue($opts['b'] === true);
        $this->assertTrue($opts['a'] === '1');
        //
        $opts = getopts(array('a' => true, 'b' => false), explode(' ', './execute -ba1'));
        $this->assertTrue($opts['b'] === true);
        $this->assertTrue($opts['a'] === '1');
        //
        $opts = getopts(array('long' => false), explode(' ', './execute --long=1'));
        $this->assertTrue(is_string($opts));
        //
        $opts = getopts(array(), explode(' ', './execute --long'));
        $this->assertTrue(is_string($opts));
        //
        $opts = getopts(array('a' => true), array('./execute', '-a '));
        $this->assertEquals(" ", $opts['a']);
        //
        $opts = getopts(array('r' => true), array('./execute', '-r', "//text()"));
        $this->assertEquals("//text()", $opts['r']);
        //
        $opts = getopts(array('r' => true), array('./execute', '-r', '--', "-r"));
        $this->assertEquals("-r", $opts['r']);
        //
        $opts = getopts(array('r' => ['multiple' => true]), array('./execute', '-rrr', '-r', '--', '-r'));
        $this->assertTrue(count($opts['r']) === 4);
        $this->assertTrue($opts[1] === '-r');
        //
        $opts = getopts(array(), explode(' ', './execute -_'));
        $this->assertTrue(is_string($opts));
    }
}

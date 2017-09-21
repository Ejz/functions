<?php

class TestGetopts extends PHPUnit_Framework_TestCase {
    public function testGetopts() {
        $time = microtime(true);
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
        $opts = getopts(array('a' => true, 'b' => false), array('./execute', '-baASD'));
        $this->assertEquals("ASD", $opts['a']);
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
        //
        $opts = getopts(array('a' => true, 'A' => 'a'), array('./execute', '-a', '1', '-A', '2'));
        $this->assertEquals("2", $opts['a']);
        //
        $opts = getopts(array('a' => ['multiple' => true, 'value' => true], 'A' => 'a'), array('./execute', '-a', '1', '-A', '2'));
        $this->assertEquals(['1', '2'], $opts['a']);
        //
        $opts = getopts(array('a' => ['multiple' => true], 'A' => 'a'), array('./execute', '-a', '1', '-A', '2'));
        $this->assertEquals([true, true], $opts['a']);
        $this->assertEquals('1', $opts['1']);
        $this->assertEquals('2', $opts['2']);
        //
        $opts = getopts(array('filter' => true, 'long' => false, 'f' => true), array('./execute', '-fil', '-filter', 'f', '-long'));
        $this->assertEquals('il', $opts['f']);
        $this->assertEquals('f', $opts['filter']);
        $this->assertEquals(true, $opts['long']);
        //
        $opts = getopts(array('auth' => false, 'no-auth' => false), array('./execute', '-no-auth', '-auth', '-no-auth'));
        $keys = array_keys($opts);
        $this->assertTrue(array_search('auth', $keys, true) < array_search('no-auth', $keys, true));
    }
}

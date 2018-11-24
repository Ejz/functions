<?php

use PHPUnit\Framework\TestCase;

class TestQuickBlast extends TestCase {
    /**
     */
    public function testQuickBlastCommon() {
        $this->assertEquals([[2, 1, 0]], quick_blast(['ABC', 'BC'], 1));
        $this->assertEquals([[2, 0, 1]], quick_blast(['BC', 'ABC'], 1));
        $this->assertEquals([[2, 0, 1]], quick_blast(['BC', 'ABC'], 2));
        $this->assertEquals([], quick_blast(['BC', 'ABC'], 3));
        $this->assertEquals([[4, 0, 7], [3, 0, 1]], quick_blast(['ABCDE', '-ABC---ABCD----'], 3));
        $this->assertEquals([[1, 0, 1], [1, 0, 3]], quick_blast(['E', '-E-E-'], 1));
        $this->assertEquals([[1, 1, 0], [1, 3, 0]], quick_blast(['-E-E-', 'E'], 1));
    }
}

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

    /**
     */
    public function testQuickBlastMultiple() {
        $this->assertEquals([[1, 0, 0, 0]], quick_blast(['A', 'A', 'A'], 1));
        $this->assertEquals([[1, 0, 1, 0]], quick_blast(['A-', '-A', 'A'], 1));
        $this->assertEquals([[1, 0, 0, 0]], quick_blast(['AB', 'AB', 'A'], 1));
        $this->assertEquals([[1, 0, 0, 0]], quick_blast(['A', 'AB', 'AB'], 1));
        $this->assertEquals([[1, 1, 1, 0]], quick_blast(['BA', 'BA', 'A'], 1));
        $this->assertEquals([[1, 0, 1, 1]], quick_blast(['A', 'BA', 'BA'], 1));
        $this->assertEquals([[1, 0, 1, 1], [1, 0, 3, 1]], quick_blast(['A', 'BA A', 'BA'], 1));
    }
}

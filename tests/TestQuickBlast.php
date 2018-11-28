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

    /**
     */
    public function testQuickBlastHighlight() {
        $this->assertEquals(
            'h<em>e</em>llo',
            highlight_quick_blast_results('hello', 1, [[1, 1]])
        );
        $this->assertEquals(
            '<em>h</em><em>e</em>llo',
            highlight_quick_blast_results('hello', 1, [[1, 0], [1, 1]])
        );
        $results = [[1, 0], [1, 1], [1, 2], [1, 3], [1, 4]];
        foreach (range(1, 100) as $_) {
            shuffle($results);
            $this->assertEquals(
                '<em>h</em><em>e</em><em>l</em><em>l</em><em>o</em>',
                highlight_quick_blast_results('hello', 1, $results)
            );
        }
        $results = [[1, 0], [1, 2], [1, 4]];
        foreach (range(1, 10) as $_) {
            shuffle($results);
            $this->assertEquals(
                '<em>h</em>e<em>l</em>l<em>o</em>',
                highlight_quick_blast_results('hello', 1, $results)
            );
        }
        $highlights = [['<em>', '</em>'], ['<s>', '</s>']];
        $results = [[3, 0], [3, 2]];
        $this->assertEquals(
            '<em>he<s>l</s></em><s>lo</s>',
            highlight_quick_blast_results('hello', 1, $results, 100, $highlights)
        );
        $highlights = [['<u>', '</u>'], ['<b>', '</b>'], ['<s>', '</s>']];
        $results = [[3, 0], [3, 1], [3, 2]];
        $this->assertEquals(
            '<u>h<b>e<s>l</s></b></u><b><s>l</s></b><s>o</s>',
            highlight_quick_blast_results('hello', 1, $results, 100, $highlights)
        );
        $highlights = [['<u>', '</u>']];
        $results = [[3, 4]];
        $this->assertEquals(
            'asd <u>asd</u> asd',
            highlight_quick_blast_results('asd asd asd', 1, $results, 2, $highlights)
        );
        $results = [[3, 5]];
        $this->assertEquals(
            '..d <u>asd</u> a..',
            highlight_quick_blast_results('1asd asd asd1', 1, $results, 2, $highlights)
        );
    }

    /**
     */
    public function testQuickBlastTokenizer() {
        $tokenizer = function ($s) {
            preg_match_all('~\w+~', $s, $matches, PREG_OFFSET_CAPTURE);
            $matches = $matches[0];
            foreach ($matches as &$match) {
                $match = [
                    'token' => strtolower($match[0]),
                    'pos' => $match[1],
                ];
                $match['length'] = strlen($match['token']);
            }
            return $matches;
        };
        $this->assertEquals(
            [[5, 0, 1]],
            quick_blast(['hello', '!HELLO!'], 1, $tokenizer)
        );
    }
}

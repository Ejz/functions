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
        $this->assertEquals([[4, 7, 0], [3, 1, 0]], quick_blast(['-ABC---ABCD----', 'ABCDE'], 3));
        $this->assertEquals([[1, 0, 1], [1, 0, 3]], quick_blast(['E', '-E-E-'], 1));
        $this->assertEquals([[1, 1, 0], [1, 3, 0]], quick_blast(['-E-E-', 'E'], 1));
        $this->assertEquals([[5, 0, 0]], quick_blast(['ABCBD', 'ABCBD'], 1));
        $this->assertEquals([3, 0, 1], quick_blast(['BAAB', 'ABAAAB'], 2)[0]);
        $this->assertEquals([3, 1, 3], quick_blast(['BAAB', 'ABAAAB'], 2)[1]);
        $this->assertEquals([3, 0, 1], quick_blast(['BAAB', 'ABAAAB'], 2, ['tokenizer' => '~.~'])[0]);
        $this->assertEquals([3, 0, 1], quick_blast(['BAAB', 'ABAAAB'], 2, ['tokenizer' => '~\w~'])[0]);
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
    public function testQuickBlastBugs() {
        $strings = ['a|a a|a a a', ' aaa|aa|a'];
        $settings = ['unique_substrings' => true, 'tokenizer' => '~\w~', 'delimiter' => "|"];
        $results = quick_blast($strings, 3, $settings);
        $this->assertEquals([[[5, 3], 6, 1]], $results);
        //
        $asd = <<<'ASD'
    if expr="$SERVER_NAME=/www.site.com/"
    e
    ef
    script src="//www.site.com/path/js/js.autostart.v15.
    i
    place9999,(none)
    U999
    Google Tag Manager
    End Google Tag Manager
    Google Analitics
    Google Analitics end
    U999
    U999
    [if lt IE 9]>
    <script>
ASD;
        $strings[0] = implode("\n", nsplit($asd));
        $strings[1] = $strings[0];
        $settings = ['unique_substrings' => true, 'tokenizer' => '~\w+~', 'delimiter' => "\n"];
        [$result] = quick_blast($strings, 10, $settings);
        $sub = substr($strings[0], $result[1], $result[0]);
        $this->assertEquals("script src=\"//www.site.com/path/js/js.autostart.v15", $sub);
        //
        l1:
        $strings = ["A\nB\nC", "A\nB\nC"];
        $results = quick_blast($strings, 1, ['delimiter' => "\n", 'tokenizer' => '~\w+~']);
        $this->assertEquals(
            [[1, 0, 0], [1, 2, 2], [1, 4, 4]],
            $results
        );
        $results = quick_blast($strings, 3, ['delimiter' => "\n", 'tokenizer' => '~\w+~']);
        $this->assertEquals([], $results);
        //
        $results = quick_blast($strings = ['BAAB', 'ABAAAB'], 2);
        $this->assertEquals(
            highlight_quick_blast_results($strings[1], 2, $results),
            highlight_quick_blast_results($strings[1], 2, [$results[0]])
        );
        //
        $strings = [
            'A1 A2 A3 CCC B1 ---------- B2',
            'B1 ---------- B2 DDD A1 A2 A3',
        ];
        $results = quick_blast($strings, 2, ['tokenizer' => '~\w+~']);
        $this->assertEquals(
            [8, 0, 21],
            $results[0]
        );
    }

    /**
     */
    public function testQuickBlastBugWithSort() {
        $link1 = 'https://site.com/';
        $_link1 = 'https://site.com';
        $link2 = 'https://site.com/blog/path/to/article.html';
        //
        [$result1, $result2] = quick_blast([$string = "{$link1}\n{$link2}", $string], 3, [
            'delimiter' => "\n",
        ]);
        $sub1 = substr($string, $result1[1], $result1[0]);
        $sub2 = substr($string, $result2[1], $result2[0]);
        $this->assertEquals($sub1, $link2);
        $this->assertEquals($sub2, $link1);
        //
        [$result1, $result2] = quick_blast([$string = "{$link1}\n{$link2}", $string], 3, [
            'delimiter' => "\n",
            'tokenizer' => '~\w+~'
        ]);
        $sub1 = substr($string, $result1[1], $result1[0]);
        $sub2 = substr($string, $result2[1], $result2[0]);
        $this->assertEquals($sub1, $link2);
        $this->assertEquals($sub2, $_link1);
    }

    /**
     */
    public function testQuickBlastNewFeatures() {
        $results = quick_blast(['A-A-B', 'B=B=A'], 1, ['unique_substrings' => true]);
        $this->assertEquals([[1, 0, 4], [1, 4, 0]], $results);
        //
        $results = quick_blast(['foo|bar', 'foo|bar'], 1, ['tokenizer' => '~\w+~', 'delimiter' => '|']);
        $this->assertEquals([[3, 0, 0], [3, 4, 4]], $results);
        //
        $results = quick_blast(['foo|bar', 'foo|bar'], 3, ['tokenizer' => '~\w~', 'delimiter' => '|']);
        $this->assertEquals([[3, 0, 0], [3, 4, 4]], $results);
        //
        $results = quick_blast(['foo|bar', 'foo|bar'], 3, ['delimiter' => '|']);
        $this->assertEquals([[3, 0, 0], [3, 4, 4]], $results);
        //
        $results = quick_blast(['|foo|bar|', '|bar|foo|'], 3, ['delimiter' => '|']);
        $this->assertEquals([[3, 1, 5], [3, 5, 1]], $results);
        //
        $results = quick_blast(['|f|foo|b|bar|', '|b|bar|f|foo|'], 3, ['delimiter' => '|']);
        $this->assertEquals([[3, 3, 9], [3, 9, 3]], $results);
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
            }
            return $matches;
        };
        $this->assertEquals(
            [[5, 0, 1]],
            quick_blast(['hello', '!HELLO!'], 1, compact('tokenizer'))
        );
        $results = quick_blast(['hello world', '!HELLO!!!!!WORLD'], 2, compact('tokenizer'));
        $this->assertEquals([[[11, 15], 0, 1]], $results);
        $results = quick_blast(['hello world world', '!HELLO!!!!!WORLD HELLO'], 2, compact('tokenizer'));
        $this->assertEquals([[[11, 15], 0, 1]], $results);
        $strings = [
            'hi world hi hi hi world',
            'world hi hi world',
        ];
        $results = quick_blast($strings, 2, ['tokenizer' => '~\w+~']);
        $this->assertEquals(
            'hi <em>world hi hi</em> hi world',
            highlight_quick_blast_results($strings[0], 1, $results)
        );
        $this->assertEquals(
            '<em>world hi hi</em> world',
            highlight_quick_blast_results($strings[1], 2, $results)
        );
        $results = quick_blast([
            $one = 'hello world world',
            $two = '!HELLO!!!!!WORLD HELLO',
        ], 2, compact('tokenizer'));
        $this->assertEquals(
            '<em>hello world</em> world',
            highlight_quick_blast_results($one, 1, $results)
        );
        $this->assertEquals(
            '!<em>HELLO!!!!!WORLD</em> HELLO',
            highlight_quick_blast_results($two, 2, $results)
        );
        $results = quick_blast([
            'hello world world',
            '--hello--world--world',
            '---HELLO---WORLD---HELLO',
            '----HELLO----WORLD----HELLO',
        ], 2, compact('tokenizer'));
        $this->assertEquals(
            [[[11, 12, 13, 14], 0, 2, 3, 4]],
            $results
        );
        $results = quick_blast([
            'hello world world',
            'hello world world',
            '---HELLO---WORLD---HELLO',
            '----HELLO----WORLD----HELLO',
        ], 2, compact('tokenizer'));
        $this->assertEquals(
            [[[11, 11, 13, 14], 0, 0, 3, 4]],
            $results
        );
    }
}

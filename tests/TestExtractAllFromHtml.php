<?php

use PHPUnit\Framework\TestCase;

class TestExtractLinksFromAll extends TestCase {
    /**
     */
    public function testExtractLinksFromAllCommon() {
        $html = '
            <html>
                <head>
                    <title>Hello
                    world</title>
                    <link rel="css/Text" href="link.css">
                    <meta name=keywords content="HI guys">
                </head>
                <body>
                    <!-- comment -->
                    <h1>foo <a>bar <span>1</span> 2</a> 3</h1>
                    <script>var a = 1;</script>
                    <input random=1 type=text name=n value=v>
                </body>
            </html>
        ';
        $all = extract_all_from_html($html, '');
        $this->assertEquals([
            'links' => [
                'link.css' => ['tag' => 'link', 'rel' => 'css/text'],
            ],
            'metas' => [
                ['content' => 'HI guys', 'name' => 'keywords'],
            ],
            'comments' => ['comment'],
            'scripts' => ['var a = 1;'],
            'texts' => ['hello world', 'foo', 'bar', '1', '2', '3'],
            'keywords' => [
                'title' => 'hello world',
                'h1' => 'foo bar 1 2 3',
                'meta' => 'hi guys',
            ],
            'inputs' => [['type' => 'text', 'name' => 'n', 'value' => 'v']],
        ], $all);
    }
}

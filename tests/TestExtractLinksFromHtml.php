<?php

use PHPUnit\Framework\TestCase;

class TestExtractLinksFromHtml extends TestCase {
    /**
     */
    public function testExtractLinksFromHtmlCommon() {
        $html = '
            <html>
                <head>
                    <link rel="css/Text" href="link.css">
                </head>
                <body>
                    <img src="image.png">
                    <a href="anchor.html">Anchor</a>
                    <a href="/relative/image.png">Anchor</a>
                    <form action="https://gh.com/submit.html" method="POST">
                </body>
            </html>
        ';
        $links = extract_links_from_html($html, 'http://site.com/relative/url.html');
        $this->assertEquals($links, [
            'http://site.com/relative/link.css' => [
                'tag' => 'link',
                'rel' => 'css/text',
            ],
            'http://site.com/relative/image.png' => [
                'tag' => 'img',
            ],
            'http://site.com/relative/anchor.html' => [
                'tag' => 'a',
                'anchor' => 'anchor',
            ],
            'https://gh.com/submit.html' => [
                'tag' => 'form',
                'method' => 'post',
            ],
        ]);
    }

    /**
     */
    public function testExtractLinksFromHtmlAnchor() {
        $html = '
            <html>
                <body>
                    <a href="anchor.html"> Привет мир! </a>
                    <a href="anchor2.html"> Àà </a>
                    <a href="anchor3.html"> привет <span>этот</span> мир <strong>эхх</strong> </a>
                </body>
            </html>
        ';
        $links = extract_links_from_html($html, 'http://site.com/relative/url.html');
        $this->assertEquals($links, [
            'http://site.com/relative/anchor.html' => [
                'tag' => 'a',
                'anchor' => 'привет мир',
            ],
            'http://site.com/relative/anchor2.html' => [
                'tag' => 'a',
                'anchor' => 'aa',
            ],
            'http://site.com/relative/anchor3.html' => [
                'tag' => 'a',
                'anchor' => 'привет этот мир эхх',
            ],
        ]);
    }

    /**
     */
    public function testExtractLinksFromHtmlBase() {
        $html = '
            <html>
                <head>
                    <base href="new/" target="_blank">
                </head>
                <body>
                    <a href="anchor.html">Anchor</a>
                </body>
            </html>
        ';
        $links = extract_links_from_html($html, 'http://site.com/relative/url.html');
        $this->assertEquals($links, [
            'http://site.com/relative/new/anchor.html' => [
                'tag' => 'a',
                'anchor' => 'anchor',
            ],
        ]);
        //
        $html = '
            <html>
                <head>
                    <base href="https://gs.com/asd/asd.html" target="_blank">
                </head>
                <body>
                    <a href="anchor.html">Anchor</a>
                </body>
            </html>
        ';
        $links = extract_links_from_html($html, 'http://site.com/relative/url.html');
        $this->assertEquals($links, [
            'https://gs.com/asd/anchor.html' => [
                'tag' => 'a',
                'anchor' => 'anchor',
            ],
        ]);
    }
}

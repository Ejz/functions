<?php

use PHPUnit\Framework\TestCase;

class TestSanitizeHtml extends TestCase {
    /**
     */
    public function testSanitizeHtmlCommon() {
        $html = " foo <hr> bar ";
        $this->assertEquals(trim($html), sanitize_html($html));
        //
        $html = "<script>\n1\n2\n</script> <title> <a> text </a> </title>";
        $this->assertEquals(
            "<script>1\n2</script><title><a>text</a></title>",
            sanitize_html($html)
        );
        //
        $html = "<div>link: <a href=''>link</a></div>";
        $this->assertEquals($html, sanitize_html($html));
        //
        $html = "<title> <a> text <!-- asd \n --> \n 1 </a> </title>";
        $this->assertEquals("<title><a>text 1</a></title>", sanitize_html($html));
        //
        $html = "<title> hello <a href='' > 1 </a> </title>";
        $this->assertEquals("<title>hello <a href=''>1</a></title>", sanitize_html($html));
        //
        $html = "<!DOCTYPE html> <html> <head></head> <body></body> </html>";
        $this->assertEquals("<!DOCTYPE html><html><head></head><body></body></html>", sanitize_html($html));
        //
        $html = "<html><body><p>text <a>link</a> text <a>link</a>. </p></body></html>";
        $this->assertEquals("<html><body><p>text <a>link</a> text <a>link</a>.</p></body></html>", sanitize_html($html));
    }
}

<?php

use PHPUnit\Framework\TestCase;

class TestXpath extends TestCase {
    /**
     */
    public function testXpathCommon() {
        $xml = '<root> <img> 1 </img> </root>';
        [$_] = xpath($xml, '/*', null, ['ignore_fix' => true]);
        $this->assertRegexp('~^\s*<root>\s*<img>\s*1\s*</img>\s*</root>\s*$~', $_);
        //
        $xml = "<root> <a> 1 </a> <b>2</b> </root>";
        list($_) = xpath($xml, '/*');
        $this->assertRegexp("~^\s*<root>\s*<a> 1 </a>\s*<b>2</b>\s*</root>\s*$~", $_);
        $_ = xpath($xml);
        $this->assertRegexp("~^\s*<root>\s*<a> 1 </a>\s*<b>2</b>\s*</root>\s*$~", $_);
        list($_) = xpath($xml, '//a/text()');
        $this->assertEquals(' 1 ', $_);
        //
        $xml = "<root> <a> 1 </a> <b>2</b> </root>";
        list($_) = xpath($xml, '/*');
        $this->assertRegexp("~^\s*<root>\s*<a> 1 </a>\s*<b>2</b>\s*</root>\s*$~", $_);
        $_ = xpath($xml, '/*', null, ['implode' => true]);
        $this->assertRegexp("~^\s*<root>\s*<a> 1 </a>\s*<b>2</b>\s*</root>\s*$~", $_);
        list($_) = xpath($xml, '//a/text()');
        $this->assertEquals(' 1 ', $_);
        //
        $xml = "<root><test> \n </test></root>";
        $_ = xpath($xml);
        $this->assertRegexp("~^\s*<root>\s*<test> \n </test>\s*</root>\s*$~", $_);
        //
        $xml = "<root> <a class='cl1 cl2 cl3'> 1 </a> <b>2</b> </root>";
        list($_) = xpath($xml, '//*[class(cl2)]');
        $this->assertRegexp("~^\s*<a\b[^>]*>.*?</a>\s*$~", $_);
        //
        $xml = "<root> <a>1</a> <b>2</b> <c>3</c> </root>";
        $xml = xpath($xml, '//b', function ($tag) {
            $tag->parentNode->removeChild($tag);
        });
        $this->assertRegexp("~^\s*<root>\s*<a>1</a>\s*<c>3</c>\s*</root>\s*$~", $xml);
        //
        $xml = "<root><a><one>1</one><two>2</two></a></root>";
        $xml = xpath($xml, '//a', function ($tag) {
            $_ = xpath($tag, '//text()');
            $tag->nodeValue = implode(' ', $_);
        });
        $this->assertRegexp("~^\s*<root>\s*<a>1 2</a>\s*</root>\s*$~", $xml);
        //
        $xml = "<root><a><one>1</one><two>2</two></a></root>";
        $xml = xpath($xml, '//a', function ($tag) {
            if($tag->hasChildNodes()) {
                $collector = array();
                foreach ($tag->childNodes as $child)
                    $collector[] = $child;
                for ($i = 0; $i < count($collector); $i++)
                    $tag->parentNode->insertBefore($collector[$i], $tag);
            }
            $tag->parentNode->removeChild($tag);
        });
        $this->assertRegexp("~^\s*<root>\s*<one>1</one>\s*<two>2</two>\s*</root>\s*$~", $xml);
        //
        $xml = '<root> <a>1</a> <b>2</b> <c>3</c> </root>';
        $xml = xpath($xml, '//b', '_xpath_callback_remove');
        $this->assertRegexp("~^\s*<root>\s*<a>1</a>\s*<c>3</c>\s*</root>\s*$~", $xml);
        //
        $xml = '<root><a remove=\'1\'><b>b</b><c remove=\'1\'></c></a></root>';
        $count = 0;
        $xml = xpath($xml, '//*[@remove="1"]', function ($tag) use(& $count) {
            $count += 1;
            $tag->parentNode->removeChild($tag);
        });
        $this->assertRegexp("~^\s*<root/>\s*$~", $xml);
        $this->assertEquals(1, $count);
        //
        $xml = '<root><p>a<br>b</p></root>';
        $texts = xpath($xml, '//p/text()');
        $this->assertEquals('a', $texts[0]);
        $this->assertEquals('b', $texts[1]);
        //
        $xml = <<<END
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>title</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
content
</body>
</html>
END;
        list($text) = xpath($xml, '/html/body/text()');
        $this->assertEquals("content", trim($text));
    }
}

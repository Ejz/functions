<?php

class TestMisc extends PHPUnit_Framework_TestCase {
    public function testMiscEsc() {
        $s = "<&>'\"";
        $this->assertEquals(esc($s), "&lt;&amp;&gt;'\"");
        $this->assertEquals(esc(esc($s), $decode = true), $s);
        $this->assertEquals(fesc($s), "&lt;&amp;&gt;&#039;&quot;");
        $this->assertEquals(fesc(fesc($s), $decode = true), $s);
    }
    public function testMiscTemplate() {
        $tpl = <<<'TEMPLATE'
<html><?=(@ $html)?></html>
TEMPLATE;
        $tmp = rtrim(`mktemp`);
        $this->assertTrue(is_file($tmp));
        file_put_contents($tmp, $tpl);
        $output = template($tmp);
        $this->assertTrue(trim($output) === "<html></html>");
        $output = template($tmp, ['html' => 'test']);
        $this->assertTrue(trim($output) === "<html>test</html>");
        unlink($tmp);
    }
    public function testMiscCurdate() {
        $this->assertTrue(intval(curdate()) === intval(date('Y')));
        $this->assertTrue(intval(curdate(365)) === (intval(date('Y')) + 1));
    }
    public function testMiscNow() {
        $this->assertTrue(intval(now()) === intval(date('Y')));
        $this->assertEquals(count(explode(' ', now())), 2);
        $this->assertTrue(intval(now(3600 * 24 * 365)) === (intval(date('Y')) + 1));
    }
    public function testMiscHost() {
        $this->assertEquals(host("http://example.com"), "example.com");
        $this->assertEquals(host("http://EXAMPLE.COM"), "example.com");
        $this->assertEquals(host("http://example.com/"), "example.com");
        $this->assertEquals(host("http://example.com/ "), "example.com");
        $this->assertEquals(host("//example.com/"), "example.com");
        $this->assertEquals(host("//example.com"), "example.com");
        $this->assertNotEquals(host("http:// example.com/"), "example.com");
        $this->assertNotEquals(host(" http://example.com/"), "example.com");
        $this->assertNotEquals(host("http ://example.com/"), "example.com");
        $this->assertNotEquals(host("http://example . com/"), "example.com");
        $this->assertNotEquals(host("http://example. com/"), "example.com");
        $this->assertNotEquals(host("http://example .com/"), "example.com");
    }
    public function testMiscIsHost() {
        $this->assertTrue(is_host("example.com"));
        $this->assertTrue(is_host("domain"));
        $this->assertTrue(is_host("EXAMPLE.COM"));
        $this->assertTrue(is_host("Example.Com"));
        $this->assertTrue(is_host("10.0.0.1"));
        $this->assertTrue(is_host("127.0.0.1"));
        $this->assertFalse(is_host("site . com"));
        $this->assertFalse(is_host("site. com"));
        $this->assertFalse(is_host("site .com"));
    }
    public function testMiscNsplit() {
        $this->assertEquals(nsplit("one"), array("one"));
        $this->assertEquals(nsplit("
            one
            two
        "), array("one", "two"));
        $this->assertEquals(nsplit("
        "), array());
        $this->assertEquals(nsplit(""), array());
    }
    public function testMiscIsClosure() {
        $closure = function() { ; };
        $this->assertFalse(is_closure('is_closure'));
        $this->assertFalse(is_closure(array($this, 'testIsClosure')));
        $this->assertTrue(is_closure($closure));
        $a = 'closure';
        $this->assertTrue(is_closure($$a));
    }
    public function testMiscIsIp() {
        $this->assertTrue(is_ip('127.0.0.1'));
        $this->assertTrue(is_ip('192.168.0.1'));
        $this->assertTrue(is_ip('1.1.1.1'));
        $this->assertFalse(is_ip('1.2.3.4.5'));
        $this->assertFalse(is_ip('a.b.c.d'));
        $this->assertFalse(is_ip('1,2.1,3.1,4.1,5'));
        $this->assertFalse(is_ip('1000.1.1.1'));
        $this->assertFalse(is_ip('256.256.256.256'));
        $this->assertFalse(is_ip('256.256.256.'));
        $this->assertFalse(is_ip('0.0.0'));
        //
        $this->assertFalse(is_ip('127.0.0.1', $allow_private = false));
        $this->assertFalse(is_ip('192.168.0.1', $allow_private = false));
        $this->assertTrue(is_ip('50.10.10.10', $allow_private = false));
    }
    public function testMiscIsAssoc() {
        $this->assertTrue(is_assoc(array()));
        $this->assertTrue(is_assoc(array('key' => 'value')));
        $this->assertFalse(is_assoc(array('value')));
        $this->assertFalse(is_assoc(array('0' => 'value')));
        $this->assertFalse(is_assoc(array('0' => 'value', 'key' => 'value')));
    }
    public function testMiscIsRegex() {
        $this->assertFalse(is_regex(array()));
        $this->assertFalse(is_regex("~\w"));
        $this->assertTrue(is_regex("~\w~"));
        $this->assertTrue(is_regex("&\w~&"));
    }
    public function testMiscStrReplaceOnce() {
        $str = "one one";
        $this->assertEquals(str_replace_once("one", "two", $str), "two one");
        $this->assertEquals(str_replace_once("three", "two", $str), "one one");
        $this->assertEquals(str_replace_once("", "two", $str), "one one");
        $this->assertEquals(str_replace_once("one", "", $str), " one");
    }
    public function testMiscStrTruncate() {
        $one = "Hello, world!";
        $this->assertEquals(str_truncate($one), $one);
        $this->assertEquals(str_truncate($one, 40), $one);
        $this->assertEquals(str_truncate($one, 5, $center = true), "H...!");
        $this->assertEquals(str_truncate($one, 6, $center = true), "He...!");
        $this->assertEquals(str_truncate($one, 5, $center = false), "He...");
        $this->assertEquals(str_truncate($one, 6, $center = false), "Hel...");
        $this->assertEquals(str_truncate($one, 6, $center = false, '..'), "Hell..");
        $this->assertEquals(str_truncate($one, 0, $center = false, '..'), "H..");
        $this->assertEquals(str_truncate($one, 0, $center = true, '..'), "H..!");
    }
    public function testMiscMtShuffle() {
        //
        $total = 100;
        $collector = array();
        $one = array("1", "2", "3", "4", "5");
        for($i = 0; $i < $total; $i++) {
            $array = $one;
            mt_shuffle($array);
            $collector[] = implode('', $array);
        }
        $this->assertTrue(count(array_unique($collector)) > 10);
        //
        $total = 100;
        $collectorV = array();
        $collectorK = array();
        $one = array(10 => "1", 20 => "2", 30 => "3", 40 => "4", 50 => "5");
        for($i = 0; $i < $total; $i++) {
            $array = $one;
            mt_shuffle($array);
            $collectorV[] = implode('', array_values($array));
            $collectorK[] = implode('', array_keys($array));
        }
        $this->assertTrue(count(array_unique($collectorV)) > 10);
        $this->assertTrue(count(array_unique($collectorK)) === 1);
        //
        // Keys
        //
        $total = 100;
        $collectorV = array();
        $collectorK = array();
        $one = array(
            'number-1' => "1",
            'number-2' => "2",
            'number-3' => "3",
            'number-4' => "4",
            'number-5' => "5"
        );
        for($i = 0; $i < $total; $i++) {
            $array = $one;
            mt_shuffle($array);
            $collectorV[] = implode('', array_values($array));
            $collectorK[] = implode('', array_keys($array));
        }
        $this->assertTrue(count(array_unique($collectorV)) > 10);
        $this->assertTrue(count(array_unique($collectorK)) === 1);
    }
    public function testMiscFileGetExt() {
        $this->assertEquals(file_get_ext("/etc/passwd"), "");
        $this->assertEquals(file_get_ext("/var/log/nginx/"), "");
        $this->assertEquals(file_get_ext("/var/log/nginx/access.log"), "log");
        $this->assertEquals(file_get_ext("/var/log/nginx/access.LOG"), "log");
        $this->assertEquals(file_get_ext("archive.tar.GZ"), "gz");
        $this->assertEquals(file_get_ext("/tmp/tmp.pvoogl4dqa"), "");
        $this->assertEquals(file_get_ext("/tmp/tmp.alpha"), "alpha");
        $this->assertEquals(file_get_ext("/tmp/tmp.1"), "1");
    }
    public function testMiscFileGetName() {
        $this->assertEquals(file_get_name("/etc/passwd"), "passwd");
        $this->assertEquals(file_get_name("/var/log/nginx/"), "");
        $this->assertEquals(file_get_name("/var/log/nginx/access.log"), "access");
        $this->assertEquals(file_get_name("/var/log/nginx/ACCESS.LOG"), "ACCESS");
        $this->assertEquals(file_get_name("/var/archive.tar.gz"), "archive.tar");
    }
    public function testMiscRandFromString() {
        $arr = array(rand_from_string("a"), rand_from_string("b"), rand_from_string("c"));
        $this->assertTrue(count(array_filter($arr, 'is_numeric')) === 3);
        $this->assertTrue(count(array_unique($arr)) === 3);
    }
    public function testMiscGetUserAgent() {
        $ua = get_user_agent();
        $this->assertTrue(is_string($ua) and strlen($ua));
        $one = get_user_agent(null, 'seed');
        $two = get_user_agent(null, 'seed');
        $three = get_user_agent(null, 'double seed');
        $this->assertTrue($one and $two and $three);
        $this->assertTrue($one === $two);
        $this->assertTrue($one != $three);
        //
        $chrome = get_user_agent('chrome');
        $this->assertTrue(is_numeric(stripos($chrome, 'chrome')));
        $msie = get_user_agent('msie');
        $this->assertTrue(is_numeric(stripos($msie, 'msie')));
        //
        $one = get_user_agent('Macintosh', 'seed');
        $two = get_user_agent('Macintosh', 'seed');
        $three = get_user_agent('Macintosh', 'double seed');
        $this->assertTrue($one and $two and $three);
        $this->assertTrue($one === $two);
        $this->assertTrue($one != $three);
    }
    public function testMiscGetTagAttributes() {
        $fesc = fesc($_ = "<&\"'>");
        $tag = "
            <a data-value=1 fesc='{$fesc}' href='/about/' class=\"class\" target=_blank>About</a>
        ";
        $attr = get_tag_attributes($tag);
        $attrHref = get_tag_attributes($tag, 'href');
        $attrNone = get_tag_attributes($tag, 'none');
        $attrFesc = get_tag_attributes($tag, 'fesc');
        $this->assertEquals($attr['href'], "/about/");
        $this->assertEquals($attr['class'], "class");
        $this->assertEquals($attr['target'], "_blank");
        $this->assertEquals($attr['data-value'], "1");
        $this->assertTrue($attrHref ? true : false);
        $this->assertFalse($attrNone ? true : false);
        $this->assertEquals($attrFesc, $_);
    }
    public function testMiscPrepareTagAttributes() {
        $attributes = ["href" => "/link.html?a=1&b=2", "class" => ["_left", "_clearfix"]];
        $prepared = prepare_tag_attributes($attributes);
        $this->assertEquals($prepared, "href=\"/link.html?a=1&amp;b=2\" class=\"_left _clearfix\"");
        $attributes = ["style" => ["margin-top" => "0", "display" => "flex"]];
        $prepared = prepare_tag_attributes($attributes);
        $this->assertEquals($prepared, "style=\"margin-top:0;display:flex;\"");
    }
    public function testMiscRealUrl() {
        $baseRoot = "http://site.com/";
        $baseAsd = "http://site.com/asd";
        $baseAsdSlash = "http://site.com/asd/";
        $baseAsdSlashD = "http://site.com/asd/./";
        $baseAsdSlashDD = "http://site.com/asd/../";
        //
        $this->assertTrue(realurl("http://site.com") === "http://site.com/");
        $this->assertTrue(realurl("http://site.com/") === "http://site.com/");
        $this->assertTrue(realurl("http://site.com/./") === "http://site.com/");
        $this->assertTrue(realurl("http://site.com/asd") === "http://site.com/asd");
        $this->assertTrue(realurl("http://site.com/asd/") === "http://site.com/asd/");
        $this->assertTrue(realurl("http://site.com/../") === "http://site.com/");
        $this->assertTrue(realurl("http://site.com/../../../asd/") === "http://site.com/asd/");
        $this->assertTrue(realurl("http://site.com/123/456/../asd/") === "http://site.com/123/asd/");
        //
        $this->assertTrue(realurl("/", "http://site.com/") === "http://site.com/");
        $this->assertTrue(realurl("/", "http://site.com/asd") === "http://site.com/");
        $this->assertTrue(realurl("/./", "http://site.com/asd") === "http://site.com/");
        $this->assertTrue(realurl("/./../", "http://site.com/asd") === "http://site.com/");
        //
        $this->assertTrue(
            realurl("index.html", "http://site.com/asd/contacts.html")
                ===
            "http://site.com/asd/index.html"
        );
        $this->assertTrue(
            realurl("?q=1", "http://site.com/asd/../contacts.html")
                ===
            "http://site.com/contacts.html?q=1"
        );
        $this->assertTrue(
            realurl("../page?q=1", "http://site.com/asd/path/")
                ===
            "http://site.com/asd/page?q=1"
        );
        //
        $this->assertTrue(realurl("//site.com", 'https://site2.com') === "https://site.com/");
        $this->assertTrue(realurl("//site.com", '//site2.com') === "http://site.com/");
    }
    public function testMiscLatinize() {
        $this->assertTrue(latinize('ÁΓă') === "AGa");
        $this->assertTrue(latinize('ђÜẽ') === "djUe");
        $this->assertTrue(latinize('Màl Śir') === "Mal Sir");
        //
        $this->assertTrue(latinize('привет мир ă', $ru = true) === "privet mir a");
        $this->assertTrue(latinize('щука ямка хрен ă', $ru = true) === "shchuka iamka khren a");
    }
    public function testMiscNormalize() {
        $this->assertTrue(normalize("Hello, world!") === "hello world");
        $this->assertTrue(normalize("|Hello, \n world!", '!|') === "|hello world!");
        $this->assertTrue(normalize("Привет, мир!", '', $ru = true) === "привет мир");
    }
    public function testMiscConfig() {
        $configString = <<<END
[global]
secret = ""

[db-1]
user-1 = user_1
user-2 = user_2

[db-2]
user-2 = user_2
user-3 = user_3
END;
        config('.', parse_ini_string($configString, true));
        $this->assertEquals(config(), config('.'));
        $config = config();
        $this->assertEquals("", $config['global']['secret']);
        $this->assertEquals("user_1", $config['db-1']['user-1']);
        $this->assertEquals("", config('global.secret'));
        $this->assertEquals(array('secret' => ""), config('global'));
        $this->assertEquals(array("user-1" => "user_1", "user-2" => "user_2"), config('db-1'));
        config('global.secret', '1');
        $this->assertEquals("1", config('global.secret'));
        config('global', array('secret' => 2, 'key' => 'value'));
        $this->assertEquals("2", config('global.secret'));
        $this->assertEquals(array('secret' => 2, 'key' => 'value'), config('global'));
        //
        $this->assertEquals(array('db-1' => array('user-1' => 'user_1', 'user-2' => 'user_2'), 'db-2' => array('user-3' => 'user_3', 'user-2' => 'user_2')), config('db-*'));
        $this->assertEquals(array('user-1' => 'user_1', 'user-2' => 'user_2'), config('db-1.user-*'));
        $this->assertEquals(array('user-1' => 'user_1', 'user-2' => 'user_2'), config('db-1.*'));
        $config = config();
        unset($config['global']);
        $this->assertEquals($config, config('db-*.user-*'));
        $this->assertEquals(array('db-1' => array('user-1' => 'user_1')), config('db-*.user-1'));
        //
        config('array.value[]', 'one');
        config('array.value[]', 'two');
        $this->assertEquals(array('one', 'two'), config('array.value'));
    }
}

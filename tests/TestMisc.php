<?php

use PHPUnit\Framework\TestCase;

class TestMisc extends TestCase {
    /**
     */
    public function testMiscEsc() {
        $s = '<&>\'"';
        $this->assertEquals(esc($s), '&lt;&amp;&gt;&#039;&quot;');
        $this->assertEquals(esc(esc($s), $decode = true), $s);
    }

    /**
     */
    public function testMiscCurdate() {
        $this->assertTrue(intval(curdate()) === intval(date('Y')));
        $this->assertTrue(intval(curdate(365)) === (intval(date('Y')) + 1));
    }

    /**
     */
    public function testMiscNow() {
        $this->assertTrue(intval(now()) === intval(date('Y')));
        $this->assertEquals(count(explode(' ', now())), 2);
        $this->assertTrue(intval(now(3600 * 24 * 365)) === (intval(date('Y')) + 1));
    }

    /**
     */
    public function testMiscHost() {
        $this->assertEquals(host('http://example.com'), 'example.com');
        $this->assertEquals(host('http://EXAMPLE.COM'), 'example.com');
        $this->assertEquals(host('http://example.com/'), 'example.com');
        $this->assertEquals(host('http://example.com/ '), 'example.com');
        $this->assertEquals(host('//example.com/'), 'example.com');
        $this->assertEquals(host('//example.com'), 'example.com');
        $this->assertNotEquals(host('http:// example.com/'), 'example.com');
        $this->assertNotEquals(host(' http://example.com/'), 'example.com');
        $this->assertNotEquals(host('http ://example.com/'), 'example.com');
        $this->assertNotEquals(host('http://example . com/'), 'example.com');
        $this->assertNotEquals(host('http://example. com/'), 'example.com');
        $this->assertNotEquals(host('http://example .com/'), 'example.com');
    }

    /**
     */
    public function testMiscIsHost() {
        $this->assertTrue(is_host('example.com'));
        $this->assertTrue(is_host('domain'));
        $this->assertTrue(is_host('EXAMPLE.COM'));
        $this->assertTrue(is_host('Example.Com'));
        $this->assertTrue(is_host('10.0.0.1'));
        $this->assertTrue(is_host('127.0.0.1'));
        $this->assertFalse(is_host('site . com'));
        $this->assertFalse(is_host('site. com'));
        $this->assertFalse(is_host('site .com'));
    }

    /**
     */
    public function testMiscNsplit() {
        $this->assertEquals(nsplit('one'), array('one'));
        $this->assertEquals(nsplit('
            one
            two
        '), array('one', 'two'));
        $this->assertEquals(nsplit('
        '), []);
        $this->assertEquals(nsplit(''), []);
    }

    /**
     */
    public function testMiscIsClosure() {
        $closure = function() { ; };
        $this->assertFalse(is_closure('is_closure'));
        $this->assertFalse(is_closure(array($this, 'testIsClosure')));
        $this->assertTrue(is_closure($closure));
        $a = 'closure';
        $this->assertTrue(is_closure($$a));
    }

    /**
     */
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

    /**
     */
    public function testMiscIsAssoc() {
        $this->assertTrue(is_assoc([]));
        $this->assertTrue(is_assoc(['key' => 'value']));
        $this->assertFalse(is_assoc(['value']));
        $this->assertFalse(is_assoc(['0' => 'value']));
        $this->assertFalse(is_assoc(['0' => 'value', 'key' => 'value']));
    }

    /**
     */
    public function testMiscIsRegex() {
        $this->assertFalse(is_regex([]));
        $this->assertFalse(is_regex('~\w'));
        $this->assertTrue(is_regex('~\w~'));
        $this->assertTrue(is_regex('&\w~&'));
    }

    /**
     */
    public function testMiscStrReplaceOnce() {
        $str = 'one one';
        $this->assertEquals(str_replace_once('one', 'two', $str), 'two one');
        $this->assertEquals(str_replace_once('three', 'two', $str), 'one one');
        $this->assertEquals(str_replace_once('', 'two', $str), 'one one');
        $this->assertEquals(str_replace_once('one', '', $str), ' one');
    }

    /**
     */
    public function testMiscStrTruncate() {
        $one = 'Hello, world!';
        $this->assertEquals(str_truncate($one), $one);
        $this->assertEquals(str_truncate($one, 40), $one);
        $this->assertEquals(str_truncate($one, 5, $center = true), 'H...!');
        $this->assertEquals(str_truncate($one, 6, $center = true), 'He...!');
        $this->assertEquals(str_truncate($one, 5, $center = false), 'He...');
        $this->assertEquals(str_truncate($one, 6, $center = false), 'Hel...');
        $this->assertEquals(str_truncate($one, 6, $center = false, '..'), 'Hell..');
        $this->assertEquals(str_truncate($one, 0, $center = false, '..'), 'H..');
        $this->assertEquals(str_truncate($one, 0, $center = true, '..'), 'H..!');
    }

    /**
     */
    public function testMiscFileGetExt() {
        $this->assertEquals(file_get_ext('/etc/passwd'), '');
        $this->assertEquals(file_get_ext('/var/log/nginx/'), '');
        $this->assertEquals(file_get_ext('/var/log/nginx/access.log'), 'log');
        $this->assertEquals(file_get_ext('/var/log/nginx/access.LOG'), 'log');
        $this->assertEquals(file_get_ext('archive.tar.GZ'), 'gz');
        $this->assertEquals(file_get_ext('/tmp/tmp.pvoogl4dqa'), '');
        $this->assertEquals(file_get_ext('/tmp/tmp.alpha'), 'alpha');
        $this->assertEquals(file_get_ext('/tmp/tmp.1'), '1');
    }

    /**
     */
    public function testMiscFileGetName() {
        $this->assertEquals(file_get_name('/etc/passwd'), 'passwd');
        $this->assertEquals(file_get_name('/var/log/nginx/'), '');
        $this->assertEquals(file_get_name('/var/log/nginx/access.log'), 'access');
        $this->assertEquals(file_get_name('/var/log/nginx/ACCESS.LOG'), 'ACCESS');
        $this->assertEquals(file_get_name('/var/archive.tar.gz'), 'archive.tar');
    }

    /**
     */
    public function testMiscGetTagAttributes() {
        $esc = esc($_ = '<&"\'>');
        $tag = '
            <a data-value=1 esc=\'' . $esc . '\' href=\'/about/\' class="class" target=_blank>About</a>
        ';
        $attr = get_tag_attributes($tag);
        $attrHref = get_tag_attributes($tag, 'href');
        $attrNone = get_tag_attributes($tag, 'none');
        $attrFesc = get_tag_attributes($tag, 'esc');
        $this->assertEquals($attr['href'], '/about/');
        $this->assertEquals($attr['class'], 'class');
        $this->assertEquals($attr['target'], '_blank');
        $this->assertEquals($attr['data-value'], '1');
        $this->assertTrue(!!$attrHref);
        $this->assertFalse(!!$attrNone);
        $this->assertEquals($attrFesc, $_);
    }

    /**
     */
    public function testMiscPrepareTagAttributes() {
        $attributes = ['href' => '/link.html?a=1&b=2', 'class' => ['_left', '_clearfix']];
        $prepared = prepare_tag_attributes($attributes);
        $this->assertEquals($prepared, 'href="/link.html?a=1&amp;b=2" class="_left _clearfix"');
        $attributes = ['style' => ['margin-top' => '0', 'display' => 'flex']];
        $prepared = prepare_tag_attributes($attributes);
        $this->assertEquals($prepared, 'style="margin-top:0;display:flex;"');
    }

    /**
     */
    public function testMiscRealUrl() {
        $baseRoot = 'http://site.com/';
        $baseAsd = 'http://site.com/asd';
        $baseAsdSlash = 'http://site.com/asd/';
        $baseAsdSlashD = 'http://site.com/asd/./';
        $baseAsdSlashDD = 'http://site.com/asd/../';
        //
        $this->assertTrue(realurl('http://site.com') === 'http://site.com/');
        $this->assertTrue(realurl('http://site.com/') === 'http://site.com/');
        $this->assertTrue(realurl('http://site.com/./') === 'http://site.com/');
        $this->assertTrue(realurl('http://site.com/asd') === 'http://site.com/asd');
        $this->assertTrue(realurl('http://site.com/asd/') === 'http://site.com/asd/');
        $this->assertTrue(realurl('http://site.com/../') === 'http://site.com/');
        $this->assertTrue(realurl('http://site.com/../../../asd/') === 'http://site.com/asd/');
        $this->assertTrue(realurl('http://site.com/123/456/../asd/') === 'http://site.com/123/asd/');
        //
        $this->assertTrue(realurl('/', 'http://site.com/') === 'http://site.com/');
        $this->assertTrue(realurl('/', 'http://site.com/asd') === 'http://site.com/');
        $this->assertTrue(realurl('/./', 'http://site.com/asd') === 'http://site.com/');
        $this->assertTrue(realurl('/./../', 'http://site.com/asd') === 'http://site.com/');
        //
        $this->assertTrue(
            realurl('index.html', 'http://site.com/asd/contacts.html')
                ===
            'http://site.com/asd/index.html'
        );
        $this->assertTrue(
            realurl('?q=1', 'http://site.com/asd/../contacts.html')
                ===
            'http://site.com/contacts.html?q=1'
        );
        $this->assertTrue(
            realurl('../page?q=1', 'http://site.com/asd/path/')
                ===
            'http://site.com/asd/page?q=1'
        );
        //
        $this->assertTrue(realurl('//site.com', 'https://site2.com') === 'https://site.com/');
        $this->assertTrue(realurl('//site.com', '//site2.com') === 'http://site.com/');
        //
        $this->assertTrue(realurl('../text.md', 'path/a/new.md') === 'path/text.md');
        $this->assertTrue(realurl('../../text.md', 'path/a/new.md') === 'text.md');
        $this->assertTrue(realurl('../../../text.md', 'path/a/new.md') === '../text.md');
        $this->assertTrue(realurl('a/b', './c') === './a/b');
        $this->assertTrue(realurl('a/b', 'c') === 'a/b');
    }

    /**
     */
    public function testMiscLatinize() {
        $this->assertTrue(latinize('ÁΓă') === 'AGa');
        $this->assertTrue(latinize('ђÜẽ') === 'djUe');
        $this->assertTrue(latinize('Màl Śir') === 'Mal Sir');
        //
        $this->assertTrue(latinize('привет мир ă', $ru = true) === 'privet mir a');
        $this->assertTrue(latinize('щука ямка хрен ă', $ru = true) === 'shchuka iamka khren a');
    }

    /**
     */
    public function testMiscNormalize() {
        $this->assertTrue(normalize('Hello, world!') === 'hello world');
        $this->assertTrue(normalize("|Hello, \n world!", '!|') === '|hello world!');
        $this->assertTrue(normalize('Привет, мир!', '', $ru = true) === 'привет мир');
    }
    
    /**
     */
    public function testMiscReadableToVariable() {
        define($c = md5(microtime(true)), 'bla');
        $this->assertTrue(readable_to_variable('true') === true);
        $this->assertTrue(readable_to_variable($c) === 'bla');
        $this->assertTrue(readable_to_variable('[]') === []);
        $this->assertTrue(readable_to_variable('[k=>v]') === array('k' => 'v'));
        $this->assertTrue(readable_to_variable('[v1,v2]') === array('v1', 'v2'));
        $this->assertTrue(readable_to_variable("[v1, {$c}]") === array('v1', 'bla'));
    }

    /**
     */
    public function testMiscSuffixDomains() {
        $this->assertEquals([
            'suffix' => 'com',
            'suffix_match' => 'com',
            'domain' => 'site',
            'tld' => 'com',
        ], get_domain_info('site.com'));
        //
        $this->assertEquals([
            'suffix' => 'com',
            'suffix_match' => 'com',
            'domain' => 'site',
            'tld' => 'com',
            'subdomain' => '1.www',
        ], get_domain_info('1.www.site.com'));
        //
        $this->assertEquals([], get_domain_info('go.ci'));
        //
        $this->assertEquals([
            'suffix' => 'go.ci',
            'suffix_match' => 'go.ci',
            'domain' => 'www',
            'tld' => 'ci',
        ], get_domain_info('www.go.ci'));
        //
        $this->assertEquals([
            'suffix' => 'ck',
            'suffix_match' => 'ck',
            'domain' => 'www',
            'tld' => 'ck',
        ], get_domain_info('www.ck'));
        //
        $this->assertEquals([
            'suffix' => 'ck',
            'suffix_match' => 'ck',
            'domain' => 'www',
            'tld' => 'ck',
            'subdomain' => '1.2',
        ], get_domain_info('1.2.www.ck'));
        //
        $this->assertEquals([], get_domain_info('www1.ck'));
        $this->assertEquals([
            'suffix' => '*.ck',
            'suffix_match' => 'www1.ck',
            'domain' => 'site',
            'tld' => 'ck',
        ], get_domain_info('site.www1.ck'));
        //
        $this->assertEquals([
            'suffix' => '*.bd',
            'suffix_match' => 'mil.bd',
            'domain' => 'army',
            'tld' => 'bd',
        ], get_domain_info('army.mil.bd'));
        $this->assertEquals('go.www', get_domain_info('go.www.army.mil.bd')['subdomain']);
        //
        $this->assertTrue(is_same_suffix_domains('www.gole', 'www.gole'));
        $this->assertTrue(is_same_suffix_domains('www.google.com', 'google.com'));
        $this->assertTrue(is_same_suffix_domains('google.com', 'www.google.com'));
        $this->assertFalse(is_same_suffix_domains('oogle.com', 'google.com'));
        $this->assertTrue(is_same_suffix_domains('army.mil.bd', 'www.go.army.mil.bd'));
    }
}

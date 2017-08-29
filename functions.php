<?php

define('SQL_FORMAT_DATE', 'Y-m-d');
define('SQL_FORMAT_DATETIME', 'Y-m-d H:i:s');
define('EXPECT_FUNCTION_ERR_MSG', "Invalid type: expected %expected, but given %given. Backtrace: %debug");
define('LOG_FUNCTION_CONSOLE_FORMAT', "[%type] %now ~ %msg");
define('GETOPTS_FUNCTION_INVALID', "Invalid argument %arg!");
define('GETOPTS_FUNCTION_UNKNOWN', "Unknown argument %arg!");
define('GETOPTS_FUNCTION_NOVALUE', "No value for argument %arg!");
define('GETOPTS_FUNCTION_VALUE_EXPECTED', "Expected value for %arg, given another argument %given!");
define('GETOPTS_FUNCTION_EXPECT_NOVALUE', "Expect no value for argument %arg!");

/**
 * Encode/decode HTML chars in given string: `>`, `<` and `&`. 
 * Use this function to escape HTML tags content.
 *
 * ```php
 * $s = esc("HTML: <>&");
 * // $s => "HTML: &lt;&gt;&amp;"
 * $s = esc($s, $decode = true);
 * // $s => "HTML: <>&"
 * ```
 */
function esc($string, $decode = false) {
    return call_user_func(
        $decode ? 'html_entity_decode' : 'htmlspecialchars',
        $string,
        ENT_NOQUOTES
    );
}

/**
 * Encode/decode HTML chars in given string: `>`, `<`, `&`, `'` and `"`. 
 * Use this function to escape HTML tags atrribute values.
 *
 * ```php
 * $s = fesc("HTML: <>&, '\"");
 * // $s => "HTML: &lt;&gt;&amp;, &#039;&quot;"
 * $s = esc($s, $decode = true);
 * // $s => "HTML: <>&, '\""
 * ```
 */
function fesc($string, $decode = false) {
    return call_user_func(
        $decode ? 'html_entity_decode' : 'htmlspecialchars',
        $string,
        ENT_QUOTES
    );
}

/**
 * Native PHP templating engine.
 *
 * ```
 * string template(string $file, array $vars = array());
 * ```
 *
 * ```html
 * <!-- test.tpl -->
 * <html>
 * <head>
 *     <title><?=$title?></title>
 * </head>
 * <body>
 *     <?=$body?>
 * </body>
 * </html>
 * ```
 *
 * ```php
 * echo template("test.tpl", [
 *     "title" => "Test Title",
 *     "body" => "<h1>Hello!</h1>",
 * ]);
 * ```
 *
 * Output:
 *
 * ```html
 * <html>
 * <head>
 *     <title>Test Title</title>
 * </head>
 * <body>
 *     <h1>Hello!</h1>
 * </body>
 * </html>
 * ```
 */
function template() {
    if (func_num_args() > 1)
        extract(func_get_arg(1));
    ob_start();
    include(func_get_arg(0));
    return ob_get_clean();
}

/**
 * Validate a hostname (an IP address or domain name).
 *
 * ```
 * bool is_host(string $host);
 * ```
 *
 * ```php
 * $bool = is_host("github.com");
 * // $bool => true
 * ```
 */
function is_host($host) {
    return (
        preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $host)
            and
        strlen($host) <= 253
            and
        preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $host)
    );
}

/**
 * Get hostname from URL.
 *
 * ```
 * string host(string $url);
 * ```
 *
 * ```php
 * $host = host("https://github.com/");
 * // $host => "github.com"
 * ```
 */
function host($url) {
    $host = strtolower(parse_url($url, PHP_URL_HOST));
    return is_host($host) ? $host : '';
}

/**
 * Get current date in SQL format. Can shift current day using first argument.
 *
 * ```
 * string curdate(int $shift_days = 0);
 * ```
 *
 * ```php
 * $today = curdate();
 * // $today => "2017-08-17"
 * $yesterday = curdate(-1);
 * // $yesterday => "2017-08-16"
 * ```
 */
function curdate($shift_days = 0) {
    return date(SQL_FORMAT_DATE, time() + ($shift_days * 24 * 3600));
}

/**
 * Get current time is SQL format. Can shift current time using first argument.
 *
 * ```
 * string now(int $shift_seconds = 0);
 * ```
 *
 * ```php
 * $now = now();
 * // $now => "2017-08-17 11:04:31"
 * $min_ago = now(-60);
 * // $min_ago => "2017-08-17 11:03:31"
 * ```
 */
function now($shift_seconds = 0) {
    return date(SQL_FORMAT_DATETIME, time() + $shift_seconds);
}

/**
 * Split line by line given string. Each line is trimmed, empty ones are filtered out.
 *
 * ```
 * array nsplit(string $string);
 * ```
 */
function nsplit($string) {
    $string = str_replace("\r", "\n", $string);
    $string = explode("\n", $string);
    $string = array_map('trim', $string);
    $string = array_filter($string);
    return array_values($string);
}

/**
 * Return whether or not the provided object is callable.
 *
 * ```
 * bool is_closure(object $object);
 * ```
 *
 * ```php
 * $bool = is_closure(function() { ; });
 * // $bool => true
 * ```
 */
function is_closure($object) {
    return (is_callable($object) and is_object($object));
}

/**
 * Whether or not provided IP is valid IP.
 *
 * ```
 * bool is_ip(string $ip, bool $allow_private = true);
 * ```
 *
 * ```php
 * $ip = "127.0.0.1";
 * $bool = is_ip($ip);
 * // $bool => true
 * $bool = is_ip($ip, $allow_private = false);
 * // $bool => false
 * ```
 */
function is_ip($ip, $allow_private = true) {
    return (bool)(filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        $allow_private ? 0 : (FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
    ));
}

/**
 * Validate associative array.
 *
 * ```
 * bool is_assoc(array $array);
 * ```
 *
 * ```php
 * $bool = is_assoc([]);
 * // $bool => true
 * ```
 *
 * ```php
 * $bool = is_assoc([1, 2]);
 * // $bool => false
 * ```
 *
 * ```php
 * $bool = is_assoc(["key" => "value"]);
 * // $bool => true
 * ```
 */
function is_assoc($array) {
    if (!is_array($array)) return false;
    $count0 = count($array);
    $count1 = count(array_filter(array_keys($array), 'is_string'));
    return ($count0 === $count1);
}

/**
 * Validate regular expression.
 *
 * ```
 * bool is_regex(string $regex);
 * ```
 *
 * ```php
 * $bool = is_regex("invalid");
 * // $bool => false
 * ```
 *
 * ```php
 * $bool = is_regex("~\w~");
 * // $bool => true
 * ```
 */
function is_regex($regex) {
    if (is_numeric($regex)) return false;
    if (!is_string($regex)) return false;
    return (@ (preg_match($regex . '', '') !== false));
}

/**
 * String replace. Replace is applied only once.
 *
 * ```
 * string str_replace_once(string $needle, string $replace, string $haystack);
 * ```
 *
 * ```php
 * $str = str_replace_once("foo", "bar", "foo foo");
 * // $str => "bar foo"
 * ```
 */
function str_replace_once($needle, $replace, $haystack) {
    @ $pos = strpos($haystack, $needle);
    if ($pos === false) return $haystack;
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}

/**
 * Truncate string to certain length (be default 40 chars).
 *
 * ```
 * string str_truncate(string $string, int $length = 40, bool $center = false, string $replacer = '...');
 * ```
 *
 * ```php
 * $str = str_truncate("Hello, world!", 5);
 * // $str => "He..."
 * ```
 *
 * ```php
 * $str = str_truncate("Hello, world!", 5, $center = true);
 * // $str => "H...!"
 * ```
 */
function str_truncate($string, $length = 40, $center = false, $replacer = '...') {
    $l = mb_strlen($replacer);
    if ($center and $length < (2 + $l)) $length = (2 + $l);
    if (!$center and $length < (1 + $l)) $length = (1 + $l);
    if ($center and mb_strlen($string) > $length) {
        $length -= $l;
        $begin = ceil($length / 2);
        $end = $length - $begin;
        return mb_substr($string, 0, $begin) . $replacer . mb_substr($string, - $end);
    } elseif (!$center and mb_strlen($string) > $length) {
        $length -= $l;
        $begin = $length;
        return mb_substr($string, 0, $begin) . $replacer;
    } else return $string;
}

/**
 * Shuffle an array using `mt_rand()`. Can use seed for remembering randomize.
 *
 * ```
 * mt_shuffle(array & $array, string|int|null $seed = null);
 * ```
 *
 * ```php
 * $arr = ["one", "two", "three"];
 * mt_shuffle($arr);
 * // $arr => ["two", "three", "one"]
 * ```
 */
function mt_shuffle(& $array, $seed = null) {
    $keys = array_keys($array);
    $n = func_num_args();
    _expect($seed, 'string|int|null');
    $seed = is_null($seed) ? null : (string)($seed);
    for ($i = count($array) - 1; $i > 0; $i--) {
        if (!is_null($seed)) {
            $j = rand_from_string($seed) % ($i + 1);
            $seed .= ($j . '');
        } else $j = mt_rand(0, $i);
        if ($i != $j) {
            $_ = $array[$keys[$i]];
            $array[$keys[$i]] = $array[$keys[$j]];
            $array[$keys[$j]] = $_;
        }
    }
}

/**
 * Get file extension.
 *
 * ```
 * string file_get_ext(string $file);
 * ```
 *
 * ```php
 * $ext = file_get_ext("image.PNG");
 * // $ext => "png"
 * ```
 *
 * ```php
 * $ext = file_get_ext("archive.tar.gz");
 * // $ext => "gz"
 * ```
 *
 * ```php
 * $ext = file_get_ext("/etc/passwd");
 * // $ext => ""
 * ```
 *
 * ```php
 * $ext = file_get_ext("/var/www/");
 * // $ext => ""
 * ```
 */
function file_get_ext($file) {
    preg_match('~\.([a-z0-9A-Z]{1,5})$~', $file, $match);
    if (!$match) return '';
    return strtolower($match[1]);
}

/**
 * Get file name (without extension).
 *
 * ```
 * string file_get_name(string $file);
 * ```
 *
 * ```php
 * $name = file_get_name("image.png");
 * // $name => "image"
 * ```
 *
 * ```php
 * $name = file_get_name("archive.tar.gz");
 * // $name => "archive.tar"
 * ```
 *
 * ```php
 * $name = file_get_name("/etc/passwd");
 * // $name => "passwd"
 * ```
 *
 * ```php
 * $name = file_get_name("/var/www/");
 * // $name => ""
 * ```
 */
function file_get_name($file) {
    if (substr($file, -1) === '/')
        return '';
    $file = basename($file);
    return preg_replace('~\.([a-z0-9A-Z]{1,5})$~', '', $file);
}

/**
 * Get random integer from string.
 *
 * ```
 * int rand_from_string(string $string);
 * ```
 *
 * ```php
 * $int = rand_from_string("one");
 * // $int => 975299411
 * ```
 *
 * ```php
 * $int = rand_from_string("two");
 * // $int => 897156455
 * ```
 *
 * ```php
 * $int = rand_from_string("one");
 * // $int => 975299411
 * ```
 */
function rand_from_string($string) {
    $max = mt_getrandmax();
    $int = md5((string)($string));
    $int = preg_replace('/[^0-9]/', '', $int);
    $int = substr($int, 0, strlen($max . '') - 1);
    return intval($int);
}

/**
 * Randomly get User agent string.
 *
 * ```
 * string get_user_agent(string|null $filter = null, string|int|null $seed = null);
 * ```
 *
 * ```php
 * $ua = get_user_agent();
 * // $ua => "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)"
 * ```
 *
 * ```php
 * $ua = get_user_agent("invalid filter");
 * // $ua => ""
 * ```
 *
 * ```php
 * $ua = get_user_agent("opera");
 * // $ua => "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; de) Opera 11.51"
 * ```
 *
 * ```php
 * var_dump(get_user_agent("Mac OS X", "seed") === get_user_agent("Mac OS X", "seed"));
 * // => bool(true)
 * ```
 */
function get_user_agent($filter = null, $seed = null) {
    _expect($filter, 'string|null');
    _expect($seed, 'string|int|null');
    $list = __DIR__ . '/ua.list.txt';
    $list = nsplit(file_get_contents($list));
    if (!is_null($filter)) {
        $is_regex = is_regex($filter);
        $list = array_values(array_filter($list, function ($line) use ($filter, $is_regex) {
            if ($is_regex) return preg_match($filter, $line);
            return (stripos($line, $filter) !== false);
        }));
    }
    if (!$list) return '';
    if (!is_null($seed))
        $rand = rand_from_string((string)($seed)) % count($list);
    else $rand = mt_rand(0, count($list) - 1);
    return $list[$rand];
}

/**
 * Get tag attributes. Returns list. 
 * If second argument is not null, returns value of this argument 
 * (or null if no such argument).
 *
 * ```
 * array|string|null get_user_agent(string|DOMNode $tag, string|null $attr = null);
 * ```
 *
 * ```php
 * $tag = "<a href='/link.html?a=1&amp;b=2'>";
 * $attrs = get_tag_attributes($tag);
 * // $attrs => ["href" => "/link.html?a=1&b=2"]
 * $attr = get_tag_attributes($tag, 'href');
 * // $attr => "/link.html?a=1&b=2"
 * $attr = get_tag_attributes($tag, '_target');
 * // $attr => null
 * ```
 */
function get_tag_attributes($tag, $attr = null) {
    _expect($tag, 'string|DOMNode');
    _expect($attr, 'string|null');
    if ($tag instanceof DOMNode) {
        $doc = $tag->ownerDocument;
        $tag = $doc->saveXML($tag);
    }
    $tag = trim($tag);
    $tag = preg_replace('~^(<\w+\b([^>]*)>).*$~is', '$2', $tag);
    preg_match_all('~\b(?P<attr>[\w-]+)=([\'"]?)(?P<value>.*?)\2(?=\s|>|$)~', $tag, $matches, PREG_SET_ORDER);
    $collector = array();
    foreach ($matches as $match) {
        $collector[strtolower($match['attr'])] = html_entity_decode($match['value'], ENT_QUOTES);
    }
    if (!is_null($attr)) {
        return array_key_exists($attr, $collector) ? $collector[$attr] : null;
    }
    return $collector;
}

/**
 * Prepare attributes for outputing in HTML tag.
 *
 * ```
 * string prepare_tag_attributes(array $attributes);
 * ```
 *
 * ```php
 * $attributes = ["href" => "/link.html?a=1&b=2", "class" => ["_left", "_clearfix"]];
 * $prepared = prepare_tag_attributes($attributes);
 * // $prepared => 'href="/link.html?a=1&amp;b=2" class="_left _clearfix"'
 * ```
 *
 * ```php
 * $attributes = ["style" => ["margin-top" => "0", "display" => "flex"]];
 * $prepared = prepare_tag_attributes($attributes);
 * // $prepared => "style='margin-top:0;display:flex;'"
 * ```
 */
function prepare_tag_attributes($attributes) {
    _expect($attributes, 'array');
    $collector = array();
    foreach ($attributes as $k => $v) {
        if (!$k or (!$v and $v !== "0")) continue;
        if (is_assoc($v)) {
            $_collector = array();
            foreach ($v as $_k => $_v) {
                if (!$_k or (!$_v and $_v !== "0")) continue;
                $_collector[] = sprintf('%s:%s', fesc($_k), fesc($_v));
            }
            $v = implode(';', $_collector) . ($_collector ? ';' : '');
        } elseif (is_array($v)) {
            $v = implode(' ', array_values(array_filter($v)));
        }
        if (!$v and $v !== "0") continue;
        $collector[] = sprintf('%s="%s"', $k, fesc($v));
    }
    return implode(' ', $collector);
}

/**
 * Get absolute URL, also lead URL to more canonical form.
 *
 * ```
 * string|null realurl(string $url, string $absolute = '');
 * ```
 *
 * ```php
 * $url = realurl("/link.html", "http://site.com/");
 * // $url => "http://site.com/link.html"
 * ```
 *
 * ```php
 * $url = realurl("http://site.com/archive/2014/../link.html");
 * // $url => "http://site.com/archive/link.html"
 * ```
 *
 * ```php
 * $url = realurl("../home.html", "http://site.com/archive/link.html");
 * // $url => "http://site.com/home.html"
 * ```
 */
function realurl($url, $absolute = '') {
    if (!host($absolute) and !host($url)) return null;
    if (strpos($url, '#') === 0) return null;
    if (strpos($url, 'javascript:') === 0) return null;
    if (strpos($url, 'mailto:') === 0) return null;
    if (strpos($url, 'skype:') === 0) return null;
    if (strpos($url, 'data:') === 0) return null;
    if (!parse_url($url, PHP_URL_SCHEME) and host($absolute) and host($url))
        $url = (parse_url($absolute, PHP_URL_SCHEME) ?: 'http') . ':' . $url;
    $normalize = function ($url) {
        $parse = parse_url($url);
        if (!$parse) return null;
        $url = substr($url, strlen("{$parse['scheme']}://{$parse['host']}"));
        if (!$url) $url = '/';
        do {
            $old = $url;
            $url = preg_replace('~/+~', '/', $url);
            $url = preg_replace('~/\./~', '/', $url);
            $url = preg_replace('~/[^/]+/\.\./~', '/', $url);
            $url = preg_replace('~^/\.\./~', '/', $url);
            $url = preg_replace('~\?+$~', '', $url);
        } while ($old != $url);
        return "{$parse['scheme']}://{$parse['host']}{$url}";
    };
    if (host($url)) return $normalize($url);
    if (strpos($url, '/') === 0)
        return $normalize(preg_replace('~(?<!/)/(?!/).*$~', '', $absolute) . $url);
    if (strpos($url, '?') === 0)
        return $normalize(preg_replace('~\?.*$~', '', $absolute) . $url);
    return $normalize(preg_replace('~/[^/]+$~', '/', $absolute) . $url);
}

function xpath_callback_remove($tag) {
    $tag->parentNode->removeChild($tag);
}

function xpath_callback_unwrap($tag) {
    if ($tag->hasChildNodes()) {
        $collector = array();
        foreach ($tag->childNodes as $child)
            $collector[] = $child;
        for ($i = 0; $i < count($collector); $i++)
            $tag->parentNode->insertBefore($collector[$i], $tag);
    }
    $tag->parentNode->removeChild($tag);
}

/**
 * Wrapper around [DOMXPath](http://php.net/manual/en/class.domxpath.php). 
 * Accepts XPath queries for extracting tags and callback function for tag manipulating.
 *
 * ```
 * array|string xpath(
 *     string|DOMNode $xml,
 *     string $query = '/*',
 *     callable|int|null $callback = null,
 *     array $flags = []
 * );
 * ```
 *
 * ```php
 * $content = file_get_contents("http://github.com/");
 * $metas = xpath($content, "//meta");
 * print_r($metas);
 * ```
 *
 * Output:
 *
 * ```
 * Array
 * (
 *     [0] => <meta charset="utf-8"/>
 *     [1] => <meta name="viewport" content="width=device-width"/>
 *     [2] => <meta property="og:url" content="https://github.com"/>
 *     [3] => <meta name="pjax-timeout" content="1000"/>
 *     [4] => <meta name="theme-color" content="#1e2327"/>
 * )
 * ```
 *
 * For more examples, please, refer to [xpath.md](docs/xpath.md).
 * 
 */
function xpath($xml, $query = '/*', $callback = null, $flags = array()) {
    _expect($xml, 'string|DOMNode');
    _expect($query, 'string');
    _expect($callback, 'callable|int|null');
    _expect($flags, 'assoc');
    $flags = $flags + [
        'preserve_white_space' => false,
        'ignore_fix' => false,
    ];
    if (is_string($xml) and !$flags['ignore_fix']) {
        // FIX HTML TO BE COMPATIBLE WITH XML
        $tags = 'area|base|br|col|command|embed|hr|img|input|keygen|link|meta|param|source|track|wbr';
        $xml = preg_replace_callback("~<({$tags})\b[^>]*>~", function ($m) {
            $last = mb_substr($m[0], -2);
            if ($last != "/>") return rtrim(mb_substr($m[0], 0, -1)) . ' />';
            return $m[0];
        }, $xml);
        $xml = preg_replace_callback('~<html\b[^>]*>~', function ($m) {
            $attrs = get_tag_attributes($m[0]);
            if (!isset($attrs['xmlns'])) return $m[0];
            unset($attrs['xmlns']);
            $attrs = prepare_tag_attributes($attrs);
            $attrs = $attrs ? " {$attrs}" : '';
            return "<html{$attrs}>";
        }, $xml);
    }
    $query = preg_replace(
        '~class\((?P<class>.*?)\)~i',
        'contains(concat(" ",normalize-space(@class)," ")," $1 ")',
        $query
    );
    $query = preg_replace(
        '~lower-case\((?P<lower>.*?)\)~i',
        'translate($1,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")',
        $query
    );
    if ($xml instanceof DOMNode) {
        $doc = $xml->ownerDocument;
        $xml = $doc->saveXML($xml);
    }
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = $flags['preserve_white_space'];
    $doc->formatOutput = true;
    libxml_use_internal_errors(true);
    $ret = $doc->loadXML($xml);
    if (!$ret) {
        $doc->loadHTML('<?xml encoding="UTF-8">' . $xml);
    }
    $doc->normalizeDocument();
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);
    $tags = $xpath->query($query);
    if (!($tags instanceof DOMNodeList)) {
        _warn(__FUNCTION__ . ": Invalid query ~ {$query}");
        return array();
    }
    if (is_int($callback)) {
        if ($callback < 0) {
            $sum = 0;
            foreach ($tags as $tag) $sum += 1;
            $callback = $sum + $callback;
            if ($callback < 0 or $callback > $sum - 1)
                return '';
        }
        $i = 0;
        foreach ($tags as $tag) {
            if ($i === $callback)
                return $doc->saveXML($tag);
            $i += 1;
        }
        _err(__FUNCTION__ . ': Critical error!');
    }
    if (is_callable($callback)) {
        $callback = function ($tag) use ($callback) {
            $_ = $tag;
            while (isset($_->parentNode))
                $_ = $_->parentNode;
            if ($_ instanceof DOMDocument)
                $callback($tag);
        };
        $collector = array();
        foreach ($tags as $tag) $collector[] = $tag;
        $tags = $collector;
        for ($i = 0, $len = count($tags); $i < $len; $i++)
            $callback($tags[$i]);
        return $doc->saveXML($doc->documentElement);
    }
    $return = array();
    foreach ($tags as $tag)
        $return[] = $doc->saveXML($tag);
    return func_num_args() === 1 ? implode('', $return) : $return;
}

/**
 * All-in-one cURL function with multi threading support.
 *
 * ```
 * array|string curl(array|string $urls, array $settings = []);
 * ```
 *
 * ```php
 * $content = curl("http://github.com/");
 * preg_match("~<title>(.*?)</title>~", $content, $title);
 * echo $title[1];
 * // => "The world&#39;s leading software development platform · GitHub"
 * ```
 *
 * For more examples, please, refer to [curl.md](docs/curl.md).
 */
function curl($urls, $settings = array()) {
    $first = func_get_arg(0);
    $return = array();
    if (is_string($urls))
        $urls = array($urls);
    _expect($urls, 'array');
    $urls = array_values(array_filter($urls, 'is_string'));
    $urls = array_values(array_unique($urls));
    $sett = $settings + [
        'modify_content' => null,
        'retry' => 0,
        'verbose' => false,
        'threads' => 3,
        'sleep' => 5,
        'delay' => 0,
        'format' => (is_string($first) ? 'simple' : 'array'),
        'checker' => null,
    ];
    if (is_numeric($sett['checker']))
        $sett['checker'] = array(intval($sett['checker']));
    if (!is_callable($sett['checker']) and is_array($_ = $sett['checker']))
        $sett['checker'] = function ($url, $ch) use ($_) {
            $info = curl_getinfo($ch);
            $code = intval($info['http_code']);
            return in_array($code, array_map('intval', $_));
        };
    $handleReturn = function (& $return) use ($sett) {
        $fail = array();
        foreach (array_keys($return) as $key) {
            $value = & $return[$key];
            if (!is_array($value) or !array_key_exists('ch', $value) or !is_resource($value['ch']))
                continue;
            $ch = $value['ch'];
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            // if ($error and in_array($errno, $sett['ignore_errors'])) $error = "";
            if ($error or ($sett['checker'] and !($_ = $sett['checker']($value['url'], $ch)))) {
                unset($return[$key]);
                curl_close($ch);
                $fail[$key] = $value['url'];
                if ($sett['verbose']) {
                    _warn("{$value['url']} .. ERR!" . ($error ? " ({$error})" : '') . ($_ ? " ({$_})" : ''));
                }
                continue;
            }
            $info = curl_getinfo($ch);
            $content = curl_multi_getcontent($ch);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            if ($sett['verbose'] and $value['url'] === $info['url'])
                _log("{$value['url']} .. OK!");
            elseif ($sett['verbose'])
                _log("{$value['url']} .. {$info['url']} .. OK!");
            if (intval($headerSize) > 0) {
                $header = substr($content, 0, $headerSize);
                $content = substr($content, $headerSize);
                $headers = array();
                $_ = explode("\r\n\r\n", $header);
                for ($index = 0; $index < count($_) - 1; $index++)
                    foreach (explode("\r\n", $_[$index]) as $i => $line)
                        if ($i === 0) {
                            $line = explode(' ', $line);
                            $headers[$index]['http-code'] = $line[1];
                        } else {
                            $line = explode(': ', $line, 2);
                            if (count($line) != 2) continue;
                            list($k, $v) = $line;
                            $headers[$index][strtolower($k)] = $v;
                        }
            } else $header = '';
            $return[$key]['content'] = $content;
            $return[$key]['header'] = $header;
            if ($sett['modify_content'] and is_callable($sett['modify_content']))
                $return[$key]['content'] = $sett['modify_content']($value['url'], $content);
            $return[$key]['info'] = $info;
            if (isset($headers)) $return[$key]['headers'] = $headers;
            unset($value['ch']);
            curl_close($ch);
        }
        return $fail;
    };
    $getCH = function ($url, $settings) {
        $ch = curl_init();
        $opts = array();
        $setopt = function ($arr) use (& $ch, & $opts) {
            $opts = array_replace($opts, $arr);
            curl_setopt_array($ch, $arr);
        };
        $setopt(array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_USERAGENT => get_user_agent(),
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HEADER => true
        ));
        $acceptCallable = array(
            CURLOPT_HEADERFUNCTION,
            CURLOPT_PROGRESSFUNCTION,
            CURLOPT_READFUNCTION,
            CURLOPT_WRITEFUNCTION
        );
        if (defined("CURLOPT_PASSWDFUNCTION"))
            $acceptCallable[] = CURLOPT_PASSWDFUNCTION;
        if (is_string($url) and host($url))
            $setopt(array(CURLOPT_URL => $url));
        $constants = array_keys(get_defined_constants());
        $constantsStrings = array_values(array_filter($constants, function ($constant) {
            return strpos($constant, 'CURLOPT_') === 0;
        }));
        $constantsValues = array_map('constant', $constantsStrings);
        foreach ($settings as $key => $value) {
            if (in_array($key, $constantsStrings))
                $key = constant($key);
            if (!in_array($key, $constantsValues)) continue;
            if (is_callable($value) and !in_array($key, $acceptCallable))
                $value = $value($url);
            $setopt(array($key => $value));
        }
        if (isset($opts[CURLOPT_URL]) and host($opts[CURLOPT_URL]))
            return $ch;
        return null;
    };
    do {
        $fails = array();
        while ($urls) {
            if ($sett['threads'] == 1 or count($urls) == 1) {
                $single = curl_init();
                $multi = null;
            } else {
                $single = null;
                $multi = curl_multi_init();
            }
            for ($i = 0; $i < $sett['threads'] and $urls; $i++) {
                $key = key($urls);
                $ch = $getCH($urls[$key], $settings);
                if (is_null($ch)) {
                    unset($urls[$key]);
                    $i--;
                    continue;
                }
                $return[$key] = array(
                    'url' => $urls[$key],
                    'ch' => $ch
                );
                if ($multi)
                    curl_multi_add_handle($multi, $ch);
                else $single = $ch;
                unset($urls[$key]);
            }
            if ($multi) {
                do {
                    curl_multi_exec($multi, $running);
                    usleep(200000);
                } while ($running > 0);
                curl_multi_close($multi);
            } else {
                curl_exec($single);
            }
            $fails[] = $handleReturn($return);
            if ($urls and $sett['delay']) sleep($sett['delay']);
        }
        foreach ($fails as $fail)
            foreach ($fail as $k => $v)
                $urls[$k] = $v;
    } while ($urls and $sett['retry']-- and sleep($sett['sleep']) === 0);
    if ($sett['format'] === "simple") {
        return implode("\n\n", array_values(array_column($return, 'content')));
    } elseif ($sett['format'] === "array") {
        return array_column($return, 'content', 'url');
    } elseif ($sett['format'] === "complex") {
        return array_column($return, null, 'url');
    } else _err(__FUNCTION__ . ': Unknown return format!');
}

/**
 * Get options from command line. In case of error returns error string.
 *
 * ```
 * array|string getopts(array $opts, array|null $argv = null);
 * ```
 *
 * ```php
 * $opts = getopts([
 *     'a' => false,     // short, no value
 *     'b' => true,      // short, with value
 *     'help' => false,  // long, no value
 *     'filter' => true, // long, with value
 * ], explode(' ', './script.sh -ab1 arg --help --filter=value'));
 * var_dump($opts);
 * ```
 *
 * Output:
 *
 * ```
 * array(6) {
 *   [0] =>
 *   string(11) "./script.sh"
 *   'a' =>
 *   bool(true)
 *   'b' =>
 *   string(1) "1"
 *   [1] =>
 *   string(3) "arg"
 *   'help' =>
 *   bool(true)
 *   'filter' =>
 *   string(5) "value"
 * }
 * ```
 *
 * For more examples, please, refer to [getopts.md](docs/getopts.md).
 */
function getopts($opts, $argv = null) {
    _expect($opts, 'assoc');
    _expect($argv, 'array|null');
    $argv = is_null($argv) ? $_SERVER['argv'] : $argv;
    $collect = array();
    $next = null;
    $raw = false;
    foreach ($opts as & $opt) {
        if (is_bool($opt)) $opt = ['value' => $opt];
        $opt = $opt + [
            'value' => false,
            'multiple' => false,
        ];
    }
    for ($i = 0; $i < count($argv); $i++) {
        $arg = $argv[$i];
        if ($arg === '--') {
            $raw = true;
            continue;
        }
        if ($next and $raw) {
            $collect[$next] = $arg;
            $next = null;
            continue;
        }
        if ($raw) {
            $collect[] = $arg;
            continue;
        }
        if ($arg and $arg[0] === '-' and $next) {
            return str_replace(
                ['%arg', '%given'],
                [$next, $arg],
                GETOPTS_FUNCTION_VALUE_EXPECTED
            );
        }
        if ($next and $opts[$next]['multiple']) {
            if (!isset($collect[$next])) $collect[$next] = [];
            $collect[$next][] = $arg;
            $next = null;
            continue;
        }
        if ($next) {
            $collect[$next] = $arg;
            $next = null;
            continue;
        }
        // Short or long without value
        if (preg_match('~^-([a-zA-Z0-9])$~', $arg, $match) or preg_match('~^--([a-z0-9][a-z0-9-]+)$~', $arg, $match)) {
            $arg = $match[1];
            if (!isset($opts[$arg]))
                return str_replace(
                    ['%arg'],
                    [$arg],
                    GETOPTS_FUNCTION_UNKNOWN
                );
            if ($opts[$arg]['value']) {
                $next = $arg;
            } elseif ($opts[$arg]['multiple']) {
                if (!isset($collect[$arg])) $collect[$arg] = [];
                $collect[$arg][] = true;
            } else {
                $collect[$arg] = true;
            }
            continue;
        }
        // Split long
        if (preg_match('~^--([a-z0-9-]+)=(.*)$~', $arg, $match)) {
            if (isset($opts[$match[1]]) and !$opts[$match[1]]['value'])
                return str_replace('%arg', $match[1], GETOPTS_FUNCTION_EXPECT_NOVALUE);
            array_splice($argv, $i, 1, array('--' . $match[1], $match[2]));
            $i--;
            continue;
        }
        // Split short
        if (preg_match('~^-([a-z])(.*)$~', $arg, $match)) {
            $arg = $match[1];
            if (!isset($opts[$arg]))
                return str_replace('%arg', $arg, GETOPTS_FUNCTION_UNKNOWN);
            array_splice($argv, $i, 1, array('-' . $match[1], ($opts[$arg]['value'] ? '' : '-') . $match[2]));
            $i--;
            continue;
        }
        // Invalid args
        if ($arg and $arg[0] === '-')
            return str_replace('%arg', $arg, GETOPTS_FUNCTION_INVALID);
        $collect[] = $arg;
    }
    if ($next) return str_replace('%arg', $next, GETOPTS_FUNCTION_NOVALUE);
    return $collect;
}

/**
 * Help function for saving data in storage.
 *
 * ```
 * string|null to_storage(string $file, array $settings = array());
 * ```
 *
 * ```php
 * $content = 'foo';
 * $tmp = rtrim(`mktemp`);
 * $file = to_storage($tmp);
 * // $file => "/tmp/tmp.qmviqzrrd1"
 * ```
 *
 * ```php
 * $content = 'foo';
 * $tmp = rtrim(`mktemp`);
 * $file = to_storage($tmp, ['shards' => 2, 'ext' => 'txt']);
 * // $file => "/tmp/ac/bd/tmp.jwueqsppoz.txt"
 * ```
 *
 * For more examples, please, refer to [to_storage.md](docs/to_storage.md).
 */
function to_storage($file, $settings = array()) {
    _expect($file, 'string');
    _expect($settings, 'assoc');
    if (!is_file($file)) {
        _warn(__FUNCTION__ . ": Invalid file ~ {$file}!");
        return null;
    }
    $settings = $settings + [
        'delete' => false,
        'check_duplicate' => false,
        'dir' => sys_get_temp_dir(),
        'ext' => file_get_ext($file),
        'name' => file_get_name($file),
        'shards' => 0,
    ];
    if (!is_dir($settings['dir']))
        return _warn(__FUNCTION__ . ": Invalid directory ~ {$settings['dir']}!");
    if ($settings['shards'] > 2) $settings['shards'] = 2;
    $md5 = md5_file($file);
    $settings['dir'] = rtrim($settings['dir'], '/');
    if ($settings['shards'])
        $settings['dir'] .= preg_replace('~^(..)(..).*~', $settings['shards'] > 1 ? '/$1/$2' : '/$1', $md5);
    exec('mkdir -p ' . escapeshellarg($settings['dir']));
    $settings['name'] = normalize($settings['name'], '.-_');
    if (!$settings['name']) $settings['name'] = mt_rand();
    if ($settings['check_duplicate']) {
        foreach (scandir($settings['dir']) as $f) {
            if ($f === '.' or $f === '..') continue;
            if (md5_file($_ = $settings['dir'] . '/' . $f) === $md5) {
                if ($settings['delete']) unlink($file);
                return $_;
            }
        }
    }
    $ext = ($settings['ext'] ? ".{$settings['ext']}" : "");
    $target = $settings['dir'] . '/' . $settings['name'] . $ext;
    $i = 0;
    while (file_exists($target) and md5_file($target) != $md5) {
        $target = preg_replace($i ? '~-\d+(\.?\w+)$~' : '~(\.?\w+)$~', '-' . ($i) . '$1', $target);
    }
    if (!file_exists($target)) {
        copy($file, $target);
        if ($settings['delete']) unlink($file);
    }
    return $target;
}

/**
 * Latinize string. Set `$ru` to `true` in order to latinize also cyrillic characters.
 *
 * ```
 * string latinize($string, $ru = false);
 * ```
 *
 * ```php
 * echo latinize('Màl Śir');
 * // => "Mal Sir"
 * ```
 *
 * ```php
 * echo latinize('привет мир', $ru = true);
 * // => "privet mir"
 * ```
 */
function latinize($string, $ru = false) {
    static $letters = null;
    if ($letters === null) {
        $letters = <<<END
            `İ¡¿ÀàÁáÂâÃãÄäÅåÆæçÇÈèÉéÊêËëÌìÍíÎîÏïÐððÑñÒòÓóÔôÕõöÖØøÙùÚúÛûÜüÝýÞþÿŸāĀĂ
            'I!?AaAaAaAaAaAaAacCEeEeEeEeIiIiIiIiDdoNnOoOoOoOooOOoUuUuUuUuYyBbyYaAA

            ăąĄćĆĈĉĊċčČďĎĐđēĒĔĕėĖęĘĘěĚĜĝğĞĠġģĢĤĥĦħĨĩīĪĪĬĭįĮıĴĵķĶĶĸĹĺļĻĽľĿŀłŁńŃņŅňŇ
            aaAcCCcCccCdDDdeEEeeEeeEeEGggGGggGHhHhIiiiIIiiIiJjkkKkLllLLlLllLnNnNnN

            ŉŊŋŌōŎŏŐőŒœŔŕŗřŘśŚŜŝşŞšŠŢţťŤŦŧŨũūŪŪŬŭůŮŰűųŲŴŵŶŷźŹżŻžŽƠơƯưǼǽȘșȚțəƏΐάΆέΈ
            nNnOoOoOoOoRrrrRsSSssSsSTttTTtUuuuUUuuUUuuUWwYyzZzZzZOoUuAaSsTteEiaAeE

            ήΉίΊΰαΑβΒγΓδΔεΕζΖηΗθΘιΙκΚλΛμΜνΝξΞοΟπΠρΡςσΣτΤυΥφΦχΧωΩϊΪϋΫόΌύΎώΏјЈћЋ
            hHiIyaAbBgGdDeEzZhH88iIkKlLmMnN33oOpPrRssStTyYfFxXwWiIyYoOyYwWjjcC

            أبتجحدرزسصضطفقكلمنهوي
            abtghdrzssdtfkklmnhoy

            ẀẁẂẃẄẅẠạẢảẤấẦầẨẩẪẫẬậẮắẰằẲẳẴẵẶặẸẹẺẻẼẽẾếỀềỂểỄễỆệỈỉỊịỌọỎ
            WwWwWwAaAaAaAaAaAaAaAaAaAaAaAaEeEeEeEeEeEeEeEeIiIiOoO

            ỏỐốỒồỔổỖỗỘộỚớỜờỞởỠỡỢợỤụỦủỨứỪừỬửỮữỰựỲỳỴỵỶỷỸỹ–—‘’“”•
            oOoOoOoOoOoOoOoOoOoOoUuUuUuUuUuUuUuYyYyYyYy--''""-
END;
        $letters = nsplit($letters);
    }
    $split = function ($_) { return preg_split('/(?<!^)(?!$)/u', $_); };
    $n = count($letters) / 2;
    for ($i = 0; $i < $n; $i++)
        $string = strtr(
            $string,
            array_combine($split($letters[$i * 2]), $split($letters[$i * 2 + 1]))
        );
    $string = strtr($string, [
        'خ' => 'kh', 'ذ' => 'th', 'ش' => 'sh', 'ظ' => 'th',
        'ع' => 'aa', 'غ' => 'gh', 'ψ' => 'ps', 'Ψ' => 'PS',
        'đ' => 'dj', 'Đ' => 'Dj', 'ß' => 'ss', 'ẞ' => 'SS',
        'Ä' => 'Ae', 'ä' => 'ae', 'Æ' => 'AE', 'æ' => 'ae',
        'Ö' => 'Oe', 'ö' => 'oe', 'Ü' => 'Ue', 'ü' => 'ue',
        'Þ' => 'TH', 'þ' => 'th', 'ђ' => 'dj', 'Ђ' => 'Dj',
        'љ' => 'lj', 'Љ' => 'Lj', 'њ' => 'nj', 'Њ' => 'Nj',
        'џ' => 'dz', 'Џ' => 'Dz', 'ث' => 'th', '…' => '...',
    ]);
    if ($ru) {
        $string = strtr($string, [
            'А' => 'A', 'а' => 'a',
            'Б' => 'B', 'б' => 'b',
            'В' => 'V', 'в' => 'v',
            'Г' => 'G', 'г' => 'g',
            'Д' => 'D', 'д' => 'd',
            'Е' => 'E', 'е' => 'e',
            'Ё' => 'E', 'ё' => 'e',
            'Ж' => 'Zh', 'ж' => 'zh',
            'З' => 'Z', 'з' => 'z',
            'И' => 'I', 'и' => 'i',
            'Й' => 'I', 'й' => 'i',
            'К' => 'K', 'к' => 'k',
            'Л' => 'L', 'л' => 'l',
            'М' => 'M', 'м' => 'm',
            'Н' => 'N', 'н' => 'n',
            'О' => 'O', 'о' => 'o',
            'П' => 'P', 'п' => 'p',
            'Р' => 'R', 'р' => 'r',
            'С' => 'S', 'с' => 's',
            'Т' => 'T', 'т' => 't',
            'У' => 'U', 'у' => 'u',
            'Ф' => 'F', 'ф' => 'f',
            'Х' => 'Kh', 'х' => 'kh',
            'Ц' => 'Tc', 'ц' => 'tc',
            'Ч' => 'Ch', 'ч' => 'ch',
            'Ш' => 'Sh', 'ш' => 'sh',
            'Щ' => 'Shch', 'щ' => 'shch',
            'Ъ' => '', 'ъ' => '',
            'Ы' => 'Y', 'ы' => 'y',
            'Ь' => '', 'ь' => '',
            'Э' => 'E', 'э' => 'e',
            'Ю' => 'Iu', 'ю' => 'iu',
            'Я' => 'Ia', 'я' => 'ia',
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
        ]);
    }
    return $string;
}

/**
 * Normalize string by removing non-English chars. Can add some extra chars (using `$extra`) and cyrillic chars (using `$ru`).
 *
 * ```
 * string normalize($string, $extra = "", $ru = false);
 * ```
 *
 * ```php
 * echo normalize("Hello, world!");
 * // => "hello world"
 * ```
 *
 * ```php
 * echo normalize("John's hat!", $extra = "'");
 * // => "john's hat"
 * ```
 *
 * ```php
 * echo normalize("Привет, мир!", $extra = "", $ru = true);
 * // => "привет мир"
 * ```
 */
function normalize($string, $extra = '', $ru = false) {
    $string = mb_strtolower($string, 'utf-8');
    $extra = preg_quote($extra, '|');
    if ($ru) {
        $string = strtr($string, array('ё' => 'е'));
        $extra .= 'а-я';
    }
    $regex = "|[^a-z0-9{$extra}]|u";
    $string = preg_replace($regex, ' ', $string);
    $string = preg_replace('|\s+|', ' ', $string);
    $string = trim($string);
    return $string;
}

/**
 * Universal entrypoint for config get/set operations.
 *
 * ```
 * ; config.ini
 *
 * [global]
 * debug = 1
 * ```
 *
 * ```php
 * $config = parse_ini_file("config.ini", true);
 * config(".", $config);
 * $global = config("global");
 * // $global => ["debug" => "1"]
 * $debug = config("global.debug");
 * // $debug => "1"
 * ```
 */
function config() {
    static $config = array();
    $n = func_num_args();
    if ($n === 0)
        return $config;
    if ($n > 2) return;
    $first = func_get_arg(0);
    if ($n === 1 and $first === ".")
        return $config;
    if ($n === 2 and $first === ".") {
        $config = func_get_arg(1);
        return;
    }
    $first = ltrim($first, '.');
    if (!$first) return;
    $first = explode('.', $first);
    if (count($first) > 2) return;
    $fnmatch0 = (strpos($first[0], '*') !== false or strpos($first[0], '?') !== false);
    $fnmatch = ($fnmatch0 or strpos(implode('', $first), '*') !== false or strpos(implode('', $first), '?') !== false);
    if ($fnmatch and $n === 2) return;
    if ($fnmatch0) {
        $return = array();
        foreach (array_keys($config) as $key) {
            if (!fnmatch($first[0], $key)) continue;
            $args = array(count($first) === 2 ? "{$key}.{$first[1]}" : $key);
            $return[$key] = call_user_func_array(__FUNCTION__, $args);
            if (is_null($return[$key])) unset($return[$key]);
            elseif (!is_array($return[$key]))
                $return[$key] = array($first[1] => $return[$key]);
        }
        return $return;
    }
    if ($fnmatch) {
        $return = array();
        if (!isset($config[$first[0]]) or !is_array($config[$first[0]]))
            return $return;
        foreach (array_keys($config[$first[0]]) as $key) {
            if (!fnmatch($first[1], $key)) continue;
            $args = array("{$first[0]}.{$key}");
            $return[$key] = call_user_func_array(__FUNCTION__, $args);
            if (is_null($return[$key])) unset($return[$key]);
        }
        return $return;
    }
    if ($n === 1 and count($_ = $first) === 1) {
        return isset($config[$_[0]]) ? $config[$_[0]] : null;
    }
    if ($n === 2 and count($_ = $first) === 1) {
        $value = func_get_arg(1);
        if (!is_null($value))
            $config[$_[0]] = $value;
        else unset($config[$_[0]]);
        return;
    }
    if ($n === 1 and count($_ = $first) === 2)
        return isset($config[$_[0]][$_[1]]) ? $config[$_[0]][$_[1]] : null;
    if ($n === 2 and count($_ = $first) === 2) {
        $value = func_get_arg(1);
        if (!is_null($value)) {
            if (!isset($config[$_[0]]))
                $config[$_[0]] = array();
            if (substr($_[1], -2) === '[]') {
                $_[1] = substr($_[1], 0, -2);
                if (!isset($config[$_[0]][$_[1]]))
                    $config[$_[0]][$_[1]] = array();
                $config[$_[0]][$_[1]][] = $value;
            } else $config[$_[0]][$_[1]] = $value;
        } else {
            unset($config[$_[0]][$_[1]]);
        }
        return;
    }
    return;
}

/**
 * Correctly saves value to INI file (or creates new one).
 *
 * ```
 * bool ini_file_set($file, $key, $value);
 * ```
 *
 * ```php
 * $return = ini_file_set("config.ini", "global.debug", "0");
 * // $return => true
 * echo file_get_contents("config.ini");
 * ```
 *
 * Output:
 *
 * ```
 * ; <?php exit();
 * ; /*
 *
 * [global]
 * debug = 0
 *
 * ; *\/
 * ```
 */
function ini_file_set($file, $key, $value) {
    $key = explode('.', $key);
    if (count($key) == 1 and is_assoc($value)) {
        $return = true;
        foreach ($value as $k => $v)
            $return = ($return and (call_user_func_array(__FUNCTION__, [$file, "{$key[0]}.{$k}", $v])));
        return $return;
    }
    if (count($key) != 2 or !$key[0] or !$key[1])
        return false;
    if (!is_file($file))
        $ini = array();
    else $ini = parse_ini_file($file, true);
    if (!array_key_exists($key[0], $ini))
        $ini[$key[0]] = array();
    if (is_null($value)) {
        unset($ini[$key[0]][$key[1]]);
    } else {
        $ini[$key[0]][$key[1]] = $value;
    }
    $save = array();
    $echo = function($_) {
        if (is_numeric($_)) return $_;
        if (is_bool($_)) return $_ ? 1 : 0;
        if (is_null($_)) return 0;
        if (ctype_alnum(str_replace(['.', '_', '-'], '', $_))) return $_;
        if ($_ === "") return "";
        return "'{$_}'";
    };
    foreach ($ini as $key => $val) {
        $save[] = sprintf("[%s]", $key);
        foreach ($val as $_key => $_val)
            if (is_array($_val)) {
                foreach ($_val as $_)
                    $save[] = sprintf("%s[] = %s", $_key, $echo($_));
            } else {
                $save[] = sprintf("%s = %s", $_key, $echo($_val));
            }
        $save[] = "\n";
    }
    $head = "; <?php exit();\n; /*";
    $tail = "; {$file} */";
    $save = sprintf("%s\n\n%s\n\n%s\n", $head, trim(implode("\n", $save)), $tail);
    $save = str_replace("\n\n\n", "\n\n", $save);
    file_put_contents($file, $save);
    return true;
}

/**
 * Transforms readable form of string to variable.
 */
function readable_to_variable($input, $trim = true) {
    $self = __FUNCTION__;
    if (!is_string($input)) return null;
    if ($trim) $input = trim($input);
    if (is_null($input)) return null;
    if ($input === "") return "";
    $lower = strtolower($input);
    if ($lower === 'true') return true;
    if ($lower === 'false') return false;
    if ($lower === 'null') return null;
    if (defined($input)) return constant($input);
    if (is_float($input)) return floatval($input);
    if (is_numeric($input)) return intval($input);
    if (preg_match('~^\[(.*)\]$~s', $input, $match)) {
        $match[1] = trim($match[1]);
        if ($match[1] === "") return array();
        $array = array();
        $kvs = preg_split('~\s*,\s*~', $match[1]);
        foreach ($kvs as $kv) {
            $kv = explode('=>', $kv, 2);
            if (count($kv) == 2)
                $array[$self($kv[0], $trim)] = $self($kv[1], $trim);
            else $array[] = $self($kv[0], $trim);
        }
        return $array;
    }
    return $input;
}

/**
 * Transform any variable to readable form.
 */
function variable_to_readable($variable) {
    $self = __FUNCTION__;
    $return = function ($_) {
        return $_ . ($_ !== "" ? "\n" : '');
    };
    if (is_null($variable)) return $return("");
    if ($variable === false) return $return("false");
    if ($variable === true) return $return("true");
    if (is_float($variable) or is_integer($variable)) return $return($variable);
    if (is_string($variable)) return $return(trim($variable));
    if (is_object($variable) and method_exists($variable, '__toString'))
        return $return(trim((string)($variable)));
    if (!is_array($variable)) return "";
    if (count(array_filter(array_keys($variable), 'is_numeric')) == count($variable) and count(array_filter($variable, 'is_array')) == 0)
        return $return(implode("\n", array_map(function ($variable) use ($self) {
            return trim($self($variable));
        }, array_values($variable))));
    if (is_assoc($variable) and count(array_filter($variable, 'is_array')) == 0) {
        $echo = array();
        foreach ($variable as $k => $v) {
            $v = trim($self($v));
            $echo[] = "{$k} => {$v}";
        }
        return $return(implode("\n", $echo));
    }
    return $return(trim(json_encode($variable, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));
}

/**
 * Cron out-of-the-box. Supports Linux format.
 * ```
 *  * * * * *
 *  | | | | |
 *  | | | | +----- Days of week (0-6), 0 - Sunday
 *  | | | +------- Months (1-12)
 *  | | +--------- Days of month (1-31)
 *  | +----------- Hours (0-23)
 *  +------------- Minutes (0-59)
 *
 * * - any value
 * 1 - certain value
 * 1-2 - value lies in interval
 * 1,4 - list of values
 * *\/2 - all even values
 * 1,2-3,*\/4 - mix
 * ```
 */
function go_cron($file, $dir = null) {
    if (is_null($dir)) $dir = sys_get_temp_dir();
    if (!is_dir($dir)) mkdir($dir);
    if (is_file($file)) $crons = nsplit(template($file));
    else _err(__FUNCTION__ . ": INVALID CRON! | {$file}");
    $time = func_num_args() > 2 ? func_get_arg(2) : time();
    $trigger = function ($cron) use ($time) {
        $format = "iHdnw";
        for ($i = 0; $i < strlen($format); $i++) {
            $t = intval(date($format[$i], $time));
            foreach (explode(',', $cron[$i]) as $elem) {
                if ($elem === '*') continue 2;
                if (is_numeric($elem) and $elem == $t) continue 2;
                if (
                    preg_match('~^(\d+)-(\d+)$~', $elem, $match)
                        and
                    intval($match[1]) <= $t
                        and
                    $t <= intval($match[2])
                ) continue 2;
                if (
                    preg_match('~^\*/(\d+)$~', $elem, $match)
                        and
                    ($t % $match[1] === 0)
                ) continue 2;
            }
            return false;
        }
        return true;
    };
    foreach ($crons as $cron) {
        if (strpos($cron, '#') === 0) continue;
        $cron = preg_split('~\s+~', $cron, 6);
        if (count($cron) != 6) continue;
        $exec = array_pop($cron);
        if (!$trigger($cron)) continue;
        $name = preg_split('~\s+~', $exec);
        array_walk($name, function (& $_) {
            if (is_file($_)) $_ = basename($_);
            if (is_dir(dirname($_))) $_ = basename($_);
        });
        $name = implode(' ', $name);
        $std = sprintf(
            "%s/%s.%s.txt",
            $dir,
            time(),
            substr(str_replace(' ', '-', normEn($name)), 0, 255)
        );
        shell_exec($_ = sprintf("nohup bash -c %s >%s 2>&1 &", escapeshellarg($exec), escapeshellarg($std)));
        _log(__FUNCTION__ . ': ' . $_);
    }
}

/**
 * Universal SQL wrapper.
 */
function SQL() {
    static $link;
    if (is_array($link)) {
        if (!function_exists('mysqli_connect'))
            _err("MYSQLI IS NOT INSTALLED!");
        @ $link = call_user_func_array('mysqli_connect', $link);
        if (mysqli_connect_errno())
            _err("INVALID SQL CONNECTION: " . mysqli_connect_error());
    }
    $n = func_num_args();
    if (!$n and !is_resource($link)) _err("SQL CONNECTION IS NOT DEFINED!");
    if (!$n) return $link;
    $args = func_get_args();
    if (is_null($link)) {
        $link = $args;
        return;
    }
    $sql = array_shift($args);
    $sql = trim($sql);
    $isInsert = $isReplace = $isUpdate = $isDelete = false;
    if ($isSelect = (stripos($sql, 'select') === 0)) ;
    elseif ($isInsert = (stripos($sql, 'insert') === 0)) ;
    elseif ($isReplace = (stripos($sql, 'replace') === 0)) ;
    elseif ($isUpdate = (stripos($sql, 'update') === 0)) ;
    elseif ($isDelete = (stripos($sql, 'delete') === 0)) ;
    $escape = function ($string) use ($link) {
        if ($string === null) return 'NULL';
        if ($string === false) return '0';
        if ($string === true) return '1';
        if (is_object($string) and method_exists($string, '__toString'))
            $string = (string)($string);
        $string = mysqli_real_escape_string($link, $string);
        return '\'' . $string . '\'';
    };
    if ($args) {
        $collect = array();
        foreach ($args as $arg)
            if (is_assoc($arg)) {
                array_walk($arg, function (& $v, $k) use ($escape) {
                    $v = sprintf("`%s` = %s", $k, $escape($v));
                });
                $collect[] = implode(', ', array_values($arg));
            } elseif (is_array($arg)) {
                array_walk($arg, function (& $v) use ($escape) {
                    $v = $escape($v);
                });
                $collect[] = implode(', ', $arg);
            } else $collect[] = $escape($arg);
        array_unshift($collect, $sql);
        $sql = call_user_func_array('sprintf', $collect);
    }
    $result = mysqli_query($link, $sql);
    if (!$result) _err(sprintf(
        "SQL ERROR: %s, %s -> %s",
        mysqli_sqlstate($link),
        mysqli_error($link),
        trim(preg_replace('~\s+~', ' ', $sql))
    ));
    if ($isInsert or $isReplace) return mysqli_insert_id($link);
    if ($isUpdate or $isDelete) return mysqli_affected_rows($link);
    if (!($result instanceof mysqli_result)) return $result;
    $rows = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) $rows[] = $row;
    mysqli_free_result($result);
    return $rows;
}

function R() {
    static $link;
    if (is_array($link)) {
        $args = [
            'host' => $link[0],
            'port' => $link[1],
            'auth' => $link[2],
            'db' => $link[3],
        ];
        if (extension_loaded('Redis')) {
            $link = new \Redis();
            $res = $link->connect($args['host'], $args['port'], 2);
            $errstr = 'using Redis class';
        } else {
            @ $link = fsockopen($args['host'], $args['port'], $errno, $errstr, 2);
        }
        if (!$link or (isset($res) and !$res))
            _err(__FUNCTION__ . ': Connection failed - ' . trim($errstr));
        if ($args['auth']) {
            $res = call_user_func(__FUNCTION__, 'AUTH', $args['auth']);
            if (!$res) _err(__FUNCTION__ . ': AUTH failed!');
        }
        if (is_numeric($args['db'])) {
            $res = call_user_func(__FUNCTION__, 'SELECT', $args['db']);
            if (!$res) _err(__FUNCTION__ . ': SELECT failed!');
        }
    }
    $n = func_num_args();
    if (!$n and !is_resource($link) and !is_object($link))
        _err("REDIS CONNECTION IS NOT DEFINED!");
    if (!$n) return $link;
    $args = func_get_args();
    if (is_null($link)) {
        $link = $args;
        return;
    }
    if (is_object($link)) {
        $method = array_shift($args);
        return call_user_func_array([$link, $method], $args);
    }
    $function = __FUNCTION__;
    $read = function () use (& $read, & $link, $function) {
        $reply = trim(fgets($link, 512));
        $first = substr($reply, 0, 1);
        if ($first === '-')
            _err($function . ': ERROR (' . trim($reply) . ')');
        if ($first === '+') {
            $response = substr(trim($reply), 1);
            if ($response === 'OK') $response = true;
            return $response;
        }
        if ($first === '$') {
            $size = intval(substr($reply, 1));
            if ($size === -1) return null;
            $response = [];
            $bytes_all = 0;
            do {
                $block_size = ($size - $bytes_all) > 1024 ? 1024 : ($size - $bytes_all);
                $r = fread($link, $block_size);
                if ($r === false)
                    _err($function . ': READ FROM SERVER ERROR!');
                else {
                    $bytes_all += strlen($r);
                    $response[] = $r;
                }
            } while ($bytes_all < $size);
            fread($link, 2); /* CRLF */
            return implode('', $response);
        }
        if ($first === '*') {
            $count = intval(substr($reply, 1));
            if ($count === -1) return null;
            $responses = array();
            for ($i = 0; $i < $count; $i++)
                $responses[] = $read();
            return $responses;
        }
        if ($first === ':') {
            return intval(substr(trim($reply), 1));
        }
        _err($function . ': Unknown response!');
    };
    $call = function ($name, $args) {
        $crlf = "\r\n";
        array_unshift($args, $name);
        $cmd = sprintf('*%d%s%s%s', count($args), $crlf, implode(
            array_map(function ($arg) use ($crlf) {
                return sprintf('$%d%s%s', strlen($arg), $crlf, $arg);
            }, $args), $crlf
        ), $crlf);
        for ($written = 0; $written < strlen($cmd); $written += $fwrite) {
            $fwrite = fwrite($link, substr($cmd, $written));
            if ($fwrite === false or $fwrite <= 0)
                _err(__FUNCTION__ . ': WRITE ERROR!!');
        }
    };
    call_user_func_array($call, $args);
    return $read();
}

/**
 * Encode string to URL-safe Base64 format.
 */
function url_base64_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Decode from URL-safe Base64 format.
 */
function url_base64_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

/**
 * XOR encryption.
 */
function xencrypt($string, $key) {
    $string = mt_rand() . ':' . $string . ':' . mt_rand();
    for ($i = 0; $i < strlen($string); $i++) {
        $k = md5($key . (string)(substr($string, $i + 1)) . $i);
        for ($j = 0; $j < strlen($k); $j++)
            $string[$i] = $string[$i] ^ $k[$j];
    }
    return url_base64_encode($string);
}

/**
 * XOR decryption.
 */
function xdecrypt($string, $key) {
    $string = url_base64_decode($string);
    for ($i = strlen($string) - 1; $i >= 0; $i--) {
        @ $k = md5($key . (string)(substr($string, $i + 1)) . $i);
        for ($j = 0; $j < strlen($k); $j++)
            $string[$i] = $string[$i] ^ $k[$j];
    }
    $string = explode(':', $string, 2);
    if (count($string) != 2 or !is_numeric($string[0])) return null;
    $string = $string[1];
    $pos = strrpos($string, ':');
    if (!$pos) return null;
    return substr($string, 0, $pos);
}

/**
 * Implements OpenSSL encryption.
 */
function oencrypt($string, $key) {
    $method = 'aes-256-ofb';
    $iv = substr(md5($key), 0, 16);
    $string = mt_rand() . ':' . $string . ':' . mt_rand();
    $string = openssl_encrypt($string, $method, $key, OPENSSL_RAW_DATA, $iv);
    return url_base64_encode($string);
}

/**
 * Implements OpenSSL decryption.
 */
function odecrypt($string, $key) {
    $method = 'aes-256-ofb';
    $iv = substr(md5($key), 0, 16);
    $string = url_base64_decode($string);
    $string = openssl_decrypt($string, $method, $key, OPENSSL_RAW_DATA, $iv);
    $string = explode(':', $string, 2);
    if (count($string) != 2 or !is_numeric($string[0]))
        return null;
    $string = $string[1];
    $pos = strrpos($string, ':');
    if (!$pos) return null;
    return substr($string, 0, $pos);
}

/**
 * Decode string from Base32 format.
 */
function base32_decode($s) {
    static $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $tmp = array();
    foreach (str_split($s) as $c)
        $tmp[] = sprintf('%05b', intval(strpos($map, $c)));
    $tmp = implode('', $tmp);
    $args = array_map('bindec', str_split($tmp, 8));
    array_unshift($args, 'C*');
    return rtrim(call_user_func_array('pack', $args), "\0");
}

/**
 * Encode string in Base32 format.
 */
function base32_encode($string) {
    static $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $output = array();
    $collect = array();
    for ($i = 0; $i < strlen($string); $i++)
        $collect[] = str_pad(decbin(ord($string[$i])), 8, '0', STR_PAD_LEFT);
    $neededPad = 5 - (count($collect) % 5);
    if ($neededPad > 0 and $neededPad < 5)
        $collect[] = str_repeat('0', 5 - $neededPad);
    $collect = implode('', $collect);
    foreach (str_split($collect, 5) as $binaryChunk)
        $output[] = $map[bindec($binaryChunk)];
    return implode('', $output);
}

function im_wrapper($image, $settings) {
    if (!is_file($image) or !getimagesize($image) or !$settings['action'])
        return null;
    if (!isset($settings['dir'])) $settings['dir'] = dirname($image);
    if (!is_dir($settings['dir'])) mkdir($settings['dir']);
    $md5 = md5(md5_file($image) . $settings['action']);
    $name = $settings['prefix'] . substr($md5, 0, 10);
    $ext = image_type_to_extension(exif_imagetype($image));
    if (!$ext) return _warn("Invalid extension: {$image}");
    $target = "{$settings['dir']}/{$name}{$ext}";
    $log = isset($settings['log']) ? $settings['log'] : false;
    $overwrite = isset($settings['overwrite']) ? $settings['overwrite'] : false;
    if (!is_file($target) or $overwrite) {
        $output = shell_exec($cmd = sprintf(
            "convert %s %s %s 2>&1",
            escapeshellarg($image),
            $settings['action'],
            escapeshellarg($target)
        ));
        $ok = is_file($target);
        if ($ok and $log) _log($cmd);
        elseif (!$ok) return _warn($cmd . ': ' . trim($output));
    }
    return $target;
}

/**
 * Resize image.
 */
function im_resize($image, $settings) {
    if (!$settings['size']) return null;
    $size = escapeshellarg($settings['size']);
    $_ = array('action' => "-resize {$size}");
    return im_wrapper($image, $_ + $settings + ['prefix' => __FUNCTION__ . '_']);
}

/**
 * Make the image transparent.
 */
function im_transparent($image, $settings = array()) {
    if (!isset($settings['transparent'])) $settings['transparent'] = 'white';
    $transparent = escapeshellarg($settings['transparent']);
    $_ = array('action' => "-fuzz 5% -transparent {$transparent}");
    return im_wrapper($image, $_ + $settings + ['prefix' => __FUNCTION__ . '_']);
}

/**
 * Crop provided image.
 */
function im_crop($image, $settings = array()) {
    if (!$settings['size']) return null;
    $size = escapeshellarg($settings['size']);
    $gravity = !empty($settings['gravity']) ? $settings['gravity'] : '';
    $values = ['', 'NorthWest', 'North', 'NorthEast', 'West', 'Center', 'East', 'SouthWest', 'South', 'SouthEast'];
    $values = array_combine(array_map('strtolower', $values), $values);
    if (!isset($values[strtolower($gravity)])) {
        _warn(__FUNCTION__ . ": Invalid gravity value: {$gravity}");
        $gravity = '';
    }
    $gravity = $values[strtolower($gravity)];
    $gravity = $gravity ? sprintf("-gravity %s", escapeshellarg($gravity)) : '';
    $_ = array('action' => "{$gravity} -crop {$size} +repage");
    return im_wrapper($image, $_ + $settings + ['prefix' => __FUNCTION__ . '_']);
}

/**
 * Draw border around image.
 */
function im_border($image, $settings = array()) {
    if (!isset($settings['border'])) $settings['border'] = '1';
    if (!isset($settings['borderColor'])) $settings['borderColor'] = 'black';
    $border = escapeshellarg($settings['border']);
    $borderColor = escapeshellarg($settings['borderColor']);
    $_ = array('action' => "-border {$border} -bordercolor {$borderColor}");
    return im_wrapper($image, $_ + $settings + ['prefix' => __FUNCTION__ . '_']);
}

/**
 * Generate captcha image.
 */
function im_captcha($settings = array()) {
    $settings = $settings + [
        'width' => null,
        'height' => 50,
        'length' => 6,
        'padding' => 4
    ];
    $width = $settings['width'];
    $height = $settings['height'];
    $length = $settings['length'];
    $padding = $settings['padding'];
    $padding = intval($padding) > 0 ? intval($padding) : 4;
    $length = intval($length) > 0 ? intval($length) : 6;
    $height = intval($height) > 0 ? intval($height) : 50;
    $paddingLR = $padding;
    $paddingTB = $padding;
    $size = $height - 2 * $padding;
    $fonts = glob(__DIR__ . "/fonts/*.ttf");
    if (!$fonts) return array();
    $font = $fonts[mt_rand(0, count($fonts) - 1)];
    $box = imagettfbbox($size, 0, $font, "H");
    $_width = abs($box[2] - $box[0]);
    $_shift = abs($box[6] - $box[0]);
    $_height = abs($box[7] - $box[1]);
    //
    if (!$width) $width = ($length * $_width) + (($length + 1) * $padding) + $_shift;
    //
    $image = imagecreatetruecolor($width, $height);
    if (!$image) return array();
    $letters = 'ABCDEFGHJKMNPRSTUVWXYZ'; // no "I", "L", "O", "Q"
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $lineColor = imagecolorallocate($image, 64, 64, 64);
    $pixelColor = imagecolorallocate($image, 0, 0, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColor);
    // 3 lines
    imageline($image, 0, mt_rand() % $height, $width, mt_rand() % $height, $lineColor);
    imageline($image, 0, mt_rand() % $height, $width, mt_rand() % $height, $lineColor);
    imageline($image, 0, mt_rand() % $height, $width, mt_rand() % $height, $lineColor);
    // add noise
    for ($i = 0; $i < 500; $i++)
        imagesetpixel($image, mt_rand() % $width, mt_rand() % $height, $pixelColor);
    $len = strlen($letters);
    $word = "";
    for ($i = 0; $i < $length; $i++) {
        $letter = $letters[mt_rand(0, $len - 1)];
        $angle = (-5 + mt_rand(0, 10));
        imagettftext(
            $image,
            $size,
            $angle,
            ($i * ($padding + $_width)) + $padding,
            $padding + $_height,
            $textColor,
            $font,
            $letter
        );
        $word .= $letter;
    }
    $file = rtrim(`mktemp --suffix=.png`);
    imagepng($image, $file);
    imagedestroy($image);
    $im_border = array();
    if (isset($settings['borderColor']))
        $im_border['borderColor'] = $settings['borderColor'];
    if (isset($settings['border']))
        $im_border['border'] = $settings['border'];
    $file = im_border($file, $im_border);
    if (!$file) return array();
    return array('file' => $file, 'word' => $word);
}

function crawler($urls, $settings) {
    if (!isset($settings['dir'])) {
        _warn("DIR IS NOT DEFINED!");
        return false;
    }
    $dir = $settings['dir'];
    if (!isset($settings['level'])) $settings['level'] = 5;
    $level = $settings['level'];
    if (!$level) return true;
    if (!is_dir($dir)) mkdir($dir);
    if (!is_dir($dir)) return false;
    if (is_file($_ = $dir . '/pool.txt') and empty(file_get_contents($_))) {
        _warn("POOL IS EMPTY!");
        return true;
    }
    $files = array('inimages', 'outimages', 'instatic', 'outstatic', 'inlinks', 'outlinks', 'errlinks', 'pool');
    foreach ($files as $name) {
        $$name = null;
        $ref = & $$name;
        $ref = array('file' => $dir . "/{$name}.txt", 'all' => array());
        if (is_file($ref['file'])) {
            $ref['all'] = nsplit(file_get_contents($ref['file']));
            $ref['all'] = array_flip($ref['all']);
        }
    }
    if (is_string($urls)) $urls = array($urls);
    $limit_per_level = isset($settings['limit_per_level']) ? $settings['limit_per_level'] : 100;
    $filter = $settings['filter'];
    if (!is_callable($filter)) {
        _warn("FILTER IS NOT CALLABLE!");
        return false;
    }
    if (!is_null($urls) and !$pool['all']) $pool['all'] = array_flip($urls);
    $urls = array_keys($pool['all']);
    _warn("Start loop #{$level} with " . count($urls) . " links!");
    $_ = microtime(true);
    if (count($urls) > $limit_per_level) {
        mt_shuffle($urls);
        $_ = round(microtime(true) - $_, 1);
        if ($_ > 0.5) _warn(sprintf("Shuffle done in %s sec!", $_));
        $_ = microtime(true);
        $urls = array_slice($urls, 0, $limit_per_level);
    }
    mt_shuffle($urls);
    $_ = round(microtime(true) - $_, 1);
    if ($_ > 0.5) _warn(sprintf("Shuffle done in %s sec!", $_));
    $path = function ($url, $header = false) use ($dir) {
        $host = host($url);
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $path = normEn(normLatinRu(normLatin($path)));
        if (!$path) $path = 'index.html';
        $path = str_replace(' ', '-', $path);
        $md5 = substr(md5($url), 0, 6);
        $path = "{$dir}/{$host}/{$path}-{$md5}/" . ($header ? "header.txt" : "content.html");
        if (!is_dir(dirname(dirname($path))))
            mkdir(dirname(dirname($path)));
        if (!is_dir(dirname($path)))
            mkdir(dirname($path));
        return $path;
    };
    $process = function ($url, $content) use (
        $filter, & $inimages, & $outimages, & $instatic, & $outstatic,
        & $inlinks, & $outlinks, & $errlinks, & $pool
    ) {
        if (!$content) return;
        $host = host($url);
        if (!$host) return;
        if (!$filter($url)) return;
        $unique = array();
        $hrefs = xpath($content, '//a/@href');
        $srcs = xpath($content, '//img/@src');
        foreach (array_merge($srcs, $hrefs) as $href) {
            $v0 = getTagAttr($href, 'href');
            $v1 = getTagAttr($href, 'src');
            $href = $v0 ?: $v1;
            $href = realurl($href, $url);
            $isImage = ($v1 or isset($inimages['all'][$href]) or isset($outimages['all'][$href]));
            if (!host($href)) continue;
            if ($href === $url) continue;
            if (isset($unique[$href])) continue;
            $unique[$href] = true;
            $isInner = $filter($href);
            $ext = file_get_ext(parse_url($href, PHP_URL_PATH) ?: '/');
            $isStatic = ($ext and !in_array($ext, array('html', 'txt', 'php', 'htm', 'aspx', 'asp', 'xhtml', 'shtml')) and strlen($ext) < 5);
            if ($isImage and $isInner and !isset($inimages['all'][$href]))
                $inimages['all'][$href] = true;
            elseif ($isImage and !$isInner and !isset($outimages['all'][$href]))
                $outimages['all'][$href] = true;
            if ($isImage) continue;
            if ($isStatic and $isInner and !isset($instatic['all'][$href]))
                $instatic['all'][$href] = true;
            elseif ($isStatic and !$isInner and !isset($outstatic['all'][$href]))
                $outstatic['all'][$href] = true;
            if ($isStatic) continue;
            $isset = (
                isset($pool['all'][$href])
                    or
                isset($inlinks['all'][$href])
                    or
                isset($outlinks['all'][$href])
                    or
                isset($errlinks['all'][$href])
                    or
                isset($inimages['all'][$href])
                    or
                isset($outimages['all'][$href])
            );
            if ($isset) continue;
            if ($isInner)
                $pool['all'][$href] = true;
            else $outlinks['all'][$href] = true;
        }
    };
    $collect = array_flip($urls);
    $results = curl($urls, ['format' => 'complex'] + $settings['curl']);
    foreach ($results as $url => $result) {
        $p0 = $path($url);
        $p1 = $path($result['info']['url']);
        $ph = $path($result['info']['url'], $header = true);
        _log("{$result['info']['url']} .. {$p1}");
        $process($result['info']['url'], $result['content']);
        file_put_contents($ph, $result['header']);
        file_put_contents($p1, $result['content']);
        unset($collect[$url]);
        unset($pool['all'][$url]);
        $inlinks['all'][$url] = true;
        if ($p0 == $p1) continue;
        if (!file_exists($p0)) symlink($p1, $p0);
        $url = $result['info']['url'];
        $inlinks['all'][$url] = true;
        if (isset($pool['all'][$url]))
            unset($pool['all'][$url]);
        if (isset($collect[$url]))
            unset($collect[$url]);
    }
    foreach (array_keys($collect) as $url) {
        _warn("{$url} .. ERR!");
        $errlinks['all'][$url] = true;
        if (isset($pool['all'][$url]))
            unset($pool['all'][$url]);
    }
    foreach ($files as $name) {
        $ref = & $$name;
        $content = implode("\n", array_keys($ref['all']));
        file_put_contents($ref['file'], $content ? $content . "\n" : '');
    }
    return crawler(null, ['level' => --$level] + $settings);
}

/**
 * Raise an error, if given variable does not match type.
 *
 * ```
 * _expect(mixed $var, string $types);
 * ```
 *
 * ```php
 * $a = 'string';
 * _expect($a, 'string|null');
 * ```
 */
function _expect($var, $types) {
    $expected = [];
    $types = explode('|', $types);
    foreach ($types as $type) {
        $function = ($type === 'boolean' ? 'is_bool' : 'is_' . $type);
        $flag = function_exists($function);
        if ($flag ? $function($var) : is_a($var, $type)) return;
        $expected[] = $type . ($flag ? '' : ' object');
    }
    $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
    $debug = array_slice($debug, 1);
    $debug = array_map(function ($arg) use (& $first) {
        $file = basename($arg['file']);
        return "{$arg['function']}@{$file}:{$arg['line']}";
    }, $debug);
    $debug = implode(', ', $debug);
    $expected = implode(', ', $expected);
    $given = (is_object($var) ? get_class($var) . ' ' : '') . gettype($var);
    _err(str_replace(
        ['%expected', '%given', '%debug'],
        [$expected, $given, $debug],
        EXPECT_FUNCTION_ERR_MSG
    ));
}

/**
 * Raise a user defined error with a message. 
 * Shortcut to `trigger_error()` function. 
 * For CLI mode outputs to `STDERR` instead of raising an error. 
 * If `E_USER_ERROR` is raised, exit with error code `1`.
 *
 * ```
 * _log(string $msg, int $level = E_USER_NOTICE);
 * ```
 */
function _log($msg, $level = E_USER_NOTICE) {
    static $console = null;
    if (is_null($console))
        $console = (defined('STDIN') and defined('STDERR'));
    if ($console) {
        if ($level === E_USER_NOTICE) $type = 'E_USER_NOTICE';
        elseif ($level === E_USER_WARNING) $type = 'E_USER_WARNING';
        elseif ($level === E_USER_ERROR) $type = 'E_USER_ERROR';
        else $type = 'E_UNKNOWN';
        $msg = preg_replace('~\s+~', ' ', trim($msg));
        $msg = str_replace(
            ['%type', '%msg', '%now'],
            [$type, $msg, now()],
            LOG_FUNCTION_CONSOLE_FORMAT
        );
        fwrite(STDERR, $msg . "\n");
    } else {
        trigger_error($msg, $level);
    }
    if ($level === E_USER_ERROR) exit(1);
}

/**
 * Raise `E_USER_WARNING` with a message.
 *
 * ```
 * _warn(string $msg);
 * ```
 */
function _warn($msg) {
    _log($msg, E_USER_WARNING);
}

/**
 * Raise `E_USER_ERROR` with a message. Exit with error code `1`.
 *
 * ```
 * _err(string $msg);
 * ```
 */
function _err($msg) {
    _log($msg, E_USER_ERROR);
}

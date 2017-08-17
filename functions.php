<?php

define('SQL_FORMAT_DATE', 'Y-m-d');
define('SQL_FORMAT_DATETIME', 'Y-m-d H:i:s');
define('EXPECT_FUNCTION_ERR_MSG', "Invalid type: expected %expected, but given %given.");
define('LOG_FUNCTION_CONSOLE_FORMAT', "[%type] %now ~ %msg");

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
    $info = pathinfo($file);
    if (isset($info['extension']))
        return strtolower($info['extension']);
    return '';
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
    $info = pathinfo($file);
    if (isset($info['filename']))
        return $info['filename'];
    return '';
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
 * // $int => 48928
 * ```
 *
 * ```php
 * $int = rand_from_string("two");
 * // $int => 48928
 * ```
 *
 * ```php
 * $int = rand_from_string("one");
 * // $int => 48928
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
    $expected = implode(', ', $expected);
    $given = (is_object($var) ? get_class($var) . ' ' : '') . gettype($var);
    _err(str_replace(
        ['%expected', '%given'],
        [$expected, $given],
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

<?php

define('SQL_FORMAT_DATE', 'Y-m-d');
define('SQL_FORMAT_DATETIME', 'Y-m-d H:i:s');

/**
 * Encode/decode HTML chars in given string: `>`, `<`, `&`, `'` and `"`. 
 * Use this function to escape HTML tags content and atrribute values.
 *
 * @param string $string
 * @param bool   $decode (optional)
 *
 * @return string
 */
function esc(string $string, bool $decode = false): string
{
    return call_user_func(
        $decode ? 'html_entity_decode' : 'htmlspecialchars',
        $string,
        ENT_QUOTES
    );
}

/**
 * Validate a hostname (an IP address or domain name).
 *
 * ```php
 * $bool = is_host('github.com');
 * // $bool => true
 * ```
 *
 * @param mixed $host
 *
 * @return bool
 */
function is_host($host): bool
{
    return (
        is_string($host)
            &&
        preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $host)
            &&
        strlen($host) <= 253
            &&
        preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $host)
    );
}

/**
 * Get hostname from URL.
 *
 * ```php
 * $host = host('https://github.com/');
 * // $host => 'github.com'
 * ```
 *
 * @param string $url
 *
 * @return string
 */
function host(string $url): string
{
    $host = strtolower(parse_url($url, PHP_URL_HOST));
    return is_host($host) ? $host : '';
}

/**
 * Get current date in SQL format. Can shift current day using first argument.
 *
 * ```php
 * $today = curdate();
 * // $today => '2017-08-17'
 * $yesterday = curdate(-1);
 * // $yesterday => '2017-08-16'
 * ```
 *
 * @param int $shift_days (optional)
 *
 * @return string
 */
function curdate(int $shift_days = 0): string
{
    return date(SQL_FORMAT_DATE, time() + ($shift_days * 24 * 3600));
}

/**
 * Get current time is SQL format. Can shift current time using first argument.
 *
 * ```php
 * $now = now();
 * // $now => '2017-08-17 11:04:31'
 * $min_ago = now(-60);
 * // $min_ago => '2017-08-17 11:03:31'
 * ```
 *
 * @param int $shift_seconds (optional)
 *
 * @return string
 */
function now(int $shift_seconds = 0): string
{
    return date(SQL_FORMAT_DATETIME, time() + $shift_seconds);
}

/**
 * Split line by line given string. Each line is trimmed, empty ones are filtered out.
 *
 * @param string $string
 *
 * @return array
 */
function nsplit(string $string): array
{
    $string = str_replace("\r", "\n", $string);
    $string = explode("\n", $string);
    $string = array_map('trim', $string);
    $string = array_filter($string);
    return array_values($string);
}

/**
 * Return whether or not the provided object is callable.
 *
 * ```php
 * $is_closure = is_closure(function () { ; });
 * // $is_closure => true
 * ```
 *
 * @param mixed $closure
 *
 * @return bool
 */
function is_closure($closure): bool
{
    return is_callable($closure) && is_object($closure);
}

/**
 * Whether or not provided IP is valid IP.
 *
 * ```php
 * $ip = '127.0.0.1';
 * $is_ip = is_ip($ip);
 * // $is_ip => true
 * $is_ip = is_ip($ip, false);
 * // $is_ip => false
 * ```
 *
 * @param mixed $ip
 * @param bool  $allow_private (optional)
 *
 * @return bool
 */
function is_ip($ip, bool $allow_private = true): bool
{
    return (bool) filter_var(
        is_string($ip) ? $ip : '',
        FILTER_VALIDATE_IP,
        $allow_private ? 0 : (FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
    );
}

/**
 * Validate associative array.
 *
 * ```php
 * $is_assoc = is_assoc([]);
 * // $is_assoc => true
 * $is_assoc = is_assoc([1, 2]);
 * // $is_assoc => false
 * $is_assoc = is_assoc(['key' => 'value']);
 * // $is_assoc => true
 * ```
 *
 * @param mixed $assoc
 *
 * @return bool
 */
function is_assoc($assoc): bool
{
    if (!is_array($assoc)) {
        return false;
    }
    $count0 = count($assoc);
    $count1 = count(array_filter(array_keys($assoc), 'is_string'));
    return $count0 === $count1;
}

/**
 * Validate regular expression.
 *
 * ```php
 * $is_regex = is_regex('invalid');
 * // $is_regex => false
 * $is_regex = is_regex('~\w~');
 * // $is_regex => true
 * ```
 *
 * @param mixed $regex
 *
 * @return bool
 */
function is_regex($regex): bool
{
    if (is_numeric($regex) || !is_string($regex)) {
        return false;
    }
    return (@ (preg_match($regex, '') !== false));
}

/**
 * String replace. Replace is applied only once.
 *
 * ```php
 * $str = str_replace_once('foo', 'bar', 'foo foo');
 * // $str => 'bar foo'
 * ```
 *
 * @param string $needle
 * @param string $replace
 * @param string $haystack
 *
 * @return string
 */
function str_replace_once(string $needle, string $replace, string $haystack): string
{
    @ $pos = strpos($haystack, $needle);
    if ($pos === false) {
        return $haystack;
    }
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}

/**
 * Truncate string to certain length (be default 40 chars).
 *
 * ```php
 * $str = str_truncate('Hello, world!', 5);
 * // $str => 'He...'
 * $str = str_truncate('Hello, world!', 5, true);
 * // $str => 'H...!'
 * ```
 *
 * @param string $string
 * @param int    $length   (optional)
 * @param bool   $center   (optional)
 * @param string $replacer (optional)
 *
 * @return string
 */
function str_truncate(
    string $string,
    int $length = 40,
    bool $center = false,
    string $replacer = '...'
): string
{
    $l = mb_strlen($replacer);
    $length = max($length, $center ? $l + 2 : $l + 1);
    if ($center && mb_strlen($string) > $length) {
        $length -= $l;
        $begin = ceil($length / 2);
        $end = $length - $begin;
        return mb_substr($string, 0, $begin) . $replacer . mb_substr($string, - $end);
    } elseif (!$center && mb_strlen($string) > $length) {
        $length -= $l;
        $begin = $length;
        return mb_substr($string, 0, $begin) . $replacer;
    }
    return $string;
}

/**
 * Get file extension.
 *
 * ```php
 * $ext = file_get_ext('image.PNG');
 * // $ext => 'png'
 * $ext = file_get_ext('archive.tar.gz');
 * // $ext => 'gz'
 * $ext = file_get_ext('/etc/passwd');
 * // $ext => ''
 * $ext = file_get_ext('/var/www/');
 * // $ext => ''
 * ```
 *
 * @param string $file
 *
 * @return string
 */
function file_get_ext(string $file): string
{
    preg_match('~\.([a-z0-9A-Z]{1,5})$~', $file, $match);
    return $match ? strtolower($match[1]) : '';
}

/**
 * Get file name (without extension).
 *
 * ```php
 * $name = file_get_name('image.png');
 * // $name => 'image'
 * $name = file_get_name('archive.tar.gz');
 * // $name => 'archive.tar'
 * $name = file_get_name('/etc/passwd');
 * // $name => 'passwd'
 * $name = file_get_name('/var/www/');
 * // $name => ''
 * ```
 *
 * @param string $file
 *
 * @return string
 */
function file_get_name(string $file): string
{
    if (substr($file, -1) === '/') {
        return '';
    }
    $file = basename($file);
    return preg_replace('~\.([a-z0-9A-Z]{1,5})$~', '', $file);
}

/**
 * Native PHP templating engine.
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
 * echo template('test.tpl', [
 *     'title' => 'Test Title',
 *     'body' => '<h1>Hello!</h1>',
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
 *
 * @param string $tpl
 * @param array  $args (optional)
 *
 * @return string
 */
function template(string $tpl, array $args = []): string
{
    extract($args);
    ob_start();
    include $tpl;
    return ob_get_clean();
}

/**
 * Get tag attributes. Returns list. 
 * If second argument is not null, returns value of this argument 
 * (or null if no such argument).
 *
 * ```php
 * $tag = "<a href='/link.html?a=1&amp;b=2'>";
 * $attributes = get_tag_attributes($tag);
 * // $attributes => ['href' => '/link.html?a=1&b=2']
 * $attribute = get_tag_attributes($tag, 'href');
 * // $attribute => '/link.html?a=1&b=2'
 * $attribute = get_tag_attributes($tag, '_target');
 * // $attribute => null
 * ```
 *
 * @param string $tag
 * @param string $attribute (optional)
 *
 * @return array|string|null
 */
function get_tag_attributes(string $tag, string $attribute = null)
{
    $tag = preg_replace('~^\s*(<\w+\b([^>]*)>).*$~is', '$2', $tag);
    preg_match_all(
        '~\b(?P<attr>[\w-]+)=([\'"]?)(?P<value>.*?)\2(?=\s|>|$)~',
        $tag,
        $matches,
        PREG_SET_ORDER
    );
    $collector = [];
    foreach ($matches as $match) {
        $collector[strtolower($match['attr'])] = esc($match['value'], true);
    }
    return is_null($attribute) ? $collector : ($collector[$attribute] ?? null);
}

/**
 * Prepare attributes for outputing in HTML tag.
 *
 * ```php
 * $attributes = [
 *     'href' => '/link.html?a=1&b=2',
 *     'class' => ['_left', '_clearfix'],
 * ];
 * $prepared = prepare_tag_attributes($attributes);
 * // $prepared => 'href="/link.html?a=1&amp;b=2" class="_left _clearfix"'
 * $attributes = [
 *     'style' => [
 *         'margin-top' => '0',
 *         'display' => 'flex',
 *     ],
 * ];
 * $prepared = prepare_tag_attributes($attributes);
 * // $prepared => 'style="margin-top:0;display:flex;"'
 * ```
 *
 * @param array $attributes
 *
 * @return string
 */
function prepare_tag_attributes(array $attributes): string
{
    $collector = [];
    foreach ($attributes as $k => $v) {
        if ($k === '' || $v === '') {
            continue;
        }
        if (is_assoc($v)) {
            $_collector = [];
            foreach ($v as $_k => $_v) {
                if ($_k === '' || $_v === '') {
                    continue;
                }
                $_collector[] = $_k . ':' . $_v . ';';
            }
            $v = implode('', $_collector);
        } elseif (is_array($v)) {
            $v = implode(' ', array_values(array_filter($v, function ($v) {
                return !is_array($v) && $v !== '';
            })));
        }
        if ($v === '') {
            continue;
        }
        $collector[] = sprintf('%s="%s"', $k, esc($v));
    }
    return implode(' ', $collector);
}

/**
 * Get absolute URL, lead URL to more canonical form. Also operates with files. 
 * `$url` is canonized according to `$relative` (file or URL). In case of error returns empty string.
 *
 * ```php
 * $url = realurl('/link.html', 'http://site.com/');
 * // $url => 'http://site.com/link.html'
 * $url = realurl('http://site.com/archive/2014/../link.html');
 * // $url => 'http://site.com/archive/link.html'
 * $url = realurl('../home.html', 'http://site.com/archive/link.html');
 * // $url => 'http://site.com/home.html'
 * $url = realurl('../new.md', 'path/a/old.md');
 * // $url => 'path/new.md'
 * ```
 *
 * @param string $url
 * @param string $relative (optional)
 *
 * @return string
 */
function realurl(string $url, string $relative = ''): string
{
    $starts = ['#', 'javascript:', 'mailto:', 'skype:', 'data:', 'tel:'];
    foreach ($starts as $start) {
        if (strpos($url, $start) === 0) {
            return '';
        }
    }
    $scheme = function ($_) {
        return parse_url($_, PHP_URL_SCHEME);
    };
    if (!$scheme($url) && host($url)) {
        $s = 'http';
        if (host($relative) && ($_ = $scheme($relative))) {
            $s = $_;
        }
        $url = $s . ':' . $url;
    }
    $normalize = function ($url) {
        $parse = parse_url($url);
        $head = '';
        if (isset($parse['scheme'])) {
            $head = "{$parse['scheme']}://{$parse['host']}";
            $url = substr($url, strlen($head));
            $url = strlen($url) ? $url : '/';
            $url = preg_replace('~\?+$~', '', $url);
        } elseif (!strlen($url)) {
            return '';
        }
        do {
            $old = $url;
            $url = preg_replace('~/{2,}|/\./~', '/', $url);
            $url = preg_replace('~#.*$~', '', $url);
            $url = preg_replace('~(^|/)[^/]+/\.\./~', '$1', $url);
            $url = preg_replace('~^/\.\./~', '/', $url);
        } while ($old != $url);
        return $head . $url;
    };
    if (host($url)) {
        return $normalize($url);
    }
    if (strpos($url, '/') === 0) {
        return $normalize(preg_replace('~(?<!/)/(?!/).*$~', '', $relative) . $url);
    }
    if (strpos($url, '?') === 0) {
        return $normalize(preg_replace('~\?.*$~', '', $relative) . $url);
    }
    return $normalize(preg_replace('~(/)?[^/]+$~', '$1', $relative) . $url);
}

/**
 * Used to set environment variable inside `.env` file. 
 * If you ignore third argument, `.env` file is taken from `DOTENV_FILE` constant.
 *
 * @param string $name
 * @param string $value
 * @param string $file  (optional)
 *
 * @return bool
 */
function setenv(string $name, string $value, string $file = DOTENV_FILE): bool
{
    if (!is_file($file) || !is_writable($file)) {
        return false;
    }
    $name = strtoupper($name);
    $env = file_get_contents($file);
    $name = preg_quote($name, '~');
    $value = ctype_alnum(strtr($value, ['_' => '', '-' => ''])) ? $value : "\"{$value}\"";
    $env = preg_replace_callback("~^({$name}=).*$~im", function ($match) use ($value) {
        return $match[1] . $value;
    }, $env, -1, $count);
    if (!$count) {
        $env .= (rtrim($env) == $env ? "\n" : '') . "{$name}={$value}\n";
    }
    return (bool) file_put_contents($file, $env);
}

/**
 * Used to return a value from translation map. 
 * Function optionally receives secord argument (`LANG`) and third argument (`LANG_FILE`).
 *
 * @param string $var
 * @param string $lang      (optional)
 * @param string $lang_file (optional)
 *
 * @return string
 */
function _T(string $var, string $lang = LANG, string $lang_file = LANG_FILE): string
{
    static $maps = [];
    if (!array_key_exists($lang_file, $maps)) {
        $maps[$lang_file] = require $lang_file;
    }
    $key = $lang . '-' . $var;
    return $maps[$lang_file][$key] ?? '';
}

/**
 * Encode string to URL-safe Base64 format.
 *
 * @param string $string
 *
 * @return string
 */
function url_base64_encode(string $string): string
{
    return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
}

/**
 * Decode from URL-safe Base64 format.
 *
 * @param string $string
 *
 * @return string
 */
function url_base64_decode(string $string): string
{
    $string = base64_decode(
        str_pad(
            strtr($string, '-_', '+/'),
            strlen($string) % 4,
            '=',
            STR_PAD_RIGHT
        )
    );
    return $string === false ? '' : $string;
}

/**
 * XOR encryption.
 *
 * @param string $string
 * @param string $key
 *
 * @return string
 */
function xencrypt(string $string, string $key): string
{
    $string = mt_rand() . ':' . $string . ':' . mt_rand();
    for ($i = 0; $i < strlen($string); $i++) {
        $k = md5($key . (string)(substr($string, $i + 1)) . $i);
        for ($j = 0; $j < strlen($k); $j++) {
            $string[$i] = $string[$i] ^ $k[$j];
        }
    }
    return url_base64_encode($string);
}

/**
 * XOR decryption.
 *
 * @param string $string
 * @param string $key
 *
 * @return string
 */
function xdecrypt(string $string, string $key): string
{
    $string = url_base64_decode($string);
    for ($i = strlen($string) - 1; $i >= 0; $i--) {
        @ $k = md5($key . (string)(substr($string, $i + 1)) . $i);
        for ($j = 0; $j < strlen($k); $j++)
            $string[$i] = $string[$i] ^ $k[$j];
    }
    $string = explode(':', $string, 2);
    if (count($string) != 2 || !is_numeric($string[0])) {
        return '';
    }
    $string = $string[1];
    $pos = strrpos($string, ':');
    return $pos ? substr($string, 0, $pos) : '';
}

/**
 * Implements OpenSSL encryption.
 *
 * @param string $string
 * @param string $key
 *
 * @return string
 */
function oencrypt(string $string, string $key): string
{
    $method = 'aes-256-ofb';
    $iv = substr(md5($key), 0, 16);
    $string = mt_rand() . ':' . $string . ':' . mt_rand();
    $string = openssl_encrypt($string, $method, $key, OPENSSL_RAW_DATA, $iv);
    return url_base64_encode($string);
}

/**
 * Implements OpenSSL decryption.
 *
 * @param string $string
 * @param string $key
 *
 * @return string
 */
function odecrypt(string $string, string $key): string
{
    $method = 'aes-256-ofb';
    $iv = substr(md5($key), 0, 16);
    $string = url_base64_decode($string);
    $string = openssl_decrypt($string, $method, $key, OPENSSL_RAW_DATA, $iv);
    $string = explode(':', $string, 2);
    if (count($string) != 2 || !is_numeric($string[0])) {
        return '';
    }
    $string = $string[1];
    $pos = strrpos($string, ':');
    return $pos ? substr($string, 0, $pos) : '';
}

/**
 * Decode string from Base32 format.
 *
 * @param string $string
 *
 * @return string
 */
function base32_decode(string $string): string
{
    static $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $tmp = [];
    foreach (str_split($string) as $c) {
        $tmp[] = sprintf('%05b', intval(strpos($map, $c)));
    }
    $args = array_map('bindec', str_split(implode('', $tmp), 8));
    return rtrim(pack('C*', ...$args), "\0");
}

/**
 * Encode string in Base32 format.
 *
 * @param string $string
 *
 * @return string
 */
function base32_encode(string $string): string
{
    static $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $output = [];
    $collect = [];
    for ($i = 0; $i < strlen($string); $i++) {
        $collect[] = str_pad(decbin(ord($string[$i])), 8, '0', STR_PAD_LEFT);
    }
    $neededPad = 5 - (count($collect) % 5);
    if ($neededPad > 0 and $neededPad < 5) {
        $collect[] = str_repeat('0', 5 - $neededPad);
    }
    $collect = implode('', $collect);
    foreach (str_split($collect, 5) as $binaryChunk) {
        $output[] = $map[bindec($binaryChunk)];
    }
    return implode('', $output);
}

/**
 * Latinize string. Set `$ru` to `true` in order to latinize also cyrillic characters.
 *
 * ```php
 * $s = latinize('Màl Śir');
 * // $s => 'Mal Sir'
 * $s = latinize('привет мир', true);
 * // $s => 'privet mir'
 * ```
 *
 * @param string $string
 * @param bool   $ru     (optional)
 *
 * @return string
 */
function latinize(string $string, bool $ru = false): string
{
    $strtr = function ($string, $from, $to) {
        preg_match_all('/./u', $from, $keys);
        preg_match_all('/./u', $to, $values);
        return strtr($string, array_combine($keys[0], $values[0]));
    };
    $string = $strtr(
        $string,
        'İ¡¿ÀàÁáÂâÃãÄäÅåÆæçÇÈèÉéÊêËëÌìÍíÎîÏïÐððÑñÒòÓóÔôÕõöÖØøÙùÚúÛûÜüÝýÞþÿŸāĀĂ`',
        'I!?AaAaAaAaAaAaAacCEeEeEeEeIiIiIiIiDdoNnOoOoOoOooOOoUuUuUuUuYyBbyYaAA\''
    );
    $string = $strtr(
        $string,
        'ăąĄćĆĈĉĊċčČďĎĐđēĒĔĕėĖęĘĘěĚĜĝğĞĠġģĢĤĥĦħĨĩīĪĪĬĭįĮıĴĵķĶĶĸĹĺļĻĽľĿŀłŁńŃņŅňŇ',
        'aaAcCCcCccCdDDdeEEeeEeeEeEGggGGggGHhHhIiiiIIiiIiJjkkKkLllLLlLllLnNnNnN'
    );
    $string = $strtr(
        $string,
        'ŉŊŋŌōŎŏŐőŒœŔŕŗřŘśŚŜŝşŞšŠŢţťŤŦŧŨũūŪŪŬŭůŮŰűųŲŴŵŶŷźŹżŻžŽƠơƯưǼǽȘșȚțəƏΐάΆέΈ',
        'nNnOoOoOoOoRrrrRsSSssSsSTttTTtUuuuUUuuUUuuUWwYyzZzZzZOoUuAaSsTteEiaAeE'
    );
    $string = $strtr(
        $string,
        'ήΉίΊΰαΑβΒγΓδΔεΕζΖηΗθΘιΙκΚλΛμΜνΝξΞοΟπΠρΡςσΣτΤυΥφΦχΧωΩϊΪϋΫόΌύΎώΏјЈћЋ',
        'hHiIyaAbBgGdDeEzZhH88iIkKlLmMnN33oOpPrRssStTyYfFxXwWiIyYoOyYwWjjcC'
    );
    $string = $strtr(
        $string,
        'أبتجحدرزسصضطفقكلمنهوي',
        'abtghdrzssdtfkklmnhoy'
    );
    $string = $strtr(
        $string,
        'ẀẁẂẃẄẅẠạẢảẤấẦầẨẩẪẫẬậẮắẰằẲẳẴẵẶặẸẹẺẻẼẽẾếỀềỂểỄễỆệỈỉỊịỌọỎ',
        'WwWwWwAaAaAaAaAaAaAaAaAaAaAaAaEeEeEeEeEeEeEeEeIiIiOoO'
    );
    $string = $strtr(
        $string,
        'ỏỐốỒồỔổỖỗỘộỚớỜờỞởỠỡỢợỤụỦủỨứỪừỬửỮữỰựỲỳỴỵỶỷỸỹ–—“”•‘’',
        'oOoOoOoOoOoOoOoOoOoOoUuUuUuUuUuUuUuYyYyYyYy--""-\'\''
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
 * Normalize string by removing non-English chars. 
 * Can add some extra chars (using `$extra`) and cyrillic chars (using `$ru`).
 *
 * ```php
 * echo normalize("Hello, world!");
 * // => "hello world"
 * echo normalize("John's hat!", $extra = "'");
 * // => "john's hat"
 * echo normalize("Привет, мир!", $extra = "", $ru = true);
 * // => "привет мир"
 * ```
 *
 * @param string $string
 * @param string $extra  (optional)
 * @param bool   $ru     (optional)
 *
 * @return string
 */
function normalize(string $string, string $extra = '', bool $ru = false): string
{
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
 * @param DOMNode $tag
 */
function _xpath_callback_remove(DOMNode $tag)
{
    $tag->parentNode->removeChild($tag);
}

/**
 * @param DOMNode $tag
 */
function _xpath_callback_unwrap(DOMNode $tag)
{
    if ($tag->hasChildNodes()) {
        $collector = [];
        foreach ($tag->childNodes as $child) {
            $collector[] = $child;
        }
        for ($i = 0; $i < count($collector); $i++) {
            $tag->parentNode->insertBefore($collector[$i], $tag);
        }
    }
    $tag->parentNode->removeChild($tag);
}

/**
 * Wrapper around [DOMXPath](http://php.net/manual/en/class.domxpath.php). 
 * Accepts XPath queries for extracting tags and callback function for tag manipulating.
 *
 * ```php
 * $content = file_get_contents("http://github.com/");
 * $metas = xpath($content, "//meta");
 * print_r($metas);
 * ```
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
 * @param DOMNode|string    $xml
 * @param string            $query    (optional)
 * @param callable|int|null $callback (optional)
 * @param array             $flags    (optional)
 *
 * @return array|string
 *
 * @throws Exception
 */
function xpath($xml, string $query = '/*', $callback = null, array $flags = [])
{
    // self-closing tags
    $sct = 'area|base|br|col|command|embed|hr|img|input|keygen|link|meta|param|source|track|wbr';
    $flags = $flags + [
        'preserve_white_space' => false,
        'ignore_fix' => false,
        'implode' => false,
    ];
    if (is_string($xml) && !$flags['ignore_fix']) {
        // FIX HTML TO BE COMPATIBLE WITH XML
        $xml = preg_replace_callback("~<({$sct})\b[^>]*>~", function ($m) {
            $last = mb_substr($m[0], -2);
            return $last == '/>' ? $m[0] : rtrim(mb_substr($m[0], 0, -1)) . ' />';
        }, $xml);
        $xml = preg_replace_callback('~<html\b[^>]*xmlns[^>]*>~', function ($m) {
            $attrs = get_tag_attributes($m[0]);
            unset($attrs['xmlns']);
            $attrs = prepare_tag_attributes($attrs);
            return "<html {$attrs}>";
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
    if (!$tags instanceof DOMNodeList) {
        throw new Exception(__FUNCTION__ . ': Invalid query: "' . $query . '"!');
    }
    if (is_int($callback)) {
        return $doc->saveXML($tags->item(
            $callback < 0 ? $callback + count($tags) : $callback
        ));
    }
    if (is_callable($callback)) {
        $callback = function ($tag) use ($callback) {
            $_ = $tag;
            while (isset($_->parentNode)) {
                $_ = $_->parentNode;
            }
            if ($_ instanceof DOMDocument) {
                $callback($tag);
            }
        };
        $tags = iterator_to_array($tags, false);
        for ($i = 0, $len = count($tags); $i < $len; $i++) {
            $callback($tags[$i]);
        }
        return $doc->saveXML($doc->documentElement);
    }
    $return = [];
    foreach ($tags as $tag) {
        $return[] = $doc->saveXML($tag);
    }
    if (func_num_args() === 1 || $flags['implode']) {
        return implode('', $return);
    }
    if (preg_match('~@(\w+)$~', $query, $match)) {
        return array_map(function ($return) use ($match) {
            return get_tag_attributes($return, $match[1]);
        }, $return);
    }
    return $return;
}

/**
 * Upper case of first letter.
 *
 * @param string $string
 *
 * @return string
 */
function mb_ucfirst(string $string): string
{
    $len = mb_strlen($string);
    $first = mb_substr($string, 0, 1);
    $then = mb_substr($string, 1, $len - 1);
    return mb_strtoupper($first) . $then;
}

/**
 * Satinize HTML output.
 *
 * @param string $content
 *
 * @return string
 */
function sanitize_html(string $content): string
{
    $map = [];
    $content = trim($content);
    $ws_reg = '~(?P<begin><(textarea|pre|script)\b[^>]*?>)(?P<body>.*?)(?P<end></\\2\b[^>]*?>)~is';
    $content = preg_replace_callback($ws_reg, function ($match) use (&$map) {
        $key = count($map);
        $map[$key] = trim($match['body']);
        return $match['begin'] . $key . $match['end'];
    }, $content);
    $content = preg_replace('~<!--.*?-->~s', '', $content);
    $content = preg_replace('~\s+~s', ' ', $content);
    $reg = '~(?P<b><)(?P<c>/?)(?P<t>\w+)\b(?P<a>[^>]*?)\s*(?P<e>/?>)~';
    $parts = preg_split($reg, $content, -1, PREG_SPLIT_DELIM_CAPTURE);
    $parts = array_chunk($parts, 6);
    $inline = array_flip([
        'a', 'span', 'b', 'strong', 'i', 'em', 'code', 's',
        'img', 'small', 'big', 'sub', 'sup',
        'ins', 'del', 'kbd',
    ]);
    $sc = array_flip(['hr', 'br']);
    $ex = [];
    $content = [];
    for ($i = 0, $l = count($parts); $i < $l; $i++) {
        if ($i == $l - 1) {
            $s = $parts[$i][0];
            if ($s === '') {
                continue;
            }
            [$b, $c, $t, $a, $e] = ['', '/', '', '', ''];
        } else {
            [$s, $b, $c, $t, $a, $e] = $parts[$i];
        }
        $is_inline = isset($inline[$t]);
        $is_sc = isset($sc[$t]);
        $is_block = !$is_inline && !$is_sc;
        $is_close = !empty($c);
        $is_ex_inline = isset($ex[1], $inline[$ex[1]]);
        $is_ex_sc = isset($ex[1], $sc[$ex[1]]);
        $is_ex_block = isset($ex[1]) && !$is_ex_inline && !$is_ex_sc;
        $is_ex_close = isset($ex[1]) && $ex[0];
        if ($is_ex_inline && !$is_ex_close) {
            $s = ltrim($s);
        }
        if ($is_inline && $is_close) {
            $s = rtrim($s);
        }
        if ($is_sc) {
            $s = rtrim($s);
        }
        if ($is_ex_sc) {
            $s = ltrim($s);
        }
        if ($is_block) {
            $s = rtrim($s);
        }
        if ($is_ex_block) {
            $s = ltrim($s);
        }
        $content[] = $s . $b . ($t === '' ? '' : $c) . $t . $a . $e;
        $ex = [$c, $t];
    }
    $content = implode('', $content);
    $content = preg_replace_callback($ws_reg, function ($match) use ($map) {
        return $match['begin'] . $map[$match['body']] . $match['end'];
    }, $content);
    return $content;
}

/**
 * Transforms readable form of string to variable.
 *
 * @param string $input
 *
 * @return mixed
 */
function readable_to_variable(string $input)
{
    $self = __FUNCTION__;
    if (is_null($input)) {
        return null;
    }
    if ($input === '') {
        return '';
    }
    $lower = strtolower($input);
    if ($lower === 'true') {
        return true;
    }
    if ($lower === 'false') {
        return false;
    }
    if ($lower === 'null') {
        return null;
    }
    if (defined($input)) {
        return constant($input);
    }
    if (is_float($input)) {
        return floatval($input);
    }
    if (is_numeric($input)) {
        return intval($input);
    }
    if (preg_match('~^\[(.*)\]$~s', $input, $match)) {
        $match[1] = trim($match[1]);
        if ($match[1] === '') {
            return [];
        }
        $array = [];
        $kvs = preg_split('~\s*,\s*~', $match[1]);
        foreach ($kvs as $kv) {
            $kv = explode('=>', $kv, 2);
            if (count($kv) == 2) {
                $array[$self($kv[0])] = $self($kv[1]);
            } else {
                $array[] = $self($kv[0]);
            }
        }
        return $array;
    }
    return $input;
}

/**
 * Help function for saving data in storage.
 *
 * ```php
 * $tmp = rtrim(`mktemp`);
 * $file = to_storage($tmp);
 * // $file => '/tmp/tmp.qmviqzrrd1'
 * $tmp = rtrim(`mktemp`);
 * $file = to_storage($tmp, ['shards' => 2, 'ext' => 'txt']);
 * // $file => '/tmp/ac/bd/tmp.jwueqsppoz.txt'
 * ```
 *
 * For more examples, please, refer to [to_storage.md](docs/to_storage.md).
 *
 * @todo Reformat
 *
 * @param string $file
 * @param array  $settings (optional)
 *
 * @return string
 */
function to_storage(string $file, array $settings = []): string
{
    if (!is_file($file)) {
        return '';
    }
    $settings = $settings + [
        'dir' => sys_get_temp_dir(),
        'ext' => file_get_ext($file),
        'name' => file_get_name($file),
        'shards' => 0,
    ];
    if (!is_dir($settings['dir'])) {
        return '';
    }
    $hash = md5_file($file);
    if ($settings['shards'] > 2) $settings['shards'] = 2;
    $settings['dir'] = rtrim($settings['dir'], '/');
    if ($settings['shards']) {
        $settings['dir'] .= preg_replace('~^(..)(..).*~', $settings['shards'] > 1 ? '/$1/$2' : '/$1', $hash);
    }
    if (!is_dir($settings['dir'])) {
        mkdir($settings['dir'], 0755, true);
    }
    if (!is_dir($settings['dir'])) {
        return '';
    }
    $settings['name'] = normalize($settings['name'], '.-_');
    if (!$settings['name']) {
        $settings['name'] = mt_rand();
    }
    $ext = ($settings['ext'] ? ".{$settings['ext']}" : "");
    $target = $settings['dir'] . '/' . $settings['name'] . $ext;
    $i = 0;
    while (file_exists($target) && md5_file($target) !== $hash) {
        $target = preg_replace($i ? '~-\d+(\.?\w+)$~' : '~(\.?\w+)$~', '-' . $i . '$1', $target);
        $i++;
    }
    copy($file, $target);
    return $target;
}

/**
 * All-in-one cURL function with multi threading support.
 *
 * ```php
 * $result = curl(['http://github.com']);
 * [$github] = iterator_to_array($result, true);
 * preg_match('~<title>(.*?)</title>~', $github['content'], $title);
 * $title = $title[1];
 * // $title => 'The world&#39;s leading software development platform · GitHub'
 * ```
 *
 * For more examples, please, refer to [curl.md](docs/curl.md).
 *
 * @param array $urls
 * @param array $settings (optional)
 *
 * @return Generator
 */
function curl(array $urls, array $settings = []): Generator
{
    $settings = $settings + ['threads' => 5];
    $settings['threads'] = max($settings['threads'], 1);
    $settings['threads'] = min($settings['threads'], 100);
    $get_headers = function (string $header): array {
        $header = trim($header);
        if ($header === '') {
            return [];
        }
        $header = str_replace("\r\n", "\n", $header);
        $headers = [];
        $lines = explode("\n\n", $header);
        for ($index = 0; $index < count($lines); $index++) {
            foreach (explode("\n", $lines[$index]) as $i => $line) {
                if (!$i) {
                    $line = explode(' ', $line);
                    if ($line[1] ?? '') {
                        $headers[$index]['status'] = (int) $line[1];
                    }
                } else {
                    $line = explode(': ', $line, 2);
                    if (count($line) != 2) {
                        continue;
                    }
                    [$k, $v] = $line;
                    $headers[$index][strtolower($k)] = $v;
                }
            }
        }
        return $headers;
    };
    $process_chs = function (array $chs, array $settings) use ($get_headers) {
        foreach ($chs as $key => $ch) {
            $value = $ch['value'];
            $opts = $ch['opts'];
            $ch = $ch['ch'];
            $info = curl_getinfo($ch);
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            if (!$info) {
                continue;
            }
            $content = curl_multi_getcontent($ch);
            curl_close($ch);
            $header_size = (int) $info['header_size'];
            $header = '';
            if ($header_size > 0) {
                $header = substr($content, 0, $header_size);
                $content = substr($content, $header_size);
            }
            $headers = $get_headers($header);
            yield $key => compact([
                'value',
                'content',
                'header',
                'headers',
                'error',
                'errno',
            ]) + $info + $opts;
        }
    };
    $get_opts = function ($key, $value, array $settings): array {
        static $constants;
        static $constantsStrings;
        static $constantsValues;
        if (!is_array($constants)) {
            $constants = array_keys(get_defined_constants());
        }
        if (!is_array($constantsStrings)) {
            $constantsStrings = array_values(array_filter($constants, function ($constant) {
                return strpos($constant, 'CURLOPT_') === 0;
            }));
        }
        if (!is_array($constantsValues)) {
            $constantsValues = array_map('constant', $constantsStrings);
        }
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => '',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1) Chrome/60',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HEADER => true,
        ];
        if (is_string($value) && host($value)) {
            $opts[CURLOPT_URL] = $value;
        }
        if (is_callable($settings['opts'] ?? '')) {
            $opts = $settings['opts']($value, $key) + $opts;
        } elseif (is_assoc($settings['opts'] ?? '')) {
            $opts = $settings['opts'] + $opts;
        }
        $acceptCallable = [
            CURLOPT_HEADERFUNCTION,
            CURLOPT_PROGRESSFUNCTION,
            CURLOPT_READFUNCTION,
            CURLOPT_WRITEFUNCTION,
        ];
        if (defined('CURLOPT_PASSWDFUNCTION')) {
            $acceptCallable[] = CURLOPT_PASSWDFUNCTION;
        }
        foreach ($settings as $k => $v) {
            if (in_array($k, $constantsStrings)) {
                $k = constant($k);
            }
            if (!in_array($k, $constantsValues)) {
                continue;
            }
            if (!in_array($k, $acceptCallable) && is_callable($v)) {
                $v = $v($value, $key);
            }
            $opts[$k] = $v;
        }
        return host($opts[CURLOPT_URL] ?? '') ? $opts : [];
    };
    while (count($urls)) {
        $chs = [];
        $multi = curl_multi_init();
        for ($i = 0; $i < $settings['threads'] && count($urls); $i++) {
            $key = key($urls);
            $opts = $get_opts($key, $urls[$key], $settings);
            if (!$opts) {
                unset($urls[$key]);
                $i--;
                continue;
            }
            $ch = curl_init();
            curl_setopt_array($ch, $opts);
            $value = $urls[$key];
            $chs[$key] = compact('ch', 'value', 'opts');
            curl_multi_add_handle($multi, $ch);
            unset($urls[$key]);
        }
        do {
            curl_multi_exec($multi, $running);
            usleep(250000);
        } while ($running > 0);
        curl_multi_close($multi);
        yield from $process_chs($chs, $settings);
    }
}

/**
 * Parses ~/.ssh/config content. 
 * Returns associative array where Host comes as key.
 *
 * @param string $content
 *
 * @return array
 */
function parse_ssh_config(string $content): array
{
    $hosts = [];
    $current = '';
    foreach (explode("\n", $content) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        [$key, $value] = preg_split('~\s+~', $line, 2);
        if (strtolower($key) === 'host') {
            $current = $value;
            $hosts[$current] = [];
        } elseif ($current !== '') {
            $hosts[$current][$key] = $value;
        }
    }
    return $hosts;
}

/**
 * Returns [
 *     'suffix' => '*.bd',
 *     'suffix_match' => 'mil.bd',
 *     'domain' => 'army',
 *     'tld' => 'bd',
 * ] for 'army.mil.bd'
 *
 * @param string $domain
 *
 * @return array
 */
function get_domain_info(string $domain): array
{
    static $list_0; // direct
    static $list_1; // wildcard
    static $list_2; // exclusions
    if (!is_host($domain)) {
        return [];
    }
    $domain = strtolower($domain);
    if (!$list_0) {
        $suffix_link = 'https://publicsuffix.org/list/effective_tld_names.dat';
        $expire = 3600 * 24 * 30; // 30 day cache
        $file = sys_get_temp_dir() . '/' . __FUNCTION__ . '.tmp';
        $list = nsplit(is_file($file) ? file_get_contents($file) : '');
        if (!is_file($file) || filemtime($file) < time() - $expire || !$list) {
            $list = file_get_contents($suffix_link);
            $list = nsplit($list);
            foreach ($list as &$elem) {
                if ($elem[0] === '#' || $elem[0] === '/') {
                    $elem = '';
                }
            }
            $list = array_filter($list);
            file_put_contents($file, implode("\n", $list) . "\n");
            $list = nsplit(file_get_contents($file));
        }
        foreach ($list as $elem) {
            $parts = explode('.', $elem);
            if ($parts[0] === '*') {
                $list_1[implode('.', array_slice($parts, 1))] = true;
            } elseif ($parts[0][0] === '!') {
                $list_2[substr($elem, 1)] = true;
            } else {
                $list_0[$elem] = true;
            }
        }
    }
    $parts = explode('.', $domain);
    $check = [];
    for ($i = 0; $i < count($parts); $i++) {
        $suffix = implode('.', array_slice($parts, $i));
        if (isset($list_0[$suffix])) {
            $suffix_match = $suffix;
            [$tld] = array_reverse(explode('.', $suffix_match));
            $domain = $parts[$i - 1] ?? '';
            if ($domain === '') {
                return [];
            }
            $subdomain = $i > 1 ? ['subdomain' => implode('.', array_slice($parts, 0, $i - 1))] : [];
            return compact('suffix', 'suffix_match', 'domain', 'tld') + $subdomain;
        }
        $suffix_m = implode('.', array_slice($parts, $i + 1));
        if (isset($list_2[$suffix])) {
            $suffix = $suffix_m;
            $suffix_match = $suffix;
            [$tld] = array_reverse(explode('.', $suffix_match));
            $domain = $parts[$i] ?? '';
            if ($domain === '') {
                return [];
            }
            $subdomain = $i > 0 ? ['subdomain' => implode('.', array_slice($parts, 0, $i))] : [];
            return compact('suffix', 'suffix_match', 'domain', 'tld') + $subdomain;
        }
        $suffix = $suffix_m;
        if (isset($list_1[$suffix])) {
            $suffix_match = $parts[$i] . '.' . $suffix;
            $suffix = '*.' . $suffix;
            [$tld] = array_reverse(explode('.', $suffix_match));
            $domain = $parts[$i - 1] ?? '';
            if ($domain === '') {
                return [];
            }
            $subdomain = $i > 1 ? ['subdomain' => implode('.', array_slice($parts, 0, $i - 1))] : [];
            return compact('suffix', 'suffix_match', 'domain', 'tld') + $subdomain;
        }
    }
    return [];
}

/**
 * Returns true, if domains have same suffix.
 *
 * @param string $domain1
 * @param string $domain2
 *
 * @return bool
 */
function is_same_suffix_domains(string $domain1, string $domain2): bool
{
    if ($domain1 === '' || $domain2 === '') {
        return false;
    }
    if (
        strpos('.' . $domain1, '.' . $domain2) !== false ||
        strpos('.' . $domain2, '.' . $domain1) !== false
    ) {
        return true;
    }
    $suffix1 = get_domain_info($domain1);
    $suffix2 = get_domain_info($domain2);
    return (
        $suffix1 && $suffix2 &&
        $suffix1['domain'] == $suffix2['domain'] &&
        $suffix1['suffix_match'] == $suffix2['suffix_match']
    );
}

/**
 * Extract links (anchors + resource) from HTML.
 *
 * @param string $html
 * @param string $url  (optional)
 *
 * @return array
 */
function extract_links_from_html(string $html, string $url = ''): array
{
    $html = trim($html);
    if (!$html) {
        return [];
    }
    $links = [];
    $map = [
        'a' => 'href',
        'link' => 'href',
        'img' => 'src',
        'script' => 'src',
        'form' => 'action',
        'iframe' => 'src',
        'area' => 'href',
        'source' => 'src',
        'embed' => 'src',
        'video' => 'src',
        'track' => 'src',
        'object' => 'data',
    ];
    [$head, $tail, $glue] = ['self::', '', ' or '];
    $tags = $head . implode($tail . $glue . $head, array_keys($map)) . $tail;
    [$head, $tail, $glue] = ['name()="', '"', ' or '];
    $names = $head . implode($tail . $glue . $head, array_unique(array_values($map))) . $tail;
    $xpath = "//*[{$tags}][./@*[{$names}]]";
    $base = xpath($html, '//head/base/@href');
    $base = $base ? realurl($base[0], $url) : $url;
    xpath($html, $xpath, function ($tag) use (&$links, $base, $map) {
        $is_link = $tag->nodeName == 'link';
        $is_a = $tag->nodeName == 'a';
        $is_form = $tag->nodeName == 'form';
        $value = trim($tag->getAttribute($map[$tag->nodeName]));
        if ($value === '') {
            return;
        }
        $value = realurl($value, $base);
        if ($value === '') {
            return;
        }
        if ($is_a && isset($links[$value])) {
            return;
        }
        $links[$value] = ['tag' => $tag->nodeName];
        if ($is_link && $rel = strtolower($tag->getAttribute('rel'))) {
            $links[$value]['rel'] = $rel;
        }
        if ($is_link && $type = strtolower($tag->getAttribute('type'))) {
            $links[$value]['type'] = $type;
        }
        if ($is_form && $method = strtolower($tag->getAttribute('method'))) {
            $links[$value]['method'] = $method;
        }
        if ($is_a && $anchor = normalize(latinize($tag->nodeValue), '', true)) {
            $links[$value]['anchor'] = $anchor;
        }
    });
    return $links;
}

/**
 * Extract all `<meta/>` tags from HTML.
 *
 * @param string $html
 *
 * @return array
 */
function extract_metas_from_html(string $html): array
{
    $metas = [];
    $attrs = ['charset', 'content', 'http-equiv', 'name', 'scheme', 'property'];
    [$head, $tail, $glue] = ['name()="', '"', ' or '];
    $names = $head . implode($tail . $glue . $head, $attrs) . $tail;
    $xpath = "//head/meta[./@*[{$names}]]";
    xpath($html, $xpath, function ($tag) use (&$metas, $attrs) {
        $collect = [];
        foreach ($attrs as $attr) {
            $value = $tag->getAttribute($attr);
            if ($value === '') {
                continue;
            }
            $collect[$attr] = $value;
        }
        if ($collect) {
            $metas[] = $collect;
        }
    });
    return $metas;
}

/**
 * Extract comments from HTML.
 *
 * @param string $html
 *
 * @return array
 */
function extract_comments_from_html(string $html): array
{
    $xpath = '//comment()';
    $comments = xpath($html, $xpath);
    $comments = array_map(function ($comment) {
        $comment = preg_replace('~^<!--\s*|\s*-->$~s', '', $comment);
        return $comment;
    }, $comments);
    $comments = array_values(array_filter($comments, function ($comment) {
        return $comment !== '';
    }));
    return $comments;
}

/**
 * Extract scripts from HTML.
 *
 * @param string $html
 *
 * @return array
 */
function extract_scripts_from_html(string $html): array
{
    $xpath = '//script/text()';
    $scripts = xpath($html, $xpath);
    $scripts = array_map(function ($script) {
        $script = trim($script);
        if (strpos($script, $_ = '<![CDATA[') === 0) {
            $script = substr($script, strlen($_));
        }
        if (substr($script, -3) === ']]>') {
            $script = substr($script, 0, strlen($script) - 3);
        }
        $script = trim($script);
        return $script;
    }, $scripts);
    $scripts = array_values(array_filter($scripts, function ($script) {
        return $script !== '';
    }));
    return $scripts;
}

/**
 * Extract text values from HTML.
 *
 * @param string $html
 *
 * @return array
 */
function extract_texts_from_html(string $html): array
{
    $xpath = '//*[not(self::script or self::style)]/text()';
    $texts = xpath($html, $xpath);
    $texts = array_map(function ($text) {
        $text = trim($text);
        $text = preg_replace('~\s+~', ' ', $text);
        return $text;
    }, $texts);
    $texts = array_values(array_filter($texts, function ($text) {
        return $text !== '';
    }));
    return $texts;
}

/**
 * Extract all `<input/>` tags from HTML.
 *
 * @param string $html
 *
 * @return array
 */
function extract_inputs_from_html(string $html): array
{
    $inputs = [];
    $attrs = ['type', 'name', 'value', 'accept', 'placeholder'];
    [$head, $tail, $glue] = ['name()="', '"', ' or '];
    $names = $head . implode($tail . $glue . $head, $attrs) . $tail;
    $xpath = "//input[./@*[{$names}]]";
    xpath($html, $xpath, function ($tag) use (&$inputs, $attrs) {
        $collect = [];
        foreach ($attrs as $attr) {
            $value = $tag->getAttribute($attr);
            if ($value === '') {
                continue;
            }
            $collect[$attr] = $value;
        }
        if ($collect) {
            $inputs[] = $collect;
        }
    });
    return $inputs;
}

/**
 * Extract possible keywords from HTML.
 *
 * @param string $html
 * @param array  $tags (optional)
 *
 * @return array
 */
function extract_keywords_from_html(string $html, array $tags = ['title', 'h1', 'meta']): array
{
    $meta_index = array_search('meta', $tags);
    if ($have_meta = $meta_index !== false) {
        unset($tags[$meta_index]);
    }
    [$head, $tail, $glue] = ['self::', '', ' or '];
    $tags = $head . implode($tail . $glue . $head, $tags) . $tail;
    $xpath = "//*[{$tags}]";
    $keywords = [];
    xpath($html, $xpath, function ($tag) use (&$keywords) {
        $text = normalize(latinize($tag->nodeValue), '', true);
        $keywords[$tag->nodeName][] = $text;
    });
    $keywords = array_map(function ($keywords) {
        return implode(' ', $keywords);
    }, $keywords);
    if ($have_meta) {
        $metas = extract_metas_from_html($html);
        foreach ($metas as $meta) {
            if (!in_array($meta['name'] ?? '', ['description', 'keywords'])) {
                continue;
            }
            $keywords['meta'][] = $meta['content'] ?? '';
        }
        if (isset($keywords['meta'])) {
            $keywords['meta'] = normalize(latinize(implode(' ', $keywords['meta'])), '', true);
            if ($keywords['meta'] === '') {
                unset($keywords['meta']);
            }
        }
    }
    return $keywords;
}

/**
 * Extract all possible objects from HTML.
 *
 * @param string $html
 * @param string $url  (optional)
 * @param array  $tags (optional)
 *
 * @return array
 */
function extract_all_from_html(
    string $html,
    string $url = '',
    array $tags = ['title', 'h1', 'meta']
): array
{
    $all = [];
    $all['links'] = extract_links_from_html($html, $url);
    $objects = ['metas', 'comments', 'scripts', 'texts', 'inputs'];
    foreach ($objects as $object) {
        $call = 'extract_' . $object . '_from_html';
        $all[$object] = $call($html);
    }
    $all['keywords'] = extract_keywords_from_html($html, $tags);
    return $all;
}

/**
 * Crawl recursively a domain.
 *
 * @param array $urls
 * @param array $settings (optional)
 *
 * @return array
 */
function crawler(array $urls, array $settings = []): array
{
    $follow_exts = [
        'html', 'htm', 'shtml', 'xhtml',
        'php', 'asp', 'aspx', 'jsp',
        'pl', 'cgi', 'cfml', 'md',
    ];
    $possible_hosts = array_values(array_filter(array_map('host', $urls)));
    $settings = $settings + [
        'threads' => 5,
        'dir' => sys_get_temp_dir(),
        'level' => 10,
        'filter_links' => function ($link, $meta) use ($follow_exts, $possible_hosts) {
            if (($meta['tag'] ?? '') != 'a') {
                return false;
            }
            if (!in_array(host($link), $possible_hosts)) {
                return false;
            }
            $ext = file_get_ext($link);
            return $ext === '' || in_array($ext, $follow_exts);
        },
        'acceptable_http_codes' => [200, 301, 302],
        'echo' => defined('STDIN'),
        'already' => [],
    ];
    $settings['level'] = max(0, $settings['level']);
    $settings['level'] = min($settings['level'], 100);
    if (!$settings['level']) {
        return [];
    }
    if (!is_dir($settings['dir']) || !is_writable($settings['dir'])) {
        return [];
    }
    $echo = function ($url, $msg) use ($settings) {
        if ($settings['echo']) {
            echo sprintf('[%s] [%d] %s: %s', now(), $settings['level'], $url, $msg), "\n";
        }
    };
    $get_path = function ($url, $mkdir) use ($settings) {
        $host = host($url);
        $path = parse_url($url, PHP_URL_PATH);
        $path = normalize(latinize($path, true));
        if (!$path) {
            $path = 'index.html';
        }
        $path = str_replace(' ', '-', $path);
        $md5 = substr(md5($url), 0, 6);
        $path = "{$settings['dir']}/{$host}/{$path}-{$md5}";
        if ($mkdir) {
            @ mkdir($path, 0777, true);
        }
        return $path;
    };
    $return = [];
    $already = [];
    $collect = [];
    clearstatcache();
    foreach ($urls as $url) {
        if (isset($settings['already'][$url])) {
            continue;
        }
        $settings['already'][$url] = true;
        if (file_exists($get_path($url, false))) {
            $already[$url] = true;
        } else {
            $collect[$url] = true;
        }
    }
    $urls = array_keys($collect);
    $collect = [];
    $return['already'] = count($already);
    $iterator = new AppendIterator();
    $iterator->append(new ArrayIterator(array_keys($already)));
    $iterator->append(curl($urls, $settings));
    foreach ($iterator->valid() ? $iterator : [] as $result) {
        $is_already = !isset($result[CURLOPT_URL]);
        if (!$is_already) {
            if ($result['errno']) {
                @ $return['error']++;
                $echo($result['value'], $result['error']);
                continue;
            }
            $echo($result['value'], $result['http_code']);
            if (!in_array($result['http_code'], $settings['acceptable_http_codes'])) {
                @ $return['invalid_code']++;
                continue;
            }
            @ $return['ok']++;
            $links = extract_links_from_html($result['content'], $result['url']);
            $path1 = $get_path($result['url'], true);
            file_put_contents($path1 . '/content.html', $result['content']);
            file_put_contents($path1 . '/header.txt', $result['header']);
            $json = json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($path1 . '/links.json', $json);
            $path2 = $get_path($result['value'], false);
            if ($path1 != $path2) {
                $echo($result['value'], '.. Symlink -> ' . $result['url']);
                symlink($path1, $path2);
            }
        }
        $url = $is_already ? $result : $result['value'];
        $path = $get_path($url, false);
        @ $links = json_decode(file_get_contents($path . '/links.json'), true);
        $old_count = count($collect);
        foreach ($links ?: [] as $link => $meta) {
            if (isset($settings['already'][$link])) {
                continue;
            }
            if (isset($collect[$link])) {
                continue;
            }
            if (!$settings['filter_links']($link, $meta)) {
                continue;
            }
            $collect[$link] = true;
        }
        $new_count = count($collect);
        $echo($url, sprintf('.. links %s / %s', $new_count - $old_count, $new_count));
    }
    $settings['level']--;
    $crawled = crawler(array_keys($collect), $settings);
    foreach (array_keys($return + $crawled) as $key) {
        $return[$key] = ($return[$key] ?? 0) + ($crawled[$key] ?? 0);
    }
    return array_filter($return);
}

/**
 * 1-threaded implementation of BLAST algorithm. 
 * Supports multiple strings.
 *
 * @param array $strings
 * @param int   $m
 * @param array $settings (optional)
 *
 * @return array
 */
function quick_blast(array $strings, int $m, array $settings = []): array
{
    $settings = $settings + [
        'tokenizer' => null,
        'delimiter' => null,
        'unique_substrings' => false,
    ];
    $tokenizer = $settings['tokenizer'];
    $delimiter = $settings['delimiter'];
    $unique_substrings = $settings['unique_substrings'];
    $c = count($strings);
    $s_map = [];
    if ($c < 2) {
        return [];
    }
    $strings = array_values($strings);
    if (is_regex($tokenizer)) {
        $regex = $tokenizer;
        $tokenizer = function ($s) use ($regex) {
            $s = mb_strtolower($s);
            preg_match_all($regex, $s, $matches, PREG_OFFSET_CAPTURE);
            $matches = $matches[0];
            foreach ($matches as &$match) {
                [$token, $pos] = $match;
                $match = compact('token', 'pos');
            }
            unset($match);
            return $matches;
        };
    }
    if (is_callable($tokenizer)) {
        $crc32 = function ($token) {
            $chars = str_split(str_pad(dechex(crc32($token)), 8, '0', STR_PAD_LEFT), 2);
            return implode(array_map(function ($hex) {
                return chr(hexdec($hex));
            }, $chars));
        };
        $delimiter_len = strlen($delimiter);
        foreach ($strings as &$string) {
            $string = $delimiter ? explode($delimiter, $string) : [$string];
            $map = [];
            $shift = 0;
            foreach ($string as &$part) {
                $tokens = $tokenizer($part);
                if ($shift > 0) {
                    foreach ($tokens as &$token) {
                        $token['pos'] += $shift;
                    }
                }
                unset($token);
                $map[] = $tokens;
                foreach ($tokens as &$token) {
                    $token = $token === "\x00\x00\x00\x00" ? $token : $crc32($token['token']);
                }
                unset($token);
                $shift += strlen($part) + $delimiter_len;
                $part = implode($tokens);
                $map[] = [[]];
            }
            unset($part);
            array_pop($map);
            $s_map[] = array_merge(...$map);
            $string = implode("\x00\x00\x00\x00", $string);
        }
        unset($string);
    }
    $prev = null;
    $merge = function ($prev, $current) {
        $merged = [];
        foreach ($prev as $p) {
            $p_len = $p[0];
            $p_s2i = $p[count($p) - 1];
            foreach ($current as $c) {
                [$c_len, $c_s2i, $c_s3i] = $c;
                if ($p_s2i == $c_s2i) {
                    $add = $p;
                    $add[] = $c_s3i;
                    $add[0] = min($p_len, $c_len);
                    $merged[] = $add;
                    continue;
                }
                $diff = $c_s2i - $p_s2i;
                if ($diff > 0 && $p_len > $diff) {
                    $add = $p;
                    $add[0] -= $diff;
                    for ($i = 1, $c = count($add); $i < $c; $i++) {
                        $add[$i] += $diff;
                    }
                    $add[] = $c_s3i;
                    $add[0] = min($add[0], $c_len);
                    $merged[] = $add;
                    continue;
                }
                if ($diff < 0 && $c_len > -$diff) {
                    $add = $p;
                    $add[] = $c_s3i + (-$diff);
                    $add[0] = min($p_len, $c_len + $diff);
                    $merged[] = $add;
                    continue;
                }
            }
        }
        return $merged;
    };
    $is_s_map = (bool) $s_map;
    $_x0 = $is_s_map ? "\x00\x00\x00\x00" : "\x00";
    for ($g = 0; $g < $c - 1; $g++) {
        $found = [];
        [$s1, $s2] = array_slice($strings, $g, 2);
        if ($delimiter && !$is_s_map) {
            $s1 = strtr($s1, [$delimiter => "\x00"]);
            $s2 = strtr($s2, [$delimiter => "\x00"]);
        }
        $l1 = strlen($s1);
        $l2 = strlen($s2);
        if ($l1 < $m || $l2 < $m || $m < 1) {
            return [];
        }
        $switch_s1_s2 = false;
        if ($l1 > $l2) {
            [$s1, $s2] = [$s2, $s1];
            [$l1, $l2] = [$l2, $l1];
            $switch_s1_s2 = true;
        }
        $projection = $s2_hash = [];
        $step = $is_s_map ? 4 : 1;
        $m *= $step;
        $_s1 = $s1;
        $_s2 = $s2;
        $s1 = $delimiter ? explode($_x0, $s1) : [$s1];
        $s2 = $delimiter ? explode($_x0, $s2) : [$s2];
        $is_triggered = false;
        foreach (array_intersect($s1, $s2) as $common) {
            $len = strlen($common);
            if ($len < $m) {
                continue;
            }
            $is_triggered = true;
            while (($pos1 = strpos($_s1, $common)) !== false) {
                $_s1 = substr_replace(
                    $_s1,
                    str_repeat($_x0, $len / $step),
                    $pos1,
                    $len
                );
                while (($pos2 = strpos($_s2, $common)) !== false) {
                    $_s2 = substr_replace(
                        $_s2,
                        str_repeat($_x0, $len / $step),
                        $pos2,
                        $len
                    );
                    $found[] = [$len / $step, $pos1 / $step, $pos2 / $step];
                }
            }
        }
        if ($is_triggered) {
            $s1 = $delimiter ? explode($_x0, $_s1) : [$_s1];
            $s2 = $delimiter ? explode($_x0, $_s2) : [$_s2];
        }
        $index = 0;
        foreach ($s2 as $s) {
            $old_index = $index;
            $l = strlen($s);
            for ($i = 0; $i <= $l - $m; $i += $step) {
                $s2_hash[substr($s, $i, $m)][$index++] = true;
            }
            $index += $old_index === $index ? (($l / $step) + 1) : $m / $step;
        }
        $index = 0;
        foreach ($s1 as $s) {
            $old_index = $index;
            $l = strlen($s);
            for ($i = 0; $i <= $l - $m; $i += $step) {
                $projection[$index++] = $s2_hash[substr($s, $i, $m)] ?? [];
            }
            $index += $old_index === $index ? (($l / $step) + 1) : $m / $step;
        }
        $m /= $step;
        @ $count = max(array_keys($projection));
        foreach ($projection as $i => $proj) {
            foreach ($proj as $coord => $_) {
                $add = [$m, $i, $coord];
                for ($j = $i + 1, $k = 1; $j <= $count; $j++, $k++) {
                    if (isset($projection[$j][$coord + $k])) {
                        $add[0]++;
                        unset($projection[$j][$coord + $k]);
                    } else {
                        break;
                    }
                }
                $found[] = $add;
            }
        }
        if (!$found) {
            return [];
        }
        $already = [];
        $found = array_values(array_filter($found, function ($elem) use (&$already) {
            foreach ($already as $a) {
                if (
                    (($elem[1] >= $a[1]) && ($elem[1] + $elem[0] <= $a[1] + $a[0]))
                        &&
                    (($elem[2] >= $a[2]) && ($elem[2] + $elem[0] <= $a[2] + $a[0]))
                ) {
                    return false;
                }
            }
            $already[] = $elem;
            return true;
        }));
        unset($already);
        if ($switch_s1_s2) {
            $found = array_map(function ($each) {
                [$each[1], $each[2]] = [$each[2], $each[1]];
                return $each;
            }, $found);
        }
        $prev = $prev === null ? $found : $merge($prev, $found);
        if (!$prev) {
            return [];
        }
    }
    $found = $prev;
    if ($unique_substrings) {
        $flags = $collect = [];
        foreach ($found as $elem) {
            if (isset($flags[$_1 = "1-{$elem[0]}-{$elem[1]}"])) {
                continue;
            }
            if (isset($flags[$_2 = "2-{$elem[0]}-{$elem[2]}"])) {
                continue;
            }
            $flags[$_1] = $flags[$_2] = true;
            $collect[] = $elem;
        }
        $found = array_values($collect);
        unset($collect, $flags);
    }
    uasort($found, function ($a, $b) {
        $c_a = is_array($a[0]) ? max($a[0]) : $a[0];
        $c_b = is_array($b[0]) ? max($b[0]) : $b[0];
        if ($c_a != $c_b) {
            return $c_b - $c_a;
        }
        for ($i = 1, $c = count($a); $i < $c; $i++) {
            if ($a[$i] != $b[$i]) {
                return $a[$i] - $b[$i];
            }
        }
        return 0;
    });
    if ($s_map) {
        $get_len = function ($a, $index, $s_map) {
            $first = $s_map[$a[$index]];
            $last = $s_map[$a[$index] + $a[0] - 1];
            $length = ($last['pos'] - $first['pos']) + strlen($last['token']);
            return $length;
        };
        foreach ($found as &$elem) {
            $len = [];
            for ($i = 1, $c = count($elem); $i < $c; $i++) {
                $len[] = $get_len($elem, $i, $s_map[$i - 1]);
                $elem[$i] = $s_map[$i - 1][$elem[$i]]['pos'];
            }
            $min = min($len);
            $max = max($len);
            if ($min === $max) {
                $len = $len[0];
            }
            $elem[0] = $len;
        }
        unset($elem);
    }
    return array_values($found);
}

/**
 * Easy way to highlight BLAST results.
 *
 * @param string $string
 * @param int    $index
 * @param array  $results
 * @param int    $context_length (optional)
 * @param array  $highlights     (optional)
 *
 * @return string
 */
function highlight_quick_blast_results(
    string $string,
    int $index,
    array $results,
    int $context_length = 16,
    array $highlights = [['<em>', '</em>']]
): string
{
    if ($results === []) {
        return '';
    }
    $intervals = [];
    foreach ($results as &$result) {
        $result = [$result[0], $result[$index]];
    }
    unset($result);
    $plus = function ($plus, $pos) use (&$results, &$intervals, $index) {
        foreach ($results as &$result) {
            if ($result[1] >= $pos) {
                $result[1] += $plus;
            }
            $l = is_array($result[0]) ? $result[0][$index - 1] : $result[0];
            if ($result[1] < $pos && $l + $result[1] > $pos) {
                if (is_array($result[0])) {
                    $result[0][$index - 1] += $plus;
                } else {
                    $result[0] += $plus;
                }
            }
        }
        foreach ($intervals as &$interval) {
            if ($interval[0] >= $pos) {
                $interval[0] += $plus;
            }
            if ($interval[1] > $pos) {
                $interval[1] += $plus;
            }
        }
        unset($result);
    };
    $get_int = function ($begin, $end) use (&$intervals) {
        $ret = [0, 0];
        foreach ($intervals as $interval) {
            if ($begin >= $interval[0] && $end <= $interval[1]) {
                return [];
            }
            if ($end < $interval[0]) {
                continue;
            }
            if ($begin > $interval[1]) {
                continue;
            }
            if ($end <= $interval[1]) {
                $ret[1]++;
            }
            if ($begin >= $interval[0]) {
                $ret[0]++;
            }
        }
        $intervals[] = [$begin, $end];
        return $ret;
    };
    while ($result = array_shift($results)) {
        [$len, $pos] = $result;
        $len = is_array($len) ? $len[$index - 1] : $len;
        $int = $get_int($pos, $pos + $len);
        if (!$int) {
            continue;
        }
        $highlight = $highlights[max(...$int)] ?? '';
        if (!$highlight) {
            continue;
        }
        $string = substr_replace($string, $highlight[0], $pos, 0);
        $l = strlen($highlight[0]);
        $plus($l, $pos);
        $pos += $l;
        $string = substr_replace($string, $highlight[1], $pos + $len, 0);
        $l = strlen($highlight[1]);
        $plus($l, $pos + $len);
    }
    $open = $close = [];
    foreach ($highlights as $highlight) {
        $open[] = $highlight[0];
        $close[$highlight[0]] = $highlight[1];
    }
    $reg = implode('|', array_map(function ($_) {
        return preg_quote($_, '~');
    }, array_merge($open, $close)));
    $parts = preg_split('~(' . $reg . ')~', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
    $opened = $string = [];
    for ($i = 0, $c = count($parts); $i < $c; $i++) {
        $part = $parts[$i];
        if (in_array($part, $open)) {
            $opened[] = $string[] = $part;
        } elseif (in_array($part, $close)) {
            $add = [];
            while ($last = array_pop($opened)) {
                $string[] = $close[$last];
                if ($close[$last] != $part) {
                    $add[] = $last;
                    continue;
                }
                break;
            }
            foreach (array_reverse($add) as $a) {
                $string[] = $opened[] = $a;
            }
        } elseif ($opened) {
            $string[] = $part;
        } elseif (!$i) {
            $string[] = strlen($part) > $context_length + 2 ? '..' . substr($part, -$context_length) : $part;
        } else {
            $string[] = str_truncate($part, $context_length + 2, $i != $c - 1, '..');
        }
    }
    return implode($string);
}

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
    if (host($relative) && host($url) && !$scheme($url)) {
        $url = ($scheme($relative) ?: 'http') . ':' . $url;
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
            $url = preg_replace('~/+~', '/', $url);
            $url = preg_replace('~/\./~', '/', $url);
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
    static $letters = null;
    if (is_null($letters)) {
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
    $split = function ($_) {
        return preg_split('/(?<!^)(?!$)/u', $_);
    };
    $n = count($letters) / 2;
    for ($i = 0; $i < $n; $i++) {
        $string = strtr(
            $string,
            array_combine($split($letters[$i * 2]), $split($letters[$i * 2 + 1]))
        );
    }
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
    return (func_num_args() === 1 || $flags['implode']) ? implode('', $return) : $return;
}

/**
 * Transforms readable form of string to variable.
 *
 * @todo Reformat
 *
 * @param string $input
 * @param bool   $trim  (optional)
 *
 * @return mixed
 */
function readable_to_variable(string $input, bool $trim = true)
{
    $self = __FUNCTION__;
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
        if ($match[1] === "") return [];
        $array = [];
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
 * @return string|null
 */
function to_storage(string $file, array $settings = []): ?string {
    if (!is_file($file)) return null;
    $settings = $settings + [
        'dir' => sys_get_temp_dir(),
        'ext' => file_get_ext($file),
        'name' => file_get_name($file),
        'shards' => 0,
    ];
    if (!is_dir($settings['dir'])) return null;
    $hash = md5_file($file);
    if ($settings['shards'] > 2) $settings['shards'] = 2;
    $settings['dir'] = rtrim($settings['dir'], '/');
    if ($settings['shards'])
        $settings['dir'] .= preg_replace('~^(..)(..).*~', $settings['shards'] > 1 ? '/$1/$2' : '/$1', $hash);
    exec('mkdir -p ' . escapeshellarg($settings['dir']));
    $settings['name'] = normalize($settings['name'], '.-_');
    if (!$settings['name']) $settings['name'] = mt_rand();
    $ext = ($settings['ext'] ? ".{$settings['ext']}" : "");
    $target = $settings['dir'] . '/' . $settings['name'] . $ext;
    $i = 0;
    while (file_exists($target) and (md5_file($target) != $hash)) {
        $target = preg_replace($i ? '~-\d+(\.?\w+)$~' : '~(\.?\w+)$~', '-' . ($i) . '$1', $target);
        $i++;
    }
    copy($file, $target);
    return $target;
}

/**
 * All-in-one cURL function with multi threading support.
 *
 * ```php
 * $content = curl('http://github.com');
 * preg_match('~<title>(.*?)</title>~', $content, $title);
 * $title = $title[1];
 * // $title => 'The world&#39;s leading software development platform · GitHub'
 * ```
 *
 * For more examples, please, refer to [curl.md](docs/curl.md).
 *
 * @todo Reformat
 *
 * @param array|string $urls
 * @param array        $settings (optional)
 *
 * @return array|string
 */
function curl($urls, array $settings = []) {
    $_urls = $urls;
    $return = [];
    if (is_string($urls)) $urls = [$urls];
    $urls = array_values(array_filter($urls, 'is_string'));
    $urls = array_values(array_unique($urls));
    $settings = $settings + [
        'modify_content' => null,
        'retry' => 0,
        'verbose' => false,
        'threads' => 3,
        'sleep' => 5,
        'delay' => 0,
        'format' => (is_string($_urls) ? 'simple' : 'array'),
        'checker' => null,
    ];
    if (is_numeric($settings['checker']))
        $settings['checker'] = [intval($settings['checker'])];
    if (!is_callable($settings['checker']) and is_array($_ = $settings['checker']))
        $settings['checker'] = function ($url, $ch) use ($_) {
            $info = curl_getinfo($ch);
            $code = intval($info['http_code']);
            return in_array($code, array_map('intval', $_));
        };
    $handleReturn = function (& $return) use ($settings) {
        $fail = [];
        foreach (array_keys($return) as $key) {
            $value = & $return[$key];
            if (!is_array($value) or !array_key_exists('ch', $value) or !is_resource($value['ch']))
                continue;
            $ch = $value['ch'];
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            if ($error or ($settings['checker'] and !($_ = $settings['checker']($value['url'], $ch)))) {
                unset($return[$key]);
                curl_close($ch);
                $fail[$key] = $value['url'];
                if ($settings['verbose']) {
                    echo $value['url'], ' .. ERR!' . ($error ? " ({$error})" : '') . ($_ ? " ({$_})" : '');
                }
                continue;
            }
            $info = curl_getinfo($ch);
            $content = curl_multi_getcontent($ch);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            if ($settings['verbose'] and $value['url'] === $info['url'])
                echo $value['url'], ' .. OK!', "\n";
            elseif ($settings['verbose'])
                echo $value['url'], ' .. ', $info['url'], ' .. OK!', "\n";
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
            if ($settings['modify_content'] and is_callable($settings['modify_content']))
                $return[$key]['content'] = $settings['modify_content']($value['url'], $content);
            $return[$key]['info'] = $info;
            if (isset($headers)) $return[$key]['headers'] = $headers;
            unset($value['ch']);
            curl_close($ch);
        }
        return $fail;
    };
    $getCH = function ($url, $settings) {
        $ch = curl_init();
        $opts = [];
        $setopt = function ($arr) use (& $ch, & $opts) {
            $opts = array_replace($opts, $arr);
            curl_setopt_array($ch, $arr);
        };
        $setopt([
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
        ]);
        $acceptCallable = [
            CURLOPT_HEADERFUNCTION,
            CURLOPT_PROGRESSFUNCTION,
            CURLOPT_READFUNCTION,
            CURLOPT_WRITEFUNCTION,
        ];
        if (defined("CURLOPT_PASSWDFUNCTION"))
            $acceptCallable[] = CURLOPT_PASSWDFUNCTION;
        if (is_string($url) and host($url))
            $setopt([CURLOPT_URL => $url]);
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
        return host($opts[CURLOPT_URL] ?? '') ? $ch : null;
    };
    do {
        $fails = array();
        while ($urls) {
            if ($settings['threads'] == 1 or count($urls) == 1) {
                $single = curl_init();
                $multi = null;
            } else {
                $single = null;
                $multi = curl_multi_init();
            }
            for ($i = 0; $i < $settings['threads'] and $urls; $i++) {
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
                    usleep(300000);
                } while ($running > 0);
                curl_multi_close($multi);
            } else {
                curl_exec($single);
            }
            $fails[] = $handleReturn($return);
            if ($urls and $settings['delay']) sleep($settings['delay']);
        }
        foreach ($fails as $fail)
            foreach ($fail as $k => $v)
                $urls[$k] = $v;
    } while ($urls and $settings['retry']-- and sleep($settings['sleep']) === 0);
    if ($settings['format'] === 'simple') {
        return implode("\n\n", array_values(array_column($return, 'content')));
    } elseif ($settings['format'] === 'array') {
        return array_column($return, 'content', 'url');
    }
    return array_column($return, null, 'url');
}

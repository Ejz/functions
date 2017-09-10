# functions [![Travis Status for Ejz/functions](https://travis-ci.org/Ejz/functions.svg?branch=master)](https://travis-ci.org/Ejz/functions)

[functions](https://github.com/Ejz/functions) is my collections of useful PHP functions. Some functions are just handy wrappers for PHP built-in functions. All functions are added to global scope, no namespaces are required.

### Install

```bash
$ curl -sS 'https://getcomposer.org/installer' | php
$ php composer.phar require ejz/functions:~1.0
```

To use it, just include `vendor/autoload.php` in your PHP script.

### Requirements

PHP 5.6 or above (with cURL and GD library installed).

### Contents

- [esc](#esc)
- [fesc](#fesc)
- [template](#template)
- [is_host](#is_host)
- [host](#host)
- [curdate](#curdate)
- [now](#now)
- [nsplit](#nsplit)
- [is_closure](#is_closure)
- [is_ip](#is_ip)
- [is_assoc](#is_assoc)
- [is_regex](#is_regex)
- [str_replace_once](#str_replace_once)
- [str_truncate](#str_truncate)
- [mt_shuffle](#mt_shuffle)
- [file_get_ext](#file_get_ext)
- [file_get_name](#file_get_name)
- [rand_from_string](#rand_from_string)
- [get_user_agent](#get_user_agent)
- [get_tag_attributes](#get_tag_attributes)
- [prepare_tag_attributes](#prepare_tag_attributes)
- [realurl](#realurl)
- [xpath](#xpath)
- [curl](#curl)
- [getopts](#getopts)
- [to_storage](#to_storage)
- [latinize](#latinize)
- [normalize](#normalize)
- [config](#config)
- [ini_file_set](#ini_file_set)
- [readable_to_variable](#readable_to_variable)
- [variable_to_readable](#variable_to_readable)
- [go_cron](#go_cron)
- [SQL](#SQL)
- [url_base64_encode](#url_base64_encode)
- [url_base64_decode](#url_base64_decode)
- [xencrypt](#xencrypt)
- [xdecrypt](#xdecrypt)
- [oencrypt](#oencrypt)
- [odecrypt](#odecrypt)
- [base32_decode](#base32_decode)
- [base32_encode](#base32_encode)
- [im_resize](#im_resize)
- [im_transparent](#im_transparent)
- [im_crop](#im_crop)
- [im_border](#im_border)
- [im_captcha](#im_captcha)
- [_expect](#_expect)
- [_log](#_log)
- [_warn](#_warn)
- [_err](#_err)

----

#### esc

Encode/decode HTML chars in given string: `>`, `<` and `&`. 
Use this function to escape HTML tags content.

```php
$s = esc("HTML: <>&");
// $s => "HTML: &lt;&gt;&amp;"
$s = esc($s, $decode = true);
// $s => "HTML: <>&"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L24-L30)

#### fesc

Encode/decode HTML chars in given string: `>`, `<`, `&`, `'` and `"`. 
Use this function to escape HTML tags atrribute values.

```php
$s = fesc("HTML: <>&, '\"");
// $s => "HTML: &lt;&gt;&amp;, &#039;&quot;"
$s = esc($s, $decode = true);
// $s => "HTML: <>&, '\""
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L43-L49)

#### template

Native PHP templating engine.

```
string template(string $file, array $vars = array());
```

```html
<!-- test.tpl -->
<html>
<head>
    <title><?=$title?></title>
</head>
<body>
    <?=$body?>
</body>
</html>
```

```php
echo template("test.tpl", [
    "title" => "Test Title",
    "body" => "<h1>Hello!</h1>",
]);
```

Output:

```html
<html>
<head>
    <title>Test Title</title>
</head>
<body>
    <h1>Hello!</h1>
</body>
</html>
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L90-L96)

#### is_host

Validate a hostname (an IP address or domain name).

```
bool is_host(string $host);
```

```php
$bool = is_host("github.com");
// $bool => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L110-L118)

#### host

Get hostname from URL.

```
string host(string $url);
```

```php
$host = host("https://github.com/");
// $host => "github.com"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L132-L135)

#### curdate

Get current date in SQL format. Can shift current day using first argument.

```
string curdate(int $shift_days = 0);
```

```php
$today = curdate();
// $today => "2017-08-17"
$yesterday = curdate(-1);
// $yesterday => "2017-08-16"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L151-L153)

#### now

Get current time is SQL format. Can shift current time using first argument.

```
string now(int $shift_seconds = 0);
```

```php
$now = now();
// $now => "2017-08-17 11:04:31"
$min_ago = now(-60);
// $min_ago => "2017-08-17 11:03:31"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L169-L171)

#### nsplit

Split line by line given string. Each line is trimmed, empty ones are filtered out.

```
array nsplit(string $string);
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L180-L186)

#### is_closure

Return whether or not the provided object is callable.

```
bool is_closure(object $object);
```

```php
$bool = is_closure(function() { ; });
// $bool => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L200-L202)

#### is_ip

Whether or not provided IP is valid IP.

```
bool is_ip(string $ip, bool $allow_private = true);
```

```php
$ip = "127.0.0.1";
$bool = is_ip($ip);
// $bool => true
$bool = is_ip($ip, $allow_private = false);
// $bool => false
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L219-L225)

#### is_assoc

Validate associative array.

```
bool is_assoc(array $array);
```

```php
$bool = is_assoc([]);
// $bool => true
```

```php
$bool = is_assoc([1, 2]);
// $bool => false
```

```php
$bool = is_assoc(["key" => "value"]);
// $bool => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L249-L254)

#### is_regex

Validate regular expression.

```
bool is_regex(string $regex);
```

```php
$bool = is_regex("invalid");
// $bool => false
```

```php
$bool = is_regex("~\w~");
// $bool => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L273-L277)

#### str_replace_once

String replace. Replace is applied only once.

```
string str_replace_once(string $needle, string $replace, string $haystack);
```

```php
$str = str_replace_once("foo", "bar", "foo foo");
// $str => "bar foo"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L291-L295)

#### str_truncate

Truncate string to certain length (be default 40 chars).

```
string str_truncate(string $string, int $length = 40, bool $center = false, string $replacer = '...');
```

```php
$str = str_truncate("Hello, world!", 5);
// $str => "He..."
```

```php
$str = str_truncate("Hello, world!", 5, $center = true);
// $str => "H...!"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L314-L328)

#### mt_shuffle

Shuffle an array using `mt_rand()`. Can use seed for remembering randomize.

```
mt_shuffle(array & $array, string|int|null $seed = null);
```

```php
$arr = ["one", "two", "three"];
mt_shuffle($arr);
// $arr => ["two", "three", "one"]
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L343-L359)

#### file_get_ext

Get file extension.

```
string file_get_ext(string $file);
```

```php
$ext = file_get_ext("image.PNG");
// $ext => "png"
```

```php
$ext = file_get_ext("archive.tar.gz");
// $ext => "gz"
```

```php
$ext = file_get_ext("/etc/passwd");
// $ext => ""
```

```php
$ext = file_get_ext("/var/www/");
// $ext => ""
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L388-L392)

#### file_get_name

Get file name (without extension).

```
string file_get_name(string $file);
```

```php
$name = file_get_name("image.png");
// $name => "image"
```

```php
$name = file_get_name("archive.tar.gz");
// $name => "archive.tar"
```

```php
$name = file_get_name("/etc/passwd");
// $name => "passwd"
```

```php
$name = file_get_name("/var/www/");
// $name => ""
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L421-L426)

#### rand_from_string

Get random integer from string.

```
int rand_from_string(string $string);
```

```php
$int = rand_from_string("one");
// $int => 975299411
```

```php
$int = rand_from_string("two");
// $int => 897156455
```

```php
$int = rand_from_string("one");
// $int => 975299411
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L450-L456)

#### get_user_agent

Randomly get User agent string.

```
string get_user_agent(string|null $filter = null, string|int|null $seed = null);
```

```php
$ua = get_user_agent();
// $ua => "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)"
```

```php
$ua = get_user_agent("invalid filter");
// $ua => ""
```

```php
$ua = get_user_agent("opera");
// $ua => "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; de) Opera 11.51"
```

```php
var_dump(get_user_agent("Mac OS X", "seed") === get_user_agent("Mac OS X", "seed"));
// => bool(true)
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L485-L502)

#### get_tag_attributes

Get tag attributes. Returns list. 
If second argument is not null, returns value of this argument 
(or null if no such argument).

```
array|string|null get_user_agent(string|DOMNode $tag, string|null $attr = null);
```

```php
$tag = "<a href='/link.html?a=1&amp;b=2'>";
$attrs = get_tag_attributes($tag);
// $attrs => ["href" => "/link.html?a=1&b=2"]
$attr = get_tag_attributes($tag, 'href');
// $attr => "/link.html?a=1&b=2"
$attr = get_tag_attributes($tag, '_target');
// $attr => null
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L523-L541)

#### prepare_tag_attributes

Prepare attributes for outputing in HTML tag.

```
string prepare_tag_attributes(array $attributes);
```

```php
$attributes = ["href" => "/link.html?a=1&b=2", "class" => ["_left", "_clearfix"]];
$prepared = prepare_tag_attributes($attributes);
// $prepared => 'href="/link.html?a=1&amp;b=2" class="_left _clearfix"'
```

```php
$attributes = ["style" => ["margin-top" => "0", "display" => "flex"]];
$prepared = prepare_tag_attributes($attributes);
// $prepared => "style='margin-top:0;display:flex;'"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L562-L581)

#### realurl

Get absolute URL, also lead URL to more canonical form.

```
string|null realurl(string $url, string $absolute = '');
```

```php
$url = realurl("/link.html", "http://site.com/");
// $url => "http://site.com/link.html"
```

```php
$url = realurl("http://site.com/archive/2014/../link.html");
// $url => "http://site.com/archive/link.html"
```

```php
$url = realurl("../home.html", "http://site.com/archive/link.html");
// $url => "http://site.com/home.html"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L605-L635)

#### xpath

Wrapper around [DOMXPath](http://php.net/manual/en/class.domxpath.php). 
Accepts XPath queries for extracting tags and callback function for tag manipulating.

```
array|string xpath(
    string|DOMNode $xml,
    string $query = '/*',
    callable|int|null $callback = null,
    array $flags = []
);
```

```php
$content = file_get_contents("http://github.com/");
$metas = xpath($content, "//meta");
print_r($metas);
```

Output:

```
Array
(
    [0] => <meta charset="utf-8"/>
    [1] => <meta name="viewport" content="width=device-width"/>
    [2] => <meta property="og:url" content="https://github.com"/>
    [3] => <meta name="pjax-timeout" content="1000"/>
    [4] => <meta name="theme-color" content="#1e2327"/>
)
```

For more examples, please, refer to [xpath.md](docs/xpath.md).


[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L687-L778)

#### curl

All-in-one cURL function with multi threading support.

```
array|string curl(array|string $urls, array $settings = []);
```

```php
$content = curl("http://github.com/");
preg_match("~<title>(.*?)</title>~", $content, $title);
echo $title[1];
// => "The world&#39;s leading software development platform · GitHub"
```

For more examples, please, refer to [curl.md](docs/curl.md).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L796-L973)

#### getopts

Get options from command line. In case of error returns error string.

```
array|string getopts(array $opts, array|null $argv = null);
```

```php
$opts = getopts([
    "a" => false,     // short, no value
    "b" => true,      // short, with value
    "help" => false,  // long, no value
    "filter" => true, // long, with value
], explode(" ", "./script.sh -ab1 arg --help --filter=value"));
var_dump($opts);
```

Output:

```
array(6) {
  [0] =>
  string(11) "./script.sh"
  'a' =>
  bool(true)
  'b' =>
  string(1) "1"
  [1] =>
  string(3) "arg"
  'help' =>
  bool(true)
  'filter' =>
  string(5) "value"
}
```

For more examples, please, refer to [getopts.md](docs/getopts.md).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1013-L1106)

#### to_storage

Help function for saving data in storage.

```
string|null to_storage(string $file, array $settings = array());
```

```php
$content = 'foo';
$tmp = rtrim(`mktemp`);
$file = to_storage($tmp);
// $file => "/tmp/tmp.qmviqzrrd1"
```

```php
$content = 'foo';
$tmp = rtrim(`mktemp`);
$file = to_storage($tmp, ['shards' => 2, 'ext' => 'txt']);
// $file => "/tmp/ac/bd/tmp.jwueqsppoz.txt"
```

For more examples, please, refer to [to_storage.md](docs/to_storage.md).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1131-L1176)

#### latinize

Latinize string. Set `$ru` to `true` in order to latinize also cyrillic characters.

```
string latinize($string, $ru = false);
```

```php
echo latinize('Màl Śir');
// => "Mal Sir"
```

```php
echo latinize('привет мир', $ru = true);
// => "privet mir"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1195-L1279)

#### normalize

Normalize string by removing non-English chars. Can add some extra chars (using `$extra`) and cyrillic chars (using `$ru`).

```
string normalize($string, $extra = "", $ru = false);
```

```php
echo normalize("Hello, world!");
// => "hello world"
```

```php
echo normalize("John's hat!", $extra = "'");
// => "john's hat"
```

```php
echo normalize("Привет, мир!", $extra = "", $ru = true);
// => "привет мир"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1303-L1315)

#### config

Universal entrypoint for config get/set operations.

```
; config.ini

[global]
debug = 1
```

```php
$config = parse_ini_file("config.ini", true);
config(".", $config);
$global = config("global");
// $global => ["debug" => "1"]
$debug = config("global.debug");
// $debug => "1"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1336-L1409)

#### ini_file_set

Correctly saves value to INI file (or creates new one).

```
bool ini_file_set($file, $key, $value);
```

```php
$return = ini_file_set("config.ini", "global.debug", "0");
// $return => true
echo file_get_contents("config.ini");
```

Output:

```
; <?php exit();
; /*

[global]
debug = 0

; */
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1436-L1482)

#### readable_to_variable

Transforms readable form of string to variable.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1487-L1514)

#### variable_to_readable

Transform any variable to readable form.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1519-L1545)

#### go_cron

Cron out-of-the-box. Supports Linux format.
```
 * * * * *
 | | | | |
 | | | | +----- Days of week (0-6), 0 - Sunday
 | | | +------- Months (1-12)
 | | +--------- Days of month (1-31)
 | +----------- Hours (0-23)
 +------------- Minutes (0-59)

* - any value
1 - certain value
1-2 - value lies in interval
1,4 - list of values
*/2 - all even values
1,2-3,*/4 - mix
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1566-L1617)

#### SQL

Universal SQL wrapper.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1622-L1687)

#### url_base64_encode

Encode string to URL-safe Base64 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1791-L1793)

#### url_base64_decode

Decode from URL-safe Base64 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1798-L1800)

#### xencrypt

XOR encryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1805-L1813)

#### xdecrypt

XOR decryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1818-L1831)

#### oencrypt

Implements OpenSSL encryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1836-L1842)

#### odecrypt

Implements OpenSSL decryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1847-L1859)

#### base32_decode

Decode string from Base32 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1864-L1873)

#### base32_encode

Encode string in Base32 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1878-L1891)

#### im_resize

Resize image.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1922-L1927)

#### im_transparent

Make the image transparent.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1932-L1937)

#### im_crop

Crop provided image.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1942-L1956)

#### im_border

Draw border around image.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1961-L1968)

#### im_captcha

Generate captcha image.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1973-L2043)

#### _expect

Raise an error, if given variable does not match type.

```
_expect(mixed $var, string $types);
```

```php
$a = 'string';
_expect($a, 'string|null');
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L2206-L2229)

#### _log

Raise a user defined error with a message. 
Shortcut to `trigger_error()` function. 
For CLI mode outputs to `STDERR` instead of raising an error. 
If `E_USER_ERROR` is raised, exit with error code `1`.

```
_log(string $msg, int $level = E_USER_NOTICE);
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L2241-L2261)

#### _warn

Raise `E_USER_WARNING` with a message.

```
_warn(string $msg);
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L2270-L2272)

#### _err

Raise `E_USER_ERROR` with a message. Exit with error code `1`.

```
_err(string $msg);
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L2281-L2283)

### Authors

- [Evgeny Cernisev](https://ejz.ru) | [GitHub](https://github.com/Ejz) | <ejz@ya.ru>

### License

[functions](https://github.com/Ejz/functions) is licensed under the [WTFPL License](https://en.wikipedia.org/wiki/WTFPL) (see [LICENSE](LICENSE)).

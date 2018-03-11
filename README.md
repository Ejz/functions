# functions [![Travis Status for Ejz/functions](https://travis-ci.org/Ejz/functions.svg?branch=master)](https://travis-ci.org/Ejz/functions)

[functions](https://github.com/Ejz/functions) is my collections of useful PHP functions. Some functions are just handy wrappers for PHP built-in functions. All functions are added to global scope, no namespaces are required.

### Install

```bash
$ curl -sS 'https://getcomposer.org/installer' | php
$ php composer.phar require ejz/functions:~1.0
```

To use it, just include `vendor/autoload.php` in your PHP script.

### Requirements

PHP 7.1 or above (with cURL library installed).

### Contents

- [esc](#esc)
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
- [file_get_ext](#file_get_ext)
- [file_get_name](#file_get_name)
- [get_tag_attributes](#get_tag_attributes)
- [prepare_tag_attributes](#prepare_tag_attributes)
- [realurl](#realurl)
- [setenv](#setenv)
- [url_base64_encode](#url_base64_encode)
- [url_base64_decode](#url_base64_decode)
- [xencrypt](#xencrypt)
- [xdecrypt](#xdecrypt)
- [oencrypt](#oencrypt)
- [odecrypt](#odecrypt)
- [base32_decode](#base32_decode)
- [base32_encode](#base32_encode)
- [latinize](#latinize)
- [normalize](#normalize)
- [sanitize_html_output](#sanitize_html_output)
- [xpath](#xpath)
- [readable_to_variable](#readable_to_variable)
- [to_storage](#to_storage)
- [curl](#curl)

----

#### esc

```
string esc(string $string, bool $decode = false)
```

Encode/decode HTML chars in given string: `>`, `<`, `&`, `'` and `"`. 
Use this function to escape HTML tags content and atrribute values.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L15-L21)

#### is_host

```
bool is_host(mixed $host)
```

Validate a hostname (an IP address or domain name).

```php
$bool = is_host('github.com');
// $bool => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L35-L45)

#### host

```
string host(string $url)
```

Get hostname from URL.

```php
$host = host('https://github.com/');
// $host => 'github.com'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L59-L62)

#### curdate

```
string curdate(int $shift_days = 0)
```

Get current date in SQL format. Can shift current day using first argument.

```php
$today = curdate();
// $today => '2017-08-17'
$yesterday = curdate(-1);
// $yesterday => '2017-08-16'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L78-L80)

#### now

```
string now(int $shift_seconds = 0)
```

Get current time is SQL format. Can shift current time using first argument.

```php
$now = now();
// $now => '2017-08-17 11:04:31'
$min_ago = now(-60);
// $min_ago => '2017-08-17 11:03:31'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L96-L98)

#### nsplit

```
array nsplit(string $string)
```

Split line by line given string. Each line is trimmed, empty ones are filtered out.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L107-L113)

#### is_closure

```
bool is_closure(mixed $closure)
```

Return whether or not the provided object is callable.

```php
$is_closure = is_closure(function () { ; });
// $is_closure => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L127-L129)

#### is_ip

```
bool is_ip(mixed $ip, bool $allow_private = true)
```

Whether or not provided IP is valid IP.

```php
$ip = '127.0.0.1';
$is_ip = is_ip($ip);
// $is_ip => true
$is_ip = is_ip($ip, false);
// $is_ip => false
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L147-L153)

#### is_assoc

```
bool is_assoc(mixed $assoc)
```

Validate associative array.

```php
$is_assoc = is_assoc([]);
// $is_assoc => true
$is_assoc = is_assoc([1, 2]);
// $is_assoc => false
$is_assoc = is_assoc(['key' => 'value']);
// $is_assoc => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L171-L176)

#### is_regex

```
bool is_regex(mixed $regex)
```

Validate regular expression.

```php
$is_regex = is_regex('invalid');
// $is_regex => false
$is_regex = is_regex('~\w~');
// $is_regex => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L192-L195)

#### str_replace_once

```
string str_replace_once(string $needle, string $replace, string $haystack)
```

String replace. Replace is applied only once.

```php
$str = str_replace_once('foo', 'bar', 'foo foo');
// $str => 'bar foo'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L211-L215)

#### str_truncate

```
string str_truncate(string $string, int $length = 40, bool $center = false, string $replacer = '...')
```

Truncate string to certain length (be default 40 chars).

```php
$str = str_truncate('Hello, world!', 5);
// $str => 'He...'
$str = str_truncate('Hello, world!', 5, true);
// $str => 'H...!'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L234-L248)

#### file_get_ext

```
string file_get_ext(string $file)
```

Get file extension.

```php
$ext = file_get_ext('image.PNG');
// $ext => 'png'
$ext = file_get_ext('archive.tar.gz');
// $ext => 'gz'
$ext = file_get_ext('/etc/passwd');
// $ext => ''
$ext = file_get_ext('/var/www/');
// $ext => ''
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L268-L272)

#### file_get_name

```
string file_get_name(string $file)
```

Get file name (without extension).

```php
$name = file_get_name('image.png');
// $name => 'image'
$name = file_get_name('archive.tar.gz');
// $name => 'archive.tar'
$name = file_get_name('/etc/passwd');
// $name => 'passwd'
$name = file_get_name('/var/www/');
// $name => ''
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L292-L297)

#### get_tag_attributes

```
array|string|null get_tag_attributes(string $tag, string $attribute = null)
```

Get tag attributes. Returns list. 
If second argument is not null, returns value of this argument 
(or null if no such argument).

```php
$tag = "<a href='/link.html?a=1&amp;b=2'>";
$attributes = get_tag_attributes($tag);
// $attributes => ['href' => '/link.html?a=1&b=2']
$attribute = get_tag_attributes($tag, 'href');
// $attribute => '/link.html?a=1&b=2'
$attribute = get_tag_attributes($tag, '_target');
// $attribute => null
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L319-L330)

#### prepare_tag_attributes

```
string prepare_tag_attributes(array $attributes)
```

Prepare attributes for outputing in HTML tag.

```php
$attributes = [
    'href' => '/link.html?a=1&b=2',
    'class' => ['_left', '_clearfix'],
];
$prepared = prepare_tag_attributes($attributes);
// $prepared => 'href="/link.html?a=1&amp;b=2" class="_left _clearfix"'
$attributes = [
    'style' => [
        'margin-top' => '0',
        'display' => 'flex',
    ],
];
$prepared = prepare_tag_attributes($attributes);
// $prepared => 'style="margin-top:0;display:flex;"'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L356-L374)

#### realurl

```
string realurl(string $url, string $relative = '')
```

Get absolute URL, lead URL to more canonical form. Also operates with files. 
`$url` is canonized according to `$relative` (file or URL). In case of error returns empty string.

```php
$url = realurl('/link.html', 'http://site.com/');
// $url => 'http://site.com/link.html'
$url = realurl('http://site.com/archive/2014/../link.html');
// $url => 'http://site.com/archive/link.html'
$url = realurl('../home.html', 'http://site.com/archive/link.html');
// $url => 'http://site.com/home.html'
$url = realurl('../new.md', 'path/a/old.md');
// $url => 'path/new.md'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L396-L431)

#### setenv

```
bool setenv(string $name, string $value, string $file = DOTENV_FILE)
```

Used to set environment variable inside `.env` file. 
If you ignore third argument, `.env` file is taken from `DOTENV_FILE` constant.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L443-L455)

#### url_base64_encode

```
string url_base64_encode(string $string)
```

Encode string to URL-safe Base64 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L464-L466)

#### url_base64_decode

```
string url_base64_decode(string $string)
```

Decode from URL-safe Base64 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L475-L478)

#### xencrypt

```
string xencrypt(string $string, string $key)
```

XOR encryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L488-L496)

#### xdecrypt

```
string xdecrypt(string $string, string $key)
```

XOR decryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L506-L520)

#### oencrypt

```
string oencrypt(string $string, string $key)
```

Implements OpenSSL encryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L530-L536)

#### odecrypt

```
string odecrypt(string $string, string $key)
```

Implements OpenSSL decryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L546-L558)

#### base32_decode

```
string base32_decode(string $string)
```

Decode string from Base32 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L567-L574)

#### base32_encode

```
string base32_encode(string $string)
```

Encode string in Base32 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L583-L596)

#### latinize

```
string latinize(string $string, bool $ru = false)
```

Latinize string. Set `$ru` to `true` in order to latinize also cyrillic characters.

```php
$s = latinize('Màl Śir');
// $s => 'Mal Sir'
$s = latinize('привет мир', true);
// $s => 'privet mir'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L613-L691)

#### normalize

```
string normalize(string $string, string $extra = '', bool $ru = false)
```

Normalize string by removing non-English chars. 
Can add some extra chars (using `$extra`) and cyrillic chars (using `$ru`).

```php
echo normalize("Hello, world!");
// => "hello world"
echo normalize("John's hat!", $extra = "'");
// => "john's hat"
echo normalize("Привет, мир!", $extra = "", $ru = true);
// => "привет мир"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L712-L724)

#### sanitize_html_output

```
string sanitize_html_output(string $string)
```

Make HTML more compact.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L733-L743)

#### xpath

```
array|string xpath(DOMNode|string $xml, string $query = '/*', callable|int|null $callback = null, array $flags = [])
```

Wrapper around [DOMXPath](http://php.net/manual/en/class.domxpath.php). 
Accepts XPath queries for extracting tags and callback function for tag manipulating.

```php
$content = file_get_contents("http://github.com/");
$metas = xpath($content, "//meta");
print_r($metas);
```

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L798-L884)

#### readable_to_variable

```
mixed readable_to_variable(string $input, bool $trim = true)
```

Transforms readable form of string to variable.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L894-L920)

#### to_storage

```
string|null to_storage(string $file, array $settings = [])
```

Help function for saving data in storage.

```php
$tmp = rtrim(`mktemp`);
$file = to_storage($tmp);
// $file => '/tmp/tmp.qmviqzrrd1'
$tmp = rtrim(`mktemp`);
$file = to_storage($tmp, ['shards' => 2, 'ext' => 'txt']);
// $file => '/tmp/ac/bd/tmp.jwueqsppoz.txt'
```

For more examples, please, refer to [to_storage.md](docs/to_storage.md).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L941-L967)

#### curl

```
array|string curl(array|string $urls, array $settings = [])
```

All-in-one cURL function with multi threading support.

```php
$content = curl('http://github.com');
preg_match('~<title>(.*?)</title>~', $content, $title);
$title = $title[1];
// $title => 'The world&#39;s leading software development platform · GitHub'
```

For more examples, please, refer to [curl.md](docs/curl.md).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L986-L1157)

### Authors

- [Evgeny Cernisev](https://ejz.ru) | [GitHub](https://github.com/Ejz) | <ejz@ya.ru>

### License

[functions](https://github.com/Ejz/functions) is licensed under the [WTFPL License](https://en.wikipedia.org/wiki/WTFPL) (see [LICENSE](LICENSE)).

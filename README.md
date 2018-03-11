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
- [xpath_callback_remove](#xpath_callback_remove)
- [xpath_callback_unwrap](#xpath_callback_unwrap)
- [xpath](#xpath)
- [readable_to_variable](#readable_to_variable)
- [to_storage](#to_storage)
- [curl](#curl)

----

#### esc

Encode/decode HTML chars in given string: `>`, `<`, `&`, `'` and `"`. 
Use this function to escape HTML tags content and atrribute values.

@param string $string
@param bool   $decode (optional)

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L15-L21)

#### is_host

Validate a hostname (an IP address or domain name).

```php
$bool = is_host('github.com');
// $bool => true
```

@param mixed $host

@return bool

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L35-L45)

#### host

Get hostname from URL.

```php
$host = host('https://github.com/');
// $host => 'github.com'
```

@param string $url

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L59-L62)

#### curdate

Get current date in SQL format. Can shift current day using first argument.

```php
$today = curdate();
// $today => '2017-08-17'
$yesterday = curdate(-1);
// $yesterday => '2017-08-16'
```

@param int $shift_days (optional)

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L78-L80)

#### now

Get current time is SQL format. Can shift current time using first argument.

```php
$now = now();
// $now => '2017-08-17 11:04:31'
$min_ago = now(-60);
// $min_ago => '2017-08-17 11:03:31'
```

@param int $shift_seconds (optional)

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L96-L98)

#### nsplit

Split line by line given string. Each line is trimmed, empty ones are filtered out.

@param string $string

@return array

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L107-L113)

#### is_closure

Return whether or not the provided object is callable.

```php
$is_closure = is_closure(function () { ; });
// $is_closure => true
```

@param mixed $closure

@return bool

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L127-L129)

#### is_ip

Whether or not provided IP is valid IP.

```php
$ip = '127.0.0.1';
$is_ip = is_ip($ip);
// $is_ip => true
$is_ip = is_ip($ip, false);
// $is_ip => false
```

@param mixed $ip
@param bool  $allow_private (optional)

@return bool

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L147-L153)

#### is_assoc

Validate associative array.

```php
$is_assoc = is_assoc([]);
// $is_assoc => true
$is_assoc = is_assoc([1, 2]);
// $is_assoc => false
$is_assoc = is_assoc(['key' => 'value']);
// $is_assoc => true
```

@param mixed $assoc

@return bool

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L171-L176)

#### is_regex

Validate regular expression.

```php
$is_regex = is_regex('invalid');
// $is_regex => false
$is_regex = is_regex('~\w~');
// $is_regex => true
```

@param mixed $regex

@return bool

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L192-L195)

#### str_replace_once

String replace. Replace is applied only once.

```php
$str = str_replace_once('foo', 'bar', 'foo foo');
// $str => 'bar foo'
```

@param string $needle
@param string $replace
@param string $haystack

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L211-L215)

#### str_truncate

Truncate string to certain length (be default 40 chars).

```php
$str = str_truncate('Hello, world!', 5);
// $str => 'He...'
$str = str_truncate('Hello, world!', 5, true);
// $str => 'H...!'
```

@param string $string
@param int    $length   (optional)
@param bool   $center   (optional)
@param string $replacer (optional)

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L234-L248)

#### file_get_ext

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

@param string $file

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L268-L272)

#### file_get_name

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

@param string $file

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L292-L297)

#### get_tag_attributes

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

@param string $tag
@param string $attribute (optional)

@return array|string|null

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L319-L330)

#### prepare_tag_attributes

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

@param array $attributes

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L356-L374)

#### realurl

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

@param string $url
@param string $relative (optional)

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L396-L431)

#### setenv

Used to set environment variable inside `.env` file. 
If you ignore third argument, `.env` file is taken from `DOTENV_FILE` constant.

@param string $name
@param string $value
@param string $file  (optional)

@return bool

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L443-L455)

#### url_base64_encode

Encode string to URL-safe Base64 format.

@param string $string

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L464-L466)

#### url_base64_decode

Decode from URL-safe Base64 format.

@param string $string

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L475-L478)

#### xencrypt

XOR encryption.

@param string $string
@param string $key

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L488-L496)

#### xdecrypt

XOR decryption.

@param string $string
@param string $key

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L506-L520)

#### oencrypt

Implements OpenSSL encryption.

@param string $string
@param string $key

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L530-L536)

#### odecrypt

Implements OpenSSL decryption.

@param string $string
@param string $key

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L546-L558)

#### base32_decode

Decode string from Base32 format.

@param string $string

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L567-L574)

#### base32_encode

Encode string in Base32 format.

@param string $string

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L583-L596)

#### latinize

Latinize string. Set `$ru` to `true` in order to latinize also cyrillic characters.

```php
$s = latinize('Màl Śir');
// $s => 'Mal Sir'
$s = latinize('привет мир', true);
// $s => 'privet mir'
```

@param string $string
@param bool   $string (optional)

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L613-L691)

#### normalize

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

@param string $string
@param string $extra
@param bool   $ru     (optional)

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L712-L724)

#### sanitize_html_output

Make HTML more compact.

@param string $string

@return string

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L733-L743)

#### xpath_callback_remove

@param DOMNode $tag

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L748-L750)

#### xpath_callback_unwrap

@param DOMNode $tag

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L755-L764)

#### xpath

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

@param DOMNode|string    $xml
@param string            $query    (optional)
@param callable|int|null $callback (optional)
@param array             $flags    (optional)

@return array|string

@throws Exception

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L798-L884)

#### readable_to_variable

Transforms readable form of string to variable.

@param string $input
@param bool   $trim  (optional)

@return mixed

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L894-L920)

#### to_storage

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

@param string $file
@param array  $settings (optional)

@return string|null

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L941-L967)

#### curl

All-in-one cURL function with multi threading support.

```php
$content = curl('http://github.com');
preg_match('~<title>(.*?)</title>~', $content, $title);
$title = $title[1];
// $title => 'The world&#39;s leading software development platform · GitHub'
```

For more examples, please, refer to [curl.md](docs/curl.md).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L981-L1152)

### Authors

- [Evgeny Cernisev](https://ejz.ru) | [GitHub](https://github.com/Ejz) | <ejz@ya.ru>

### License

[functions](https://github.com/Ejz/functions) is licensed under the [WTFPL License](https://en.wikipedia.org/wiki/WTFPL) (see [LICENSE](LICENSE)).

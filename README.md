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
- [raw2int](#raw2int)
- [int2raw](#int2raw)
- [is_closure](#is_closure)
- [is_ip](#is_ip)
- [is_assoc](#is_assoc)
- [is_regex](#is_regex)
- [str_replace_once](#str_replace_once)
- [str_truncate](#str_truncate)
- [file_get_ext](#file_get_ext)
- [file_get_name](#file_get_name)
- [template](#template)
- [get_tag_attributes](#get_tag_attributes)
- [prepare_tag_attributes](#prepare_tag_attributes)
- [realurl](#realurl)
- [setenv](#setenv)
- [_T](#_T)
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
- [xpath](#xpath)
- [mb_ucfirst](#mb_ucfirst)
- [sanitize_html](#sanitize_html)
- [readable_to_variable](#readable_to_variable)
- [to_storage](#to_storage)
- [curl](#curl)
- [parse_ssh_config](#parse_ssh_config)
- [get_domain_info](#get_domain_info)
- [is_same_suffix_domains](#is_same_suffix_domains)
- [extract_links_from_html](#extract_links_from_html)
- [extract_metas_from_html](#extract_metas_from_html)
- [extract_comments_from_html](#extract_comments_from_html)
- [extract_scripts_from_html](#extract_scripts_from_html)
- [extract_texts_from_html](#extract_texts_from_html)
- [extract_inputs_from_html](#extract_inputs_from_html)
- [extract_keywords_from_html](#extract_keywords_from_html)
- [extract_all_from_html](#extract_all_from_html)
- [crawler](#crawler)
- [quick_blast](#quick_blast)
- [highlight_quick_blast_results](#highlight_quick_blast_results)
- [string_generator](#string_generator)
- [simple_lexer](#simple_lexer)
- [get_rpn](#get_rpn)

----

#### esc

```
string esc(string $string, bool $decode = false)
```

Encode/decode HTML chars in given string: `>`, `<`, `&`, `'` and `"`. 
Use this function to escape HTML tags content and atrribute values.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L15-L22)

#### is_host

```
bool is_host(mixed $host)
```

Validate a hostname (an IP address or domain name).

```php
$bool = is_host('github.com');
// $bool => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L36-L47)

#### host

```
string host(string $url)
```

Get hostname from URL.

```php
$host = host('https://github.com/');
// $host => 'github.com'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L61-L65)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L81-L84)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L100-L103)

#### nsplit

```
array nsplit(string $string)
```

Split line by line given string. Each line is trimmed, empty ones are filtered out.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L112-L119)

#### raw2int

```
int raw2int(string $raw)
```

Unpack binary string to integer value.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L128-L131)

#### int2raw

```
string int2raw(int $int, int $bytes = 4)
```

Pack an integer value to its binary representation.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L141-L145)

#### is_closure

```
bool is_closure(mixed $closure)
```

Return whether or not the provided object is callable.

```php
$is_closure = is_closure(function () { ; });
// $is_closure => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L159-L162)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L180-L187)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L205-L213)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L229-L235)

#### str_replace_once

```
string str_replace_once(string $needle, string $replace, string $haystack)
```

String replace. Replace is applied only once.

```php
$str = str_replace_once('foo', 'bar', 'foo foo');
// $str => 'bar foo'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L251-L258)

#### str_truncate

```
string str_truncate(
    string $string,
    int $length = 40,
    bool $center = false,
    string $replacer = '...'
)
```

Truncate string to certain length (be default 40 chars).

```php
$str = str_truncate('Hello, world!', 5);
// $str => 'He...'
$str = str_truncate('Hello, world!', 5, true);
// $str => 'H...!'
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L277-L297)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L317-L321)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L341-L348)

#### template

```
string template(string $tpl, array $args = [])
```

Native PHP templating engine.

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
echo template('test.tpl', [
    'title' => 'Test Title',
    'body' => '<h1>Hello!</h1>',
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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L390-L396)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L418-L432)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L458-L485)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L507-L555)

#### setenv

```
bool setenv(string $name, string $value, string $file = DOTENV_FILE)
```

Used to set environment variable inside `.env` file. 
If you ignore third argument, `.env` file is taken from `DOTENV_FILE` constant.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L567-L583)

#### _T

```
string _T(string $var, string $lang = LANG, string $lang_file = LANG_FILE)
```

Used to return a value from translation map. 
Function optionally receives secord argument (`LANG`) and third argument (`LANG_FILE`).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L595-L603)

#### url_base64_encode

```
string url_base64_encode(string $string)
```

Encode string to URL-safe Base64 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L612-L615)

#### url_base64_decode

```
string url_base64_decode(string $string)
```

Decode from URL-safe Base64 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L624-L635)

#### xencrypt

```
string xencrypt(string $string, string $key)
```

XOR encryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L645-L655)

#### xdecrypt

```
string xdecrypt(string $string, string $key)
```

XOR decryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L665-L680)

#### oencrypt

```
string oencrypt(string $string, string $key)
```

Implements OpenSSL encryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L690-L697)

#### odecrypt

```
string odecrypt(string $string, string $key)
```

Implements OpenSSL decryption.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L707-L720)

#### base32_decode

```
string base32_decode(string $string)
```

Decode string from Base32 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L729-L738)

#### base32_encode

```
string base32_encode(string $string)
```

Encode string in Base32 format.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L747-L764)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L781-L873)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L894-L907)

#### xpath

```
array|string xpath(
    DOMNode|string $xml,
    string $query = '/*',
    callable|int|null $callback = null,
    array $flags = []
)
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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L966-L1051)

#### mb_ucfirst

```
string mb_ucfirst(string $string)
```

Upper case of first letter.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1060-L1066)

#### sanitize_html

```
string sanitize_html(string $content)
```

Satinize HTML output.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1075-L1142)

#### readable_to_variable

```
mixed readable_to_variable(string $input)
```

Transforms readable form of string to variable.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1151-L1197)

#### to_storage

```
string to_storage(string $file, array $settings = [])
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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1220-L1259)

#### curl

```
Generator curl(array $urls, array $settings = [])
```

All-in-one cURL function with multi threading support.

```php
$result = curl(['http://github.com']);
[$github] = iterator_to_array($result, true);
preg_match('~<title>(.*?)</title>~', $github['content'], $title);
$title = $title[1];
// $title => 'The world&#39;s leading software development platform · GitHub'
```

For more examples, please, refer to [curl.md](docs/curl.md).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1279-L1425)

#### parse_ssh_config

```
array parse_ssh_config(string $content)
```

Parses ~/.ssh/config content. 
Returns associative array where Host comes as key.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1435-L1453)

#### get_domain_info

```
array get_domain_info(string $domain)
```

Returns [
    'suffix' => '*.bd',
    'suffix_match' => 'mil.bd',
    'domain' => 'army',
    'tld' => 'bd',
] for 'army.mil.bd'

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1467-L1544)

#### is_same_suffix_domains

```
bool is_same_suffix_domains(string $domain1, string $domain2)
```

Returns true, if domains have same suffix.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1554-L1572)

#### extract_links_from_html

```
array extract_links_from_html(string $html, string $url = '')
```

Extract links (anchors + resource) from HTML.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1582-L1640)

#### extract_metas_from_html

```
array extract_metas_from_html(string $html)
```

Extract all `<meta/>` tags from HTML.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1649-L1670)

#### extract_comments_from_html

```
array extract_comments_from_html(string $html)
```

Extract comments from HTML.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1679-L1691)

#### extract_scripts_from_html

```
array extract_scripts_from_html(string $html)
```

Extract scripts from HTML.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1700-L1719)

#### extract_texts_from_html

```
array extract_texts_from_html(string $html)
```

Extract text values from HTML.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1728-L1743)

#### extract_inputs_from_html

```
array extract_inputs_from_html(string $html)
```

Extract all `<input/>` tags from HTML.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1752-L1773)

#### extract_keywords_from_html

```
array extract_keywords_from_html(
    string $html,
    array $tags = ['title', 'h1', 'meta']
)
```

Extract possible keywords from HTML.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1783-L1816)

#### extract_all_from_html

```
array extract_all_from_html(
    string $html,
    string $url = '',
    array $tags = ['title', 'h1', 'meta']
)
```

Extract all possible objects from HTML.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1827-L1842)

#### crawler

```
array crawler(array $urls, array $settings = [])
```

Crawl recursively a domain.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1852-L1979)

#### quick_blast

```
array quick_blast(array $strings, int $m, array $settings = [])
```

1-threaded implementation of BLAST algorithm. 
Supports multiple strings.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L1991-L2270)

#### highlight_quick_blast_results

```
string highlight_quick_blast_results(
    string $string,
    int $index,
    array $results,
    int $context_length = 16,
    array $highlights = [['<em>', '</em>']]
)
```

Easy way to highlight BLAST results.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L2283-L2400)

#### string_generator

```
Generator string_generator(string $string, array $settings = [])
```

Easy pattern-based generator of strings.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L2410-L2523)

#### simple_lexer

```
array simple_lexer(string $string, array $rules)
```

Simple lexer...

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L2533-L2555)

#### get_rpn

```
array get_rpn(
    string $string,
    string $values = '~\d+(\.\d+,
    array $operators = ['+' => 1, '-' => 1, '*' => 2, '/' => 2],
    string $parentheses = '(
)
```

Get Reverse Polish notation for expression.

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L2567-L2626)

### Authors

- [Evgeny Cernisev](https://ejz.io) | [GitHub](https://github.com/Ejz) | <ejz@ya.ru>

### License

[functions](https://github.com/Ejz/functions) is licensed under the [WTFPL License](https://en.wikipedia.org/wiki/WTFPL) (see [LICENSE](LICENSE)).

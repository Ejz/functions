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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L19-L25)

#### fesc

Encode/decode HTML chars in given string: `>`, `<`, `&`, `'` and `"`. 
Use this function to escape HTML tags atrribute values.

```php
$s = fesc("HTML: <>&, '\"");
// $s => "HTML: &lt;&gt;&amp;, &#039;&quot;"
$s = esc($s, $decode = true);
// $s => "HTML: <>&, '\""
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L38-L44)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L85-L91)

#### is_host

Validate a hostname (an IP address or domain name).

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L96-L104)

#### host

Get hostname from URL.

```
string host(string $url);
```

```php
$host = host("https://github.com/");
// $host => "github.com"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L118-L121)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L137-L139)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L155-L157)

#### nsplit

Split line by line given string. Each line is trimmed, empty ones are filtered out.

```
array nsplit(string $string);
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L166-L172)

#### is_closure

Return whether or not the provided object is callable.

```
bool is_closure(object $object);
```

```php
$bool = is_closure(function() { ; });
// $bool => true
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L186-L188)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L205-L211)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L235-L240)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L259-L263)

#### str_replace_once

String replace. Replace is applied only once.

```
string str_replace_once(string $needle, string $replace, string $haystack);
```

```php
$str = str_replace_once("foo", "bar", "foo foo");
// $str => "bar foo"
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L277-L281)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L300-L314)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L329-L345)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L374-L379)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L408-L415)

#### rand_from_string

Get random integer from string.

```
int rand_from_string(string $string);
```

```php
$int = rand_from_string("one");
// $int => 48928
```

```php
$int = rand_from_string("two");
// $int => 48928
```

```php
$int = rand_from_string("one");
// $int => 48928
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L439-L445)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L474-L491)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L512-L530)

#### prepare_tag_attributes

Prepare attributes for outputing in HTML tag.

```
string prepare_tag_attributes(array $attributes);
```

```php
$attributes = ["href" => "/link.html?a=1&b=2", "class" => ["_left", "_clearfix"]];
$prepared = prepare_tag_attributes($attributes);
// $prepared => "href=\"/link.html?a=1&amp;b=2\" class=\"_left _clearfix\""
```

```php
$attributes = ["style" => ["margin-top" => "0", "display" => "flex"]];
$prepared = prepare_tag_attributes($attributes);
// $prepared => "style=\"margin-top:0;display:flex;\""
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L551-L570)

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

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L594-L624)

#### xpath

Wrapper around [DOMXPath](http://php.net/manual/en/class.domxpath.php). 
Accepts XPath queries for extracting tags and callback function for tag manipulating.

```
array|string xpath(string|DOMNode $xml, string $query = '/*', callable|int|null $callback = null, array $flags = []);
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


[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L671-L763)

#### _expect

Raise an error, if given variable does not match type.

```
_expect(mixed $var, string $types);
```

```php
$a = 'string';
_expect($a, 'string|null');
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L777-L793)

#### _log

Raise a user defined error with a message. 
Shortcut to `trigger_error()` function. 
For CLI mode outputs to `STDERR` instead of raising an error. 
If `E_USER_ERROR` is raised, exit with error code `1`.

```
_log(string $msg, int $level = E_USER_NOTICE);
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L805-L825)

#### _warn

Raise `E_USER_WARNING` with a message.

```
_warn(string $msg);
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L834-L836)

#### _err

Raise `E_USER_ERROR` with a message. Exit with error code `1`.

```
_err(string $msg);
```

[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L845-L847)

### Authors

- [Evgeny Cernisev](https://ejz.ru) | [GitHub](https://github.com/Ejz) | <ejz@ya.ru>

### License

[functions](https://github.com/Ejz/functions) is licensed under the [WTFPL License](https://en.wikipedia.org/wiki/WTFPL) (see [LICENSE](LICENSE)).

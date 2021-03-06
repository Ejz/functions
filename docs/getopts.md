## getopts

### Arguments with single value

```php
$argv = explode(" ", "./exec.sh -a foo arg --long bar --another=value");
$opts = getopts(['a' => true, 'long' => true, 'another' => true], $argv);
print_r($opts);
```

Output:

```
Array
(
    [0] => ./exec.sh
    [a] => foo
    [1] => arg
    [long] => bar
    [another] => value
)
```

### Long arguments

Can have 3 different forms:

* `--long=value`
* `--long`
* `-long`

Please, note, that variant with one `-` in some cases can be ambiguous.

### Arguments with multiple value

```php
$argv = explode(" ", "./exec.sh -v -v -v -Ax -Ay -Az");
$opts = getopts([
    'v' => ['multiple' => true],
    'A' => ['multiple' => true, 'value' => true],
], $argv);
print_r($opts);
```

Output:

```
Array
(
    [0] => ./exec.sh
    [v] => Array
        (
            [0] => 1
            [1] => 1
            [2] => 1
        )
    [A] => Array
        (
            [0] => x
            [1] => y
            [2] => z
        )
)
```

### Aliases

Your can freely assign aliases within configuration array.

```php
$argv = explode(" ", "./exec.sh -v foo --v-alias bar");
$opts = getopts([
    'v' => ['multiple' => true, 'value' => true],
    'v-alias' => 'v',
], $argv);
print_r($opts);
```

Output:

```
Array
(
    [0] => ./exec.sh
    [v] => Array
        (
            [0] => foo
            [1] => bar
        )
)
```

### Exclusive arguments

There are situations when some arguments are mutually exclusive. For example, one argument enables (`--enable`), another disables (`--disable`).

```php
$argv = explode(" ", "./exec.sh --enable --disable --enable");
$opts = getopts([
    'enable' => false,
    'disable' => false,
], $argv);
print_r($opts);
```

Output:

```
Array
(
    [0] => ./exec.sh
    [disable] => true
    [enable] => true
)
```

In this case, refer to argument positioning inside associative array.

```php
$def = true; // default
$keys = array_keys($opts);
$e = !empty($opts['enable']);
$d = !empty($opts['disable']);
$is_enabled = (
    ($def and !$e and !$d) or
    ($e and !$d) or
    ($e and $d and (array_search('enable', $keys, true) > array_search('disable', $keys, true)))
);
```

### Possible errors

In case of error function returns error message as string instead of array.

```php
echo getopts([], explode(" ", "./exec.sh -k")) . "\n";
echo getopts(['k' => true], explode(" ", "./exec.sh -k")) . "\n";
```

Output:

```
Unknown argument k!
No value for argument k!
```

## xpath

### Find by class name

```php
$xml = "<root><a class='one findme two'>1</a><b>2</b><c>3</c></root>";
$xml = xpath($xml, '//*[class(findme)]');
print_r($xml);
```

Output:

```
Array
(
    [0] => <a class="one findme two">1</a>
)
```

### Remove tag

```php
$xml = "<root><a>1</a><b>2</b><c>3</c></root>";
$xml = xpath($xml, '//b', "xpath_callback_remove");
echo $xml, "\n";
```

Output:

```
<root>
  <a>1</a>
  <c>3</c>
</root>
```

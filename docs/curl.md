## curl

### Get bunch of URLs

```php
$urls = ['http://google.com', 'http://github.com', 'http://yandex.ru'];
$result = curl($urls);
print_r($result);
```

Output:

```
Array
(
    [http://google.com] => <!doctype html>...
    [http://github.com] => <!DOCTYPE html>...
    [http://yandex.ru] => <!DOCTYPE html>...
)
```

### Accept only 200

By default 404 responses are accepted. Change it using `checker` parameter in settings:

```php
$urls = ['http://ejz.io', 'http://ejz.io/404'];
$result = curl($urls, ['checker' => [200, 201, 202]]);
print_r($result);
```

Output:

```
Array
(
    [http://ejz.io] => <!DOCTYPE html>...
)
```

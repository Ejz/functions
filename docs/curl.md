## Curl Examples

### Get a bunch of URLs

```php
$urls = ['http://google.com', 'http://github.com', 'http://yandex.ru'];
$result = curl($urls);
print_r($result);
```

Output:

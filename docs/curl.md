## curl

### Get bunch of URLs

```php
$urls = ['http://github.com'];
$result = curl($urls);
[$github] = iterator_to_array($result, true);
print_r($github);
```

Output:

```
Array
(
    [value] => http://github.com
    [content] => <!DOCTYPE html>
                 <html lang="en">
                 </html>
    [header] => HTTP/1.1 301 Moved Permanently
                Content-length: 0
                Location: https://github.com/

                HTTP/1.1 200 OK
                Server: GitHub.com
                Date: Sat, 27 Oct 2018 20:28:39 GMT
                Content-Type: text/html; charset=utf-8
                Transfer-Encoding: chunked
                Status: 200 OK
                Cache-Control: no-cache
    [headers] => Array
        (
            [0] => Array
                (
                    [status] => 301
                    [content-length] => 0
                    [location] => https://github.com/
                )
            [1] => Array
                (
                    [status] => 200
                    [server] => GitHub.com
                    [date] => Sat, 27 Oct 2018 20:28:39 GMT
                    [content-type] => text/html; charset=utf-8
                    [transfer-encoding] => chunked
                    [cache-control] => no-cache
                )
        )
    [error] =>
    [errno] => 0
    [url] => https://github.com/
    [content_type] => text/html; charset=utf-8
    [http_code] => 200
    [header_size] => 2248
    [request_size] => 295
    [filetime] => -1
    [ssl_verify_result] => 0
    [redirect_count] => 1
    [total_time] => 5.522191
    [namelookup_time] => 0.250541
    [connect_time] => 0.501113
    [pretransfer_time] => 1.014503
    [size_upload] => 0
    [size_download] => 17892
    [speed_download] => 3240
    [speed_upload] => 0
    [download_content_length] => -1
    [upload_content_length] => -1
    [starttransfer_time] => 1.515206
    [redirect_time] => 0.751509
    [redirect_url] =>
    [primary_ip] => 192.30.253.113
    [certinfo] => Array
        (
        )
    [primary_port] => 443
    [local_ip] => 192.168.0.1
    [local_port] => 45708
    [19913] => 1
    [52] => 1
    [10102] =>
    [10018] => Mozilla/5.0 (Windows NT 6.1) Chrome/60
    [58] => 1
    [78] => 5
    [13] => 10
    [68] => 5
    [64] => 0
    [81] => 0
    [42] => 1
    [10002] => http://github.com
)
```

`10002` value above is `CURLOPT_URL`, `10018` is `CURLOPT_USERAGENT`, etc

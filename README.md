# php http proxy

## Quick Start

#### step 1
create composer.json
```json
{
    "require": {
        "colindev/proxy": "v1.*"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/colindev/php-proxy.git"
        }
    ]
}
```

#### step 2

```bash
$ composer install
```

#### step 3
create example.php
```php

<?php

include __DIR__.'/vendor/autoload.php';

$proxy = new proxy\Proxy('http://host.of.target[:port]');
// $proxy->prefix('/prefix');
$res = $proxy->exec($_SERVER);

foreach ($res->headers as $k => $vs) {
    foreach ($vs as $i => $v) {
        header("${k}: ${v}");
    }
}
echo $res->body;
```

#### step 4
```bash
$ php -S 0.0.0.0:8000 example.php
```

#### step 5

[open browser](http://127.0.0.1:8000/)

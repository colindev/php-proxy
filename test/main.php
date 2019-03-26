<?php

include __DIR__.'/vendor/autoload.php';
//include __DIR__.'/../bin/proxy.phar.gz';

$proxy = new proxy\Proxy('http://xxx');
$res = $proxy->exec($_SERVER);

foreach ($res->headers as $k => $vs) {
    foreach ($vs as $i => $v) {
        header("${k}: ${v}");
    }
}
echo $res->body;

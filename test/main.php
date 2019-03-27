<?php

include __DIR__.'/../bin/proxy.phar.gz';

$proxy = new proxy\Proxy('http://xxx');
$proxy->prefix('/abc');
$res = $proxy->exec($_SERVER);

foreach ($res->headers as $k => $vs) {
    foreach ($vs as $i => $v) {
        header("${k}: ${v}");
    }
}
http_response_code($res->code);
echo $res->body;

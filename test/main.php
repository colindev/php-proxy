<?php

include __DIR__.'/../bin/proxy.phar.gz';

if (!array_key_exists('PROXY', $_ENV)) {
    die('please specify proxy target via env "PROXY"');
}

$proxy = new proxy\Proxy($_ENV['PROXY']);
$proxy->log = new proxy\Log('php://stdout');
$proxy->log->level(proxy\Log::INFO);
$proxy->prefix('/abc');
$res = $proxy->exec($_SERVER);

foreach ($res->headers as $k => $vs) {
    foreach ($vs as $i => $v) {
        header("${k}: ${v}");
    }
}
http_response_code($res->code);
echo $res->body;

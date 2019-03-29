<?php

include __DIR__.'/../bin/proxy.phar.gz';

if (!array_key_exists('PROXY', $_ENV)) {
    die('please specify proxy target via env "PROXY"');
}

$target = $_ENV['PROXY'];
$prefix = $_ENV['PROXY_PREFIX'];
$vvv = proxy\Log::ERROR;
switch (strtolower($_ENV['PROXY_LOG_VERBOSE'])){
    case 'info':
        $vvv = proxy\Log::INFO;
        break;
    case 'warnning':
        $vvv = proxy\Log::WARN;
        break;
}

$proxy = new proxy\Proxy($target);
$proxy->log = new proxy\Log('php://stdout');
$proxy->log->verbose($vvv);
if (!!$prefix) $proxy->prefix($prefix);
$res = $proxy->exec($_SERVER);

foreach ($res->headers as $k => $vs) {
    foreach ($vs as $i => $v) {
        header("${k}: ${v}");
    }
}
http_response_code($res->code);
echo $res->body;

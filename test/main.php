<?php

include __DIR__.'/../bin/proxy.phar.gz';

$target = array_key_exists('PROXY', $_ENV)? $_ENV['PROXY'] : die('please specify proxy target via env "PROXY"');
$prefix = array_key_exists('PROXY_PREFIX', $_ENV)? $_ENV['PROXY_PREFIX'] : '';
$verbose = array_key_exists('PROXY_LOG_VERBOSE', $_ENV)? $_ENV['PROXY_LOG_VERBOSE'] : '';
$vvv = proxy\Log::ERROR;
switch (strtolower($verbose)){
    case 'info':
        $vvv = proxy\Log::INFO;
        break;
    case 'warnning':
        $vvv = proxy\Log::WARN;
        break;
}

$log = new proxy\Log('php://stdout');
$log->verbose($vvv);

$proxy = new proxy\Proxy($target);
$proxy->log = $log;
if (!!$prefix) $proxy->prefix($prefix);
$res = $proxy->exec($_SERVER);

foreach ($res->headers as $k => $vs) {
    foreach ($vs as $i => $v) {
        header("${k}: ${v}");
    }
}
http_response_code($res->code);
echo $res->body;

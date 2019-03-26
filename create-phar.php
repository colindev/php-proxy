<?php

$phar = './bin/proxy.phar';

if (file_exists($phar)) {
    unlink($phar);
}

if (file_exists($phar.'.gz')) {
    unlink($phar.'.gz');
}

try {
    $o = new Phar($phar);
    $o->buildFromDirectory('./src');
    $o->setDefaultStub('/index.php');
    $o->compress(Phar::GZ);
    echo "compile success", PHP_EOL;
} catch(Exception $e) {
    echo $e;
}


<?php

$phar = './bin/proxy.phar';

if (file_exists($phar)) {
    unlink($phar);
    echo "unlink ${phar}", PHP_EOL;
}

if (file_exists($phar.'.gz')) {
    unlink($phar.'.gz');
    echo "unlink ${phar}.gz", PHP_EOL;
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


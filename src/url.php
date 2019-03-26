<?php namespace proxy;

class URL {
    public $path;
    public $host;
    public $scheme;
    public $queryString;

    public function __toString() {
        return $this->scheme.
            '://'.
            $this->host.
            $this->path.'?'.
            $this->queryString;
    }
}

function parseURL($url) :URL {

    preg_match('/^(https?):\/\/([^\/?]+)(\/[^?]*)?(\?.*)?$/', $url, $m);
    if (!$m) {
        throw new \Exception('error url'); 
    }

    $ret = new URL;
    $ret->scheme = $m[1];
    $ret->host = $m[2];
    $ret->path = $m[3];
    $ret->queryString = preg_replace('/^\?/', '', $m[4]);
    return $ret;
}

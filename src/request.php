<?php namespace proxy;

class Request {
    public $method;
    public $URL;
    public $headers = array();
    public $body = '';

    public function __construct($method, $url, $body = '') {
        $this->method = strtoupper($method);
        $this->URL = parseURL($url);
        $this->headers['Host'] = $this->URL->host;
        $this->body = $body;
    }

    public function __toString() {
        $h = ["{$this->method} {$this->URL->path} HTTP/1.1"];
        foreach ($this->headers as $k => $v) {
            $h[] = canonicalHeaderKey($k).': '.$v;
        }
        $h[] = '';

        return join($h, "\r\n")."\r\n".$this->body;
    }
}

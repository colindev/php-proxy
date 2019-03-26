<?php namespace proxy;

class Request {
    public $URL;
    public $headers = array();
    public $body = '';

    public function __construct($url, $body = '') {
        $this->URL = parseURL($url);
        $this->body = $body;
    }
}

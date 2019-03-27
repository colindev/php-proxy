<?php namespace proxy;

class Response {
    public $code = 0;
    public $req;
    public $body = '';
    public $headers = array();

    public function __construct(Request $req) {
        $this->req = $req;
    }

    public function header($k) {
        $k = canonicalHeaderKey($k);
        if (!$this->headers || !array_key_exists($k, $this->headers)) {
            return '';
        }

        return $this->headers[$k][0];
    }
}

<?php namespace proxy;

class Proxy {

    private $processes = [];

    public function __construct($url) {
        $url = parseURL($url);
        $this->processes[] = function(Request $req) use($url) {
            $req->URL->scheme = $url->scheme;
            $req->URL->host = $url->host;
        };
    }

    public function stripPrefix($prefix) {
        $this->processes[] = function(Request $req) use($prefix) {

            $prefix = preg_replace('/\/*$/', '', $prefix);
            $prefix = preg_replace('/\//', '\/', $prefix);
            $req->URL->path = preg_replace('/^'.$prefix.'/', '', $req->URL->path);
        };
    }

    // $s: $_SERVER
    public function exec($s, $headers = array()) {

        switch ($s['REQUEST_METHOD']) {
            case 'GET':
                break;
            default:
                throw new \Exception('only support GET');
        }

        $currentHeaders = getallheaders();
        foreach ($currentHeaders as $k => $v) {
            if (array_key_exists($k, $headers)) {
                continue;
            }
            $headers[$k] = $v;
        }

        $req = new Request("http://${s['HTTP_HOST']}${s['REQUEST_URI']}", '');
        $req->headers = $headers;

        foreach ($this->processes as $i => $fn) {
            $fn($req);
        }

        $res = new Response;
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $req->URL);
        curl_setopt($c, CURLOPT_HTTPHEADER, $req->headers);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT_MS, 200);
        curl_setopt($c, CURLOPT_HEADERFUNCTION, function($curl, $header) use($res) {

            $len = strlen($header);
            $h = explode(':', $header, 2);
            if (count($h) == 2) {
                $k = trim($h[0]);
                $v = trim($h[1]);
                if ( ! array_key_exists($k, $res->headers)) {
                    $res->headers[$k] = [];
                }
                $res->headers[$k][] = $v;
            }
            return $len;
        });

        $res->body = curl_exec($c);
        $err = curl_error($c);
        curl_close($c);

        if ($err) {
            throw new \Exception($err);
        }

        return $res;
    }
}


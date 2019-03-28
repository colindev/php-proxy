<?php namespace proxy;

class Proxy {

    const init = 1;
    const prefix = 2;

    private $beforeProcess = array();
    private $afterProcess = array();
    public $connTimeoutMS = 1000;
    public $log;

    public function __construct($url) {
        $url = parseURL($url);
        $this->beforeProcess[self::init] = function(Request $req) use($url) {
            $req->URL->scheme = $url->scheme;
            $req->URL->host = $url->host;
        };
    }

    public function prefix($prefix) {

        $this->afterProcess[self::prefix] = function(Response $res) use($prefix) {

            switch (preg_replace('/;.*$/', '', $res->header('content-type'))) {
            case 'text/html':
            $res->body = preg_replace_callback('/(src|href)\s*=\s*([\'"])([^\'"]*)/', function($m) use($prefix) {
                $mm = null;
                preg_match('/^(https?:\/\/|#)/', $m[3], $mm);
                if ($mm) {
                    return $m[0];
                }
                if (strpos($m[3], '/') !== 0) {
                    return $m[0];
                }
                $newLine = $m[1].'='.$m[2].$prefix.'/'.ltrim($m[3], '/');
                return $newLine;
            }, $res->body);
                break;

            default:
            // do nothing
                break;
            }
            
        };
        $prefix = preg_replace('/\/*$/', '', $prefix);
        $prefix = preg_replace('/\//', '\/', $prefix);
        $this->beforeProcess[self::prefix] = function(Request $req) use($prefix) {
            $req->URL->path = preg_replace('/^'.$prefix.'/', '', $req->URL->path);
        };
    }

    // $s: $_SERVER
    public function exec($s, $headers = array()) {

        $method = $s['REQUEST_METHOD'];
        $host = $s['HTTP_HOST'];
        $uri = $s['REQUEST_URI'];

        switch ($method) {
            case 'GET':
                break;
            default:
                throw new \Exception('only support GET');
        }

        foreach ($headers as $k => $v) {
            $headers[canonicalHeaderKey($k)] = $v;
        }
        $currentHeaders = getallheaders();
        foreach ($currentHeaders as $k => $v) {
            $k = canonicalHeaderKey($k);
            if (array_key_exists($k, $headers)) {
                continue;
            }
            $headers[$k] = $v;
        }

        $req = new Request("http://${host}${uri}", '');
        $req->headers = $headers;

        foreach ($this->beforeProcess as $fn) {
            $fn($req);
        }

        $res = new Response($req);

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $req->URL);
        curl_setopt($c, CURLOPT_HTTPHEADER, $req->headers);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT_MS, $this->connTimeoutMS);
        curl_setopt($c, CURLOPT_HEADERFUNCTION, function($curl, $header) use($res) {

            $len = strlen($header);
            if (!$len) return $len;
            if ($this->log) $this->log->write(Log::INFO, $res->req->URL->path.'| '.$header);
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
        $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $err = curl_error($c);
        curl_close($c);

        if ($err) {
            throw new \Exception($err);
        }

        $res->code = $code;
        foreach ($this->afterProcess as $fn) {
            $fn($res);
        }

        return $res;
    }
}


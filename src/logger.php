<?php namespace proxy;

interface Logger {
    public function write(int $level, string $msg);
}

class Log implements Logger {

    const ERROR = 0;
    const WARN = 1;
    const INFO = 2;

    private $verbose = self::ERROR;
    private $output;

    public function __construct($output) {
        $this->output = fopen($output, 'w');
    }
    public function __destruct() {
        fclose($this->output);
    }

    public function verbose(int $vvv) {
        $this->verbose = $vvv;
    }

    public function write(int $vvv, string $msg) {
        if ($vvv > $this->verbose) return;
        fwrite($this->output, $msg);
    }
    
}

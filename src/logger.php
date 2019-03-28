<?php namespace proxy;

interface Logger {
    public function write(int $level, string $msg);
}

class Log implements Logger {

    const INFO = 0;
    const WARN = 1;
    const ERROR = 2;

    private $level = self::ERROR;
    private $output;

    public function __construct($output) {
        $this->output = fopen($output, 'w');
    }
    public function __destruct() {
        fclose($this->output);
    }

    public function level(int $lv) {
        $this->level = $lv;
    }

    public function write(int $lv, string $msg) {
        if ($lv < $this->level) return;
        fwrite($this->output, $msg);
    }
    
}

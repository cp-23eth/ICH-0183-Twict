<?php

namespace App\Libs;

use Monolog\Handler\StreamHandler;
use Monolog\Level;

class LogHandler extends \Monolog\Logger
{
    private static $loggers = [];

    private function __construct($key)
    {
        parent::__construct($key);

        $filename = __DIR__ . '/../../logs/twict.log';
        $level = Level::Info;

        $this->pushHandler(new StreamHandler($filename, $level));
    }

    public static function get($key = 'app')
    {
        if (empty(self::$loggers[$key])) {
            self::$loggers[$key] = new LogHandler($key);
        }

        return self::$loggers[$key];
    }
}

<?php

namespace OsdAurox;

class Log
{
    public static ?Log $logger_instance = null;
    public string $path = '';
    public string $level = '';
    private function __construct()
    {
        // sigleton
    }

    private function __clone()
    {
        // sigleton
    }

    private function init($path = '/logs/app.log', $level='warning')
    {
        $logger = new Log();
        $logger->path = APP_ROOT . $path;
        $logger->level = $level;
        return $logger;
    }
    public static function getInstance($path = '/logs/app.log', $level='warning')
    {
        if (!self::$logger_instance) {
            $o_logger = new self();
            self::$logger_instance = $o_logger->init(path : $path, level : $level);
        }

        return self::$logger_instance;
    }

    private function writeLog($message, $type)
    {
        // open file
        $date = date('Y-m-d H:i:s');
        $message = "$date : $type : $message" . PHP_EOL;
        if(!is_dir(dirname($this->path))){
            mkdir(dirname($this->path), 0777, true);
        }
        file_put_contents($this->path, $message, FILE_APPEND);
    }

    public static function debug(string $message): void
    {
        $instance = self::getInstance();
        $instance->writeLog($message, 'debug');
    }

    public static function info(string $message): void
    {
        $instance = self::getInstance();
        $instance->writeLog($message, 'info');
    }

    public static function warning(string $message): void
    {
        $instance = self::getInstance();
        $instance->writeLog($message, 'warning');
    }

    public static function error(string $message): void
    {
        $instance = self::getInstance();
        $instance->writeLog($message, 'error');
    }


}
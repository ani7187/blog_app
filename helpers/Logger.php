<?php

namespace helpers;

class Logger
{
    /**
     * @var string
     */
    private static string $logDirPath = __DIR__ . "/../storage/log";

    public static function log(string $message, string $logFile, string $type = "INFO"): void
    {
        $logMessage = "[" . date('Y-m-d H:i:s') . "] [$type]: $message" . PHP_EOL;

        if (!file_exists(self::$logDirPath)) {
            mkdir(self::$logDirPath, 0775, true);
        }

        $logDir = dirname($logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0775, true);
        }

        file_put_contents(self::$logDirPath . '/' . $logFile, $logMessage, FILE_APPEND);
    }

    /**
     * @param string $message
     * @param string $logFile
     * @return void
     */
    public static function error(string $message, string $logFile = 'error.log'): void
    {
        self::log($message, $logFile, 'ERROR');
    }

    /**
     * @param string $message
     * @param string $logFile
     * @return void
     */
    public static function info(string $message, string $logFile = 'info.log'): void
    {
        self::log($message, $logFile, 'INFO');
    }

    /**
     * @param string $message
     * @param string $logFile
     * @return void
     */
    public static function warning(string $message, string $logFile = 'warning.log'): void
    {
        self::log($message, $logFile, 'WARNING');
    }

}
<?php

namespace App\Infrastructure;

use App\Core\Config as Config;

class Logger
{
    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    /**
     * Log a debug message (only if app.debug is true).
     */
    public static function debug(string $message, array $context = []): void
    {
        // Only write debug lines in non-production environments
        if (!Config::isProduction()) {
            self::write('DEBUG', $message, $context);
        }
    }

    /**
     * The core writing logic.
     */
    private static function write(string $level, string $message, array $context = []): void
    {
        //Fetch paths from Config at runtime to ensure we have the latest settings
        $logDir = Config::get('paths.logs');
        $logFile = $logDir . '/app.log';

         // Ensure directory and file exist and are writable
         if (!is_dir($logDir)) {
             mkdir($logDir, 0777, true);
         }  else if (!is_writable($logDir)) {
             // If the directory exists but isn't writable, log to PHP's error log instead
             error_log("Logger Error: Log directory is not writable: {$logDir}");
             return;
         }else if (!file_exists($logFile)) {
             // If the log file doesn't exist, create it
             touch($logFile);
         } else if (file_exists($logFile) && !is_writable($logFile)) {
             // If the log file exists but isn't writable, log to PHP's error log instead
             error_log("Logger Error: Log file is not writable: {$logFile}");
             return;
         }

         // Format the error message
         $date             = date('d-m-Y H:i:s');
         $ctxt             = empty($context) ? '' : ' ' . json_encode($context);
         $formattedMessage = "[{$date}] [{$level}] {$message}{$ctxt}" . PHP_EOL;

         // Append to file
         file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }
}
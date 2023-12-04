<?php

namespace ServerMonitor;

/**
 * Class Log
 *
 * This class provides logging functionality for the server monitor application.
 */
class Log
{
    /**
     * Ensures that the log directory exists. If not, it creates the directory.
     *
     * @return void
     */
    private static function ensureLogDirectoryExists()
    {
        // Ensure the log directory exists
        if (!is_dir(LOG_DIR)) {
            mkdir(LOG_DIR, 0755, true); // Create the directory with proper permissions
        }
    }

    /**
     * Logs a message to the log file.
     *
     * @param string $message The message to be logged.
     *
     * @return void
     */
    public static function log($message)
    {
        self::ensureLogDirectoryExists();

        $timestamp = date('[Y-m-d H:i:s]');
        $logEntry = "$timestamp $message\n";

        // Append the log entry to the log file
        file_put_contents(LOG_DIR . '/ServerMonitor.log', $logEntry, FILE_APPEND);
    }

    /**
     * Logs a message to the log file and echoes the message to the console.
     *
     * @param string $message The message to be logged and echoed.
     *
     * @return void
     */
    public static function logMessage($message)
    {
        self::ensureLogDirectoryExists();
        self::log($message);
        echo $message . PHP_EOL;
    }

    /**
     * Logs an exception message, file, line, and optionally terminates the script.
     *
     * @param \Exception $exception The exception to be logged.
     * @param bool $kill Whether to terminate the script after logging the exception.
     *
     * @return void
     */
    public static function logException(\Exception $exception, $kill)
    {
        self::ensureLogDirectoryExists();

        $message = "Exception: " . $exception->getMessage() .
                   " File: " . $exception->getFile() .
                   " Line: " . $exception->getLine();

        self::log($message);
        echo $message . PHP_EOL;
        if ($kill === true) {
            echo ' Script terminated.' . PHP_EOL;
            echo '**** See ServerMonitor.log for full details. *****' .  PHP_EOL;
            exit('exit from logException');
        }
    }

    /**
     * Logs an error message, file, and line, and terminates the script.
     *
     * @param int $errno The error number.
     * @param string $errstr The error message.
     * @param string $errfile The file in which the error occurred.
     * @param int $errline The line number where the error occurred.
     *
     * @return void
     */
    public static function logError($errno, $errstr, $errfile, $errline)
    {
        self::ensureLogDirectoryExists();

        $message = "Error: $errstr in $errfile on line $errline";
        self::log($message);
        echo $message . ' Script terminated.' . PHP_EOL;
        echo '**** See ServerMonitor.log for full details. *****' .  PHP_EOL;
        exit('Exit from logError');
    }
}

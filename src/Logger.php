<?php

namespace struktal\Logger;

class Logger {
    private static ?string $logDirectory = null;
    private static array $instances = [];
    private static array $customLogHandlers = [];
    private static LogLevel $minLogLevel = LogLevel::TRACE;

    public static function setLogDirectory(string $directory): void {
        self::$logDirectory = rtrim($directory, '/') . '/';

        // Ensure the log directory exists
        if(!file_exists(self::$logDirectory) || !is_dir(self::$logDirectory)) {
            mkdir(self::$logDirectory, 0755, true);
        }
    }

    public static function addCustomLogHandler(LogLevel $logLevel, callable $handler): void {
        if(!isset(self::$customLogHandlers[$logLevel->value])) {
            self::$customLogHandlers[$logLevel->value] = [];
        }

        self::$customLogHandlers[$logLevel->value][] = $handler;
    }

    public static function setMinLogLevel(LogLevel $level): void {
        self::$minLogLevel = $level;
    }

    private string $tag;

    public function __construct(string $tag) {
        $this->tag = $tag;
    }

    private function __destruct() {
        // Close the logfile if it is opened
        if(self::$logfile !== null) {
            fclose(self::$logfile);
            self::$logfile = null;
        }
    }

    public static function tag(string $tag): Logger {
        if(!isset(self::$instances[$tag])) {
            self::$instances[$tag] = new Logger($tag);
        }

        return self::$instances[$tag];
    }

    /** @var resource|null */
    private static $logfile = null;
    private static function openLogfile(): void {
        // Check if the logfile is opened already
        if(self::$logfile !== null) {
            return;
        }

        // Open the logfile
        $logfileName = str_replace("%date%", date("Y-m-d"), "log-%date%.log");
        $logfilePath = self::$logDirectory . $logfileName;
        self::$logfile = fopen($logfilePath, "a");
    }

    private function log(LogLevel $level, mixed $message): void {
        if($level->value < self::$minLogLevel->value) {
            // Skip logging if the log level is below the minimum log level
            return;
        }

        // Create the formatted log message
        $originalMessage = $message;
        if(!is_string($message)) {
            $message = serialize($message);
        }
        $lineNumber = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]["line"] ?? 0; // First backtrace is the log function itself, second is the caller
        $formattedMessage = "[" . date("Y-m-d H:i:s") . "] [{$level->name}] [{$this->tag}:{$lineNumber}]: " . $message;

        // Write the message to the logfile
        self::openLogfile();
        fwrite(self::$logfile, $formattedMessage . PHP_EOL);

        // Call custom log handlers if there are any
        if(!empty(self::$customLogHandlers[$level->value])) {
            foreach(self::$customLogHandlers[$level->value] as $handler) {
                $handler($formattedMessage, $message, $originalMessage);
            }
        }
    }

    public function trace(mixed... $message) {
        foreach($message as $msg) {
            $this->log(LogLevel::TRACE, $msg);
        }
    }

    public function debug(mixed... $message) {
        foreach($message as $msg) {
            $this->log(LogLevel::DEBUG, $msg);
        }
    }

    public function info(mixed... $message) {
        foreach($message as $msg) {
            $this->log(LogLevel::INFO, $msg);
        }
    }

    public function warn(mixed... $message) {
        foreach($message as $msg) {
            $this->log(LogLevel::WARN, $msg);
        }
    }

    public function error(mixed... $message) {
        foreach($message as $msg) {
            $this->log(LogLevel::ERROR, $msg);
        }
    }

    public function fatal(mixed... $message) {
        foreach($message as $msg) {
            $this->log(LogLevel::FATAL, $msg);
        }
    }
}

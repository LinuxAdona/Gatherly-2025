<?php
/**
 * Logger Utility
 * Application logging system
 */

class Logger {
    
    private static $logFile;
    
    /**
     * Initialize logger
     */
    public static function init() {
        self::$logFile = LOG_PATH . '/' . date('Y-m-d') . '.log';
        
        // Create logs directory if not exists
        if (!is_dir(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }
    }
    
    /**
     * Write log entry
     */
    private static function write($level, $message, $context = []) {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? json_encode($context) : '';
        
        $logEntry = "[$timestamp] [$level] $message $contextString" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }
    
    /**
     * Log debug message
     */
    public static function debug($message, $context = []) {
        if (APP_DEBUG) {
            self::write('DEBUG', $message, $context);
        }
    }
    
    /**
     * Log info message
     */
    public static function info($message, $context = []) {
        self::write('INFO', $message, $context);
    }
    
    /**
     * Log warning message
     */
    public static function warning($message, $context = []) {
        self::write('WARNING', $message, $context);
    }
    
    /**
     * Log error message
     */
    public static function error($message, $context = []) {
        self::write('ERROR', $message, $context);
    }
    
    /**
     * Log critical message
     */
    public static function critical($message, $context = []) {
        self::write('CRITICAL', $message, $context);
    }
}

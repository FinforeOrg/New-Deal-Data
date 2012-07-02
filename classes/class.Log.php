<?php

/**
 * Log class
 *
 * $Id:$
 *
 * $Rev:  $
 *
 * $LastChangedBy:  $
 *
 * $LastChangedDate: $
 *
 * @author Ionut MIHAI <ionut_mihai25@yahoo.com>
 * @copyright 2011 Ionut MIHAI
 */
class Log {

    const WARN = 3;
    const INFO = 2;
    const DEBUG = 1;
    const LOG_LINE_PATTERN = '%s|%s|%s|%s|%s|%s';
        
    private static $_logLevels = array(
        'WARNING' => self::WARN,
        'INFO' => self::INFO,
        'DEBUG' => self::DEBUG
    );   
    
    public static function warning($msg, $file, $line)
    {
        self::_write($msg, $file, $line, self::WARN);
    }
    
    public static function info($msg, $file, $line)
    {
        self::_write($msg, $file, $line, self::INFO);
    }
    
    public static function debug($msg, $file, $line)
    {
        self::_write($msg, $file, $line, self::DEBUG);
    }
    
    public static function profiler($msg, $file, $line)
    {
        self::_displayQuery($msg);
    }
    
    private static function _displayQuery($query) {
        $query = str_replace(array('\n', '\n\r', '\r\n', PHP_EOL), array('','','', ''), $query);
        $query = preg_replace('/\s{3,}/', ' ', $query);
        $query = str_replace(array(
            'FROM', 'WHERE', 'LEFT JOIN', 'JOIN', 'INNER', 'OR', 'AND', 'GROUP BY', 'ORDER BY'
        ), array(
            PHP_EOL . 'FROM', PHP_EOL . 'WHERE', PHP_EOL . 'LEFT JOIN', PHP_EOL . 'JOIN', PHP_EOL . 'INNER', PHP_EOL . "\tOR", PHP_EOL . "\tAND", PHP_EOL .'GROUP BY', PHP_EOL . 'ORDER BY'
        ), $query);
        
        self::_write(str_repeat('-', 40), 'PROFILER', 'PROFILER', self::DEBUG);
        $lines = explode(PHP_EOL, $query);
        foreach ($lines as $logLine) {
            self::_write($logLine, 'PROFILER', 'PROFILER', self::DEBUG);
        }
    }
    
    public static function table($data) 
    {
        if (!is_array($data) || !count($data)) {
            return false;
        }
        
        return true;
    }
    
    public static function enable()
    {
        global $logFile, $logLevel;
        self::checkLogConfiguration();

    }
    
    public static function checkLogConfiguration()
    {
        global $logFile, $logLevel;
        
        if (!in_array($logLevel, array('WARNING', 'INFO', 'DEBUG'))) {
            throw new Exception('Invalid log level provided: ' . $logLevel);
        }  
    }
    
    private static function _write($msg, $file, $line, $level, $formatted = false)
    {
        global $logLevel, $logFile;
        
        
        if ($level < self::$_logLevels[$logLevel]) {
            return true;
        }
        
        $lineToPrint = '';
        
        if (is_array($msg) || is_object($msg)) {
            //$output = is_array($obj) ? 'Array: ' : 'Object: ';
            $lineToPrint =  self::_displayObj($msg, $file, $line, $level);
        } else {
            if (!$formatted)
                $lineToPrint = self::_formatLine($msg, $file, $line, $level); 
            else {
                $lineToPrint = $msg;
            }
        }
        
        file_put_contents($logFile, $lineToPrint, FILE_APPEND);
    }
    
    private static function _displayObj($obj, $file, $line, $level)
    {
        $c = var_export($obj, true);
        $lines = explode("\n", $c);
        $finalOutput = '';
        
        foreach ($lines as $logLine) {
            $finalOutput .= self::_formatLine($logLine, $file, $line, $level);
        }

        return $finalOutput;        
    }
    
    private static function _formatLine($msg, $file, $line, $level)
    {
        $logLevels = array_flip(self::$_logLevels);
        
        $memUsage = str_pad( (number_format(memory_get_usage() / 1024)) .' Kb', 10, ' ', STR_PAD_RIGHT);
        $file = str_pad(basename($file), 15, ' ', STR_PAD_RIGHT);
        $line = str_pad($line, 6, ' ', STR_PAD_RIGHT);
        
        return sprintf(self::LOG_LINE_PATTERN, date('Y-m-d H:i:s'), $memUsage, $file, $line, $logLevels[$level], $msg ) . PHP_EOL;
    }
    
}


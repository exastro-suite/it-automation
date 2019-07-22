<?php
//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
//////////////////////////////////////////////////////////////////////
//
//  【概要】
//      AnsibleTower ログ出力 クラス
//
//////////////////////////////////////////////////////////////////////

class LogWriter {

    const TRACE_WALL = 600;
    const DEBUG_WALL = 500;
    const INFO_WALL  = 400;
    const WARN_WALL  = 300;
    const ERROR_WALL = 200;
    const FATAL_WALL = 100;

    private $outputForce;

    private $log_output_php;
    private $log_output_dir;
    private $log_file_prefix;
   
    private $UIExecLogPath;
    private $UIErrorLogPath;

    // singleton
    private static $INSTANCE = null;

    private function __construct() {
    }

    static function getInstance() {
        if(! isset(static::$INSTANCE)) {
            static::$INSTANCE = new LogWriter();
        }

        return static::$INSTANCE;
    }

    function setUp($log_output_php, $log_output_dir, $log_file_prefix, $log_level, $UIExecLogPath='', $UIErrorLogPath='') {
        $this->log_output_php  = $log_output_php;
        $this->log_output_dir  = $log_output_dir;
        $this->log_file_prefix = $log_file_prefix;

        $this->$UIExecLogPath  = $UIExecLogPath;
        $this->$UIErrorLogPath = $UIErrorLogPath;

        $this->setOutputForce($log_level);
    }

    function setOutputForce($log_level) {
        switch($log_level) {
            case "TRACE":
                $this->outputForce = self::TRACE_WALL;
                break;
            case "DEBUG":
                $this->outputForce = self::DEBUG_WALL;
                break;
            case "WARN":
                $this->outputForce = self::WARN_WALL;
                break;
            case "INFO":
            case "NORMAL":
                $this->outputForce = self::INFO_WALL;
                break;
            case "ERROR":
                $this->outputForce = self::ERROR_WALL;
                break;
            case "FATAL":
                $this->outputForce = self::FATAL_WALL;
                break;
            default:
                // log_level設定されていないときはExceptionにせずINFO(NORMAL)で出力する
                $this->outputForce = self::INFO_WALL;
        }
    }

    function log($level, $message) {
        $log_output_dir  = $this->log_output_dir;
        $log_file_prefix = $this->log_file_prefix;

        if(is_array($message)) {
            foreach($message as $parts) {
                $FREE_LOG = $level . $parts;
                require($this->log_output_php);
            }
        } else{
            $FREE_LOG = $level . $message;
            require($this->log_output_php);
        }
    }

    function trace($message) {
        if(self::TRACE_WALL <= $this->outputForce) {
            $this->log("=TRACE= ", $message);
        }
    }

    function debug($message) {
        if(self::DEBUG_WALL <= $this->outputForce) {
            $this->log("/DEBUG/ ", $message);
        }
    }

    function info($message) {
        if(self::INFO_WALL <= $this->outputForce) {
            $this->log("[INFO ] ", $message);
        }
    }

    function warn($message) {
        if(self::WARN_WALL <= $this->outputForce) {
            $this->log("{WARN } ", $message);
        }
    }

    function error($message) {
        if(self::ERROR_WALL <= $this->outputForce) {
            $this->log(">ERROR< ", $message);
        }
    }

    function fatal($message) {
        if(self::FATAL_WALL <= $this->outputForce) {
            $this->log("*FATAL* ", $message);
        }
    }

    function debug_string_backtrace() { 
        ob_start(); 
        debug_print_backtrace(); 
        $trace = ob_get_contents(); 
        ob_end_clean(); 

        // Remove first item from backtrace as it's this function which 
        // is redundant. 
        $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1); 

        // Renumber backtrace items. 
        $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace); 

        return $trace; 
    }

    function UIExecLog($message) {
        $filepointer=fopen($this->$UIExecLogPath, "a");
        flock($filepointer, LOCK_EX);
        fputs($filepointer, $message. "\n" );
        flock($filepointer, LOCK_UN);
        fclose($filepointer);
    }

    function UIErrorLog($message) {
        $filepointer=fopen($this->$UIErrorLogPath, "a");
        flock($filepointer, LOCK_EX);
        fputs($filepointer, $message. "\n" );
        flock($filepointer, LOCK_UN);
        fclose($filepointer);
    }

    function __destruct() {
    }
}

?>

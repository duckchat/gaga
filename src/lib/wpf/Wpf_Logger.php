<?php

/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 13/07/2018
 * Time: 11:48 AM
 */
class Wpf_Logger
{
    private $_level = [
        "info",
        "warn",
        "error",
        "sql"
    ];

    private $errorLevel = "error";
    private $warnLevel  = "warn";
    private $infoLevel  = "info";
    private $sqlLevel   = "sql";

    private $logType = "";

    private $debugMode;

    public function __construct()
    {
        // 禁止在运行目录写Log
        // $this->fileName = $this->fileName . "-" . date("Ymd") . ".log";
        // $this->filePath = ZalyConfig::getConfig("logPath");
        // if($this->filePath == ".") {
        //     $this->filePath = dirname(__DIR__)."/../logs/";
        // }
        // $this->filePath = $this->filePath . "/" . $this->fileName;
        // $this->handler = fopen($this->filePath, "a+");

        // require ChromePhp
        // $path = WPF_LIB_DIR . "/ChromePhp/ChromePhp.php";
        // require_once($path);

        $this->debugMode = ZalyConfig::getConfig("debugMode");
    }

    /**
     * info log
     * @param $tag
     * @param $infoMsg
     */
    public function info($tag, $infoMsg)
    {
        $this->logType = $this->infoLevel;
        $this->writeLog($tag, $infoMsg);
    }

    /**
     * warn log
     * @param $tag
     * @param $infoMsg
     */
    public function warn($tag, $infoMsg)
    {
        $this->logType = $this->warnLevel;
        $this->writeLog($tag, $infoMsg);
    }

    /**
     * error log
     * @param $tag
     * @param $infoMsg
     */
    public function error($tag, $infoMsg)
    {
        $this->logType = $this->errorLevel;
        $this->writeLog($tag, $infoMsg);
    }

    /**
     * write log
     * @param $tag
     * @param $msg
     */
    private function writeLog($tag, $msg)
    {

        if (!in_array($this->logType, $this->_level)) {
            return;
        }

        if (is_array($msg)) {
            $msg = json_encode($msg);
        }

        // $requestUri = $_SERVER["REQUEST_URI"];
        $content = "$tag $msg \n";

        $errorLevel = ($this->logType == $this->errorLevel) ? E_USER_WARNING : E_USER_NOTICE;
        if($this->debugMode == true) {
            // switch ($errorLevel) {
            //     case E_USER_WARNING:
            //         ChromePhp::warn($content);
            //         break;
                
            //     default:
            //         ChromePhp::log($content);
            //         break;
            // }
            
            trigger_error($content, $errorLevel);
        } elseif ($this->logType == $this->errorLevel) {
            trigger_error($content, $errorLevel);
        }
    }

    public function writeSqlLog($tag, $sql, $params = [], $startTime = 0)
    {
        if (is_array($params)) {
            $params = json_encode($params);
        }
        $this->logType = $this->sqlLevel;
        $expendTime = microtime(true) - $startTime;
        $expendTime = round($expendTime, 3) . "ms";

        $content = "{$expendTime} $sql  params=$params";
        $this->writeLog($tag, $content);
    }

    public function dbLog($tag, $sql, $params = [], $startTime = 0, $result)
    {
        if (is_array($params)) {
            $params = json_encode($params);
        }

        if (is_array($result)) {
            $result = json_encode($result);
        }
        $this->logType = $this->sqlLevel;
        $expendTime = microtime(true) - $startTime;
        $expendTime = round($expendTime, 3) . "ms";

        $content = "{$expendTime} $sql  params=$params";
        
        $this->writeLog($tag, $content);
    }
}

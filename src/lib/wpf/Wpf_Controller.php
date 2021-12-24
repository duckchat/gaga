<?php

use \Zaly\Proto\Core\TransportDataHeaderKey;

abstract class Wpf_Controller
{

    protected $ctx;
    public $zalyError;
    private $configName = "config.php";
    private $sampleConfigName = "config.sample.php";
    private $cookieTimeOut = 2592000;//30天 单位s
    public function __construct(BaseCtx $context)
    {
        $this->ctx = $context;
    }

    public function doIndex()
    {
    }


    public function response($data = array(), $errorCode = 0, $errorInfo = "")
    {
        $output = array(
            'errorCode' => $errorCode,
            'errorInfo' => $errorInfo,
            'data' => $data
        );
        echo json_encode($output);
    }

    public function display($viewName, $params = [])
    {
        // 自己实现实现一下这个方法，加载view目录下的文件
        ob_start();
        $fileName = str_replace("_", "/", $viewName);
        $path = dirname(dirname(__DIR__)) . '/views/' . $fileName . '.php';
        if ($params) {
            extract($params, EXTR_SKIP);
        }
        include($path);
        $var = ob_get_clean();
        return $var;
    }

    final public function parseUrlParamByPattern($urlParamPattern)
    {
        $m = $this->ctx->Wpf_Router->parseUrlParamByPattern($urlParamPattern);
        $_REQUEST = array_merge($_REQUEST, $m);
    }

    public function getParam($key)
    {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
    }


    public function checkDBIsExist()
    {
        $dbType = ZalyConfig::getConfig("dbType");

        if (empty($dbType)) {
            $dbType = "sqlite";
        }
        switch ($dbType) {
            case "sqlite":
                $sqliteInfo = ZalyConfig::getConfig("sqlite");
                $sqliteName = $sqliteInfo['sqliteDBName'];
                if (empty($sqliteName)) {
                    return false;
                }
                $sqliteName = dirname(__FILE__) . '/../../' . $sqliteName;
                if (file_exists($sqliteName)) {
                    return true;
                }

                break;
            case "mysql":
                return true;

        }

        return false;
    }

    public function setCookie($val, $cookieName)
    {
        setcookie($cookieName, $val, time() + $this->cookieTimeOut, "/", "", false, true);
    }
}
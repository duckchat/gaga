<?php

define("DEVELOPER_MODE", false);
if (DEVELOPER_MODE) {
    tideways_xhprof_enable();
}

$timeRequestStart = microtime(true);

//$_SERVER['REQUEST_URI'] = "/User/100369-api.html
ini_set("display_errors", "Off");
ini_set("log_errors", "Off");

$_ENV['WPF_URL_PATH_SUFFIX'] = '/wpf';

// mock
require_once (__DIR__ . "/lib/mock.php");

// adapt to duckchat-gagaphp.
if (!empty($_GET['action'])) {
    $action = isset($_GET['action']) ? $_GET['action'] : "";
    $action =  ucwords($action, '.');
    $controllerName  = str_replace(".", "_", $action);

    $_ENV['WPF_URL_CONTROLLER_NAME'] = $controllerName;
    $_ENV['WPF_URL_CONTROLLER_METHOD_PARAM_NAME'] = "doIndex";
}


if(!isset($_ENV['WPF_URL_CONTROLLER_NAME'])) {
    $_ENV['WPF_URL_CONTROLLER_NAME'] = "Page_Index";
    $_ENV['WPF_URL_CONTROLLER_METHOD_PARAM_NAME'] = isset($_GET['method'] ) ? $_GET['method']  : "doIndex" ;
}

require_once(__DIR__ . "/lib/wpf/init.php");

if (DEVELOPER_MODE) {
    $config = require_once (__DIR__ . "/config.developer.php");
    $logDir = $config["xhprofDir"];
    $timeRequestEnd = microtime(true);
    $timeCost = intval(($timeRequestEnd - $timeRequestStart) * 1000);
    if ($timeCost > 100) {
        $data = tideways_xhprof_disable();
        file_put_contents(
            "{$logDir}/{$timeCost}ms" . uniqid() . ".file.xhprof",
            serialize($data)
        );
    } else {
        tideways_xhprof_disable();
    }
}

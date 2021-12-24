<?php
//$_SERVER['REQUEST_URI'] = "/User/100369-api.html
ini_set("display_errors", "Off");
ini_set("log_errors", "On");

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
    if($_SERVER["REQUEST_URI"] == "/favicon.ico") {
        header("HTTP/1.0 404 Not Found");
        die();
    }
    $_ENV['WPF_URL_CONTROLLER_NAME'] = "Page_Index";
    $_ENV['WPF_URL_CONTROLLER_METHOD_PARAM_NAME'] = isset($_GET['method'] ) ? $_GET['method']  : "doIndex" ;
}

require_once(__DIR__ . "/lib/wpf/init.php");
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 07/11/2018
 * Time: 11:52 AM
 */

class Manage_Security_LogController extends Manage_CommonController
{
    private $defaultPageSize = 200;
    public function doRequest()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if($method == "post") {
            $page = isset($_POST['page']) ? $_POST['page'] : "1";
            $offset = ($page -1 )*$this->defaultPageSize;
            echo $this->getResults($offset);
        }else {
           $for = isset($_GET['for']) ? $_GET['for'] : "page";
           switch ($for) {
               case "page":
                   $page =1;
                   $offset = ($page -1 )*$this->defaultPageSize;
                   echo $this->getLogPage($offset);
                   break;
               case "truncate":
                   echo $this->truncateLogs();
                   break;
           }
        }
        return;
    }

    private function getLogPage($offset)
    {
        $results = $this->ctx->PassportPasswordLogTable->getLists($offset, $this->defaultPageSize);
        $count = $this->ctx->PassportPasswordLogTable->getAllCount();
        $params = [
            'count' => $count,
            'logs'  => $results,
            "lang"  => $this->language
        ];
        echo $this->display("manage_security_log", $params);
        return;
    }

    private function getResults($offset)
    {
        $results = $this->ctx->PassportPasswordLogTable->getLists($offset, $this->defaultPageSize);
        return json_encode(["data" => $results]);
    }

    private function truncateLogs()
    {
        $results = $this->ctx->PassportPasswordLogTable->deleteLogData();
        return json_encode(["errCode" => $results]);
    }
}
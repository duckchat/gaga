<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 06/11/2018
 * Time: 10:27 AM
 */

class Manage_AdvancedController  extends Manage_CommonController
{

    public function doRequest()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : "index";
        $params = ["lang" => $this->language];
        switch ($page) {
            case "index":
                $this->toPageIndex($params);
                break;
            case "phpinfo":
                $this->toPagePHPInfo();
                break;
            default:
                $this->toPageIndex($params);
        }

        return;
    }

    /**
     * @param array $params
     */
    private function toPageIndex($params)
    {
        echo $this->display("manage_advanced_index", $params);
    }

    private function toPagePHPInfo()
    {
         phpinfo(INFO_VARIABLES);
         die();
    }
}
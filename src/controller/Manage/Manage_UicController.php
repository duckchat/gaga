<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 3:48 PM
 */

class Manage_UicController extends Manage_CommonController
{

    public function doRequest()
    {
        $page = $_GET['page'];
        $params = ["lang" => $this->language];

        switch ($page) {
            case "index":
                $this->toPageIndex($params);
                break;
            case "used":
                $this->toPageUsed($params);
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
        $unusedList = $this->getUnuseUicList(1, 50);
        $params['unusedList'] = $unusedList;
        echo $this->display("manage_uic_index", $params);
    }

    private function toPageUsed($params)
    {
        $usedList = $this->getUsedUicList(1, 50);

        $params["usedList"] = $usedList;

        echo $this->display("manage_uic_usedList", $params);
    }

    private function getUnuseUicList($pageNum, $pageSize)
    {
        return $this->ctx->SiteUicTable->queryUnusedUic($pageNum, $pageSize);
    }

    private function getUsedUicList($pageNum, $pageSize)
    {
        return $this->ctx->SiteUicTable->queryUsedUic($pageNum, $pageSize);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 3:48 PM
 */

class Manage_Uic_UsedController extends Manage_CommonController
{

    public function doRequest()
    {
        $pageNum = $_POST['pageNum'];
        $pageSize = $_POST['pageSize'];

        $usedList = $this->getUsedUicList($pageNum, $pageSize);

        $this->ctx->Wpf_Logger->info("manage.uic.used",
            "pageNum=" . $pageNum . " pageSize=" . $pageNum . " list=" . count($usedList));

        $response = [
            "errCode" => "success",
            "errInfo" => "",
            "usedList" => $usedList
        ];

        echo json_encode($response);
        return;
    }


    private function getUsedUicList($pageNum, $pageSize)
    {
        return $this->ctx->SiteUicTable->queryUsedUic($pageNum, $pageSize);
    }

}
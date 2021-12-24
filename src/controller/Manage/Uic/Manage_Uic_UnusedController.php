<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 3:48 PM
 */

class Manage_Uic_UnusedController extends Manage_CommonController
{

    public function doRequest()
    {
        $pageNum = $_POST['pageNum'];
        $pageSize = $_POST['pageSize'];

        $unusedList = $this->getUnuseUicList($pageNum, $pageSize);

        $this->ctx->Wpf_Logger->info("manage.uic.used",
            "pageNum=" . $pageNum . " pageSize=" . $pageNum . " list=" . count($unusedList));

        $response = [
            "errCode" => "success",
            "errInfo" => "",
            "unusedList" => $unusedList
        ];

        echo json_encode($response);
        return;
    }


    private function getUnuseUicList($pageNum, $pageSize)
    {
        return $this->ctx->SiteUicTable->queryUnusedUic($pageNum, $pageSize);
    }

}
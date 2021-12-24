<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 28/08/2018
 * Time: 6:40 PM
 */

class Manage_MiniProgram_ListController extends Manage_CommonController
{
    protected function doRequest()
    {
        $params = [];
        $params['lang'] = $this->language;

        $type = $_GET['type'];

        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST') {
            echo "no data";

        } else {
            $miniProgramList = $this->getMiniProgramList(0, 100);

            $params["miniProgramList"] = $miniProgramList;

            $this->ctx->Wpf_Logger->info("manage.miniprogram.list", "list=" . json_encode($params));

            echo $this->display("manage_miniProgram_list", $params);
        }

        return;
    }


    private function getMiniProgramList($offset, $count)
    {
        $list = $this->ctx->SitePluginTable->getNonRepeatedPluginList();

        return $list;
    }

}
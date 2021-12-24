<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 11:05 AM
 */

class Manage_IndexController extends Manage_CommonController
{

    public function doRequest()
    {
        $page = $_GET['page'];
        $params = ["lang" => $this->language];

        if ("home" == $page) {


            $pluginAdmins = $this->getMiniProgramList();

            $params['miniProgramList'] = $pluginAdmins;

            echo $this->display("manage_home", $params);
        } else {
            echo $this->display("manage_index", $params);
        }
        return;
    }

    private function getMiniProgramList()
    {
        $hasAdminMiniPrograms = [];

        $miniProgramList = $this->ctx->SitePluginTable->getNonRepeatedPluginList();

        if ($miniProgramList) {
            foreach ($miniProgramList as $miniProgram) {

                $adminPageUrl = $miniProgram['adminPageUrl'];

                if (!empty($adminPageUrl)) {
                    $miniProgram["name"] .= "后台管理";
                    $hasAdminMiniPrograms[] = $miniProgram;
                }

            }
        }

        return $hasAdminMiniPrograms;
    }
}
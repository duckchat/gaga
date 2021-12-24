<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 28/08/2018
 * Time: 6:40 PM
 */

class Manage_MiniProgram_ProfileController extends Manage_CommonController
{

    protected function doRequest()
    {
        $pluginId = $_GET['pluginId'];

        $miniProgramProfileList = $this->getPluginProfileList($pluginId);

        $miniProgramProfile = $miniProgramProfileList[0];
        $miniProgramProfile['lang'] = $this->language;

        foreach ($miniProgramProfileList as $pluginProfile) {

            $usageType = $pluginProfile["usageType"];

            $miniProgramProfile["usageType_" . $usageType] = $usageType;
        }

        echo $this->display("manage_miniProgram_profile", $miniProgramProfile);
        return;
    }


    private function getPluginProfileList($pluginId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SitePluginTable->getPluginsById($pluginId);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
        return [];
    }

}
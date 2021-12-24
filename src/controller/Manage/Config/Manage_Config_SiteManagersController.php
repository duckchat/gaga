<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:58 AM
 */

class Manage_Config_SiteManagersController extends Manage_CommonController
{
    /**
     * 站点管理
     */
    public function doRequest()
    {
        $response = [];
        try {
            $params['lang'] = $this->language;

            $siteManagers = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_MANAGERS);

            $managerList = explode(",", $siteManagers);

            $params['userList'] = $this->getSiteManagerList($managerList);

            echo $this->display("manage_config_siteManagers", $params);
            return;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error("manage.config.update", $e);
            $response["errCode"] = false;
            $response["errInfo"] = $e->getMessage();
        }

        echo json_encode($response);
        return;
    }


    private function getSiteManagerList(array $managersList)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $managers = $this->ctx->SiteUserTable->getUserByUserIds($managersList);
            $this->ctx->Wpf_Logger->info("manage.config.managers", "managers=" . json_encode($managers));
            return $managers;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
        return false;
    }

}
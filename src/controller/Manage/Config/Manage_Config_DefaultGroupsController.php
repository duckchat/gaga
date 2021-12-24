<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:58 AM
 */

class Manage_Config_DefaultGroupsController extends Manage_CommonController
{
    /**
     * 站点管理
     */
    public function doRequest()
    {
        $response = [];
        try {
            $params['lang'] = $this->language;

            $defaultGroupStr = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_DEFAULT_GROUPS);

            $groupList = explode(",", $defaultGroupStr);

            $params['groupList'] = $this->getDefaultGroupList($groupList);

            $this->ctx->Wpf_Logger->info("manage.config.defaultGroups", "params=" . json_encode($params));

            echo $this->display("manage_config_defaultGroups", $params);
            return;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error("manage.config.update", $e);
            $response["errCode"] = false;
            $response["errInfo"] = $e->getMessage();
        }

        echo json_encode($response);
        return;
    }


    private function getDefaultGroupList(array $groupList)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $result = $this->ctx->SiteGroupTable->getGroupListByGroupIds($groupList);
            $this->ctx->Wpf_Logger->info("manage.config.defaultGroups", "groups=" . json_encode($result));
            return $result;

        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
        return false;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:58 AM
 */

class Manage_Config_DefaultFriendsController extends Manage_CommonController
{
    /**
     * 站点管理
     */
    public function doRequest()
    {
        $response = [];
        try {
            $params['lang'] = $this->language;

            $defaultFriendString = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_DEFAULT_FRIENDS);

            $defaultFriendList = explode(",", $defaultFriendString);

            $params['userList'] = $this->getDefaultFriendProfileList($defaultFriendList);

            $this->ctx->Wpf_Logger->info("------------", $params);
            $this->ctx->Wpf_Logger->info("------------", "json=" . json_encode($params));

            echo $this->display("manage_config_defaultFriends", $params);
            return;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error("manage.config.update", $e);
            $response["errCode"] = false;
            $response["errInfo"] = $e->getMessage();
        }

        echo json_encode($response);
        return;
    }


    private function getDefaultFriendProfileList(array $managersList)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $managers = $this->ctx->SiteUserTable->getUserByUserIds($managersList);
            $this->ctx->Wpf_Logger->info("manage.config.update", "managerss=" . json_encode($managers));
            return $managers;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
        return false;
    }

}
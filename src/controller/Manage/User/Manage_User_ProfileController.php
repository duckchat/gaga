<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_User_ProfileController extends Manage_CommonController
{

    public function doRequest()
    {
        $userId = $_GET['userId'];

        $params = $this->getUserProfile($userId);
        $params['lang'] = $this->language;

        $params['isSiteOwner'] = $this->ctx->Site_Config->isSiteOwner($userId);
        $params['isSiteManager'] = $this->ctx->Site_Config->isManager($userId);

        $defaultFriendStr = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_DEFAULT_FRIENDS);
        if ($defaultFriendStr) {
            $isDefaultFriend = in_array($userId, explode(",", $defaultFriendStr));
            $params['isDefaultFriend'] = $isDefaultFriend;
        }

        echo $this->display("manage_user_profile", $params);
        return;
    }

    private function getUserProfile($userId)
    {
        return $this->ctx->SiteUserTable->getUserByUserId($userId);
    }

}
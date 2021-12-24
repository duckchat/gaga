<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_Group_ProfileController extends Manage_CommonController
{

    public function doRequest()
    {
        $groupId = $_GET['groupId'];

        $params = $this->getGroupProfile($groupId);
        $params['lang'] = $this->language;

        $config = $this->ctx->Site_Config->getAllConfig();

        $defaultGroupsStr = $config[SiteConfig::SITE_DEFAULT_GROUPS];
        $maxGroupMembers = $config[SiteConfig::SITE_MAX_GROUP_MEMBERS];

        if ($defaultGroupsStr) {
            $defaultGroupsList = explode(",", $defaultGroupsStr);

            if (in_array($groupId, $defaultGroupsList)) {
                $params['isDefaultGroup'] = 1;
            }
        }

        $maxMembers = $params["maxMembers"];
        if (empty($maxMembers)) {
            $maxMembers = $maxGroupMembers;
        } else {
            $maxMembers = min($maxMembers, $maxGroupMembers);
        }
        $params["maxMembers"] = $maxMembers;

        echo $this->display("manage_group_profile", $params);
        return;
    }

    private function getGroupProfile($groupId)
    {
        return $this->ctx->SiteGroupTable->getGroupInfo($groupId);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_User_GroupsController extends Manage_CommonController
{

    public function doRequest()
    {
        $userId = $_GET['userId'];

        $params = [];
        $params['lang'] = $this->language;
        $params['groupList'] = $this->getUserGroups($userId);

        $this->ctx->Wpf_Logger->info("manage.user.groups", "user=" . $userId . " groups=" . json_encode($params));

        echo $this->display("manage_user_groups", $params);
        return;
    }

    private function getUserGroups($userId)
    {
        return $this->ctx->SiteGroupTable->getGroupList($userId, 0, 200);
    }

}
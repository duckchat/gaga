<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_Group_MembersController extends Manage_CommonController
{

    public function doRequest()
    {
        $groupId = $_GET['groupId'];

        $params = [];
        $params['lang'] = $this->language;

        $groupProfile = $this->getGroupProfile($groupId);

        //get group master
        $masterUserId = $groupProfile['owner'];
        $groupMaster = $this->getUserProfile($masterUserId);
        $params['groupMaster'] = $groupMaster;

        //get group managers
        $groupManagers = $this->getGroupManagers($groupId);
        $params['groupManagers'] = $groupManagers;

        //get group members
        $groupMembers = $this->getGroupMembers($groupId, 0, 50);
        $params['groupMembers'] = $groupMembers;

        $this->ctx->Wpf_Logger->info("------------------", json_encode($params));

        echo $this->display("manage_group_members", $params);
        return;
    }

    private function getGroupProfile($groupId)
    {
        return $this->ctx->SiteGroupTable->getGroupInfo($groupId);
    }

    private function getUserProfile($userId)
    {
        return $this->ctx->SiteUserTable->getUserByUserId($userId);
    }


    /**
     * get group manager/admin
     * @param $groupId
     * @return mixed
     * @throws Exception
     */
    private function getGroupManagers($groupId)
    {
        $memberType = Zaly\Proto\Core\GroupMemberType::GroupMemberAdmin;
        return $this->ctx->SiteGroupUserTable->getGroupUserList($groupId, 0, 50, $memberType);

    }

    private function getGroupMembers($groupId, $offset, $pageSize)
    {
        $memberType = Zaly\Proto\Core\GroupMemberType::GroupMemberNormal;
        return $this->ctx->SiteGroupUserTable->getGroupUserList($groupId, $offset, $pageSize, $memberType);
    }

}
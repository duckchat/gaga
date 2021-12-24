<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 2018/10/25
 * Time: 6:37 PM
 */

class MiniProgram_Test_ToolsController extends MiniProgram_BaseController
{

    private $testMiniProgramId = 106;


    public function getMiniProgramId()
    {
        return $this->testMiniProgramId;
    }

    public function requestException($ex)
    {
        $this->showPermissionPage();
    }

    public function preRequest()
    {
        return true;
    }

    public function doRequest()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $params = [
            "myFriendProfile" => $this->getMyFriendProfile(),
            "notMyFriendProfile" => $this->getNotMyFriendProfile(),
            "myGroupProfile" => $this->getMyGroupProfile(),
            "notMyGroupProfile" => $this->getNotMyGroupProfile(),
            "miniProgramId" => 100,
        ];

        echo $this->display("miniProgram_test_zalyjsTools", $params);

        return;

    }


    private function getMyFriendProfile()
    {

        $friendProfile = $this->ctx->SiteUserFriendTable->queryUserFriendByPage($this->userId, 0, 1);

        if ($friendProfile) {
            return $friendProfile[0];
        }

        return false;
    }

    private function getNotMyFriendProfile()
    {

        $notFriendProfile = false;

        for ($i = 0; $i < 100; $i += 20) {


            $userList = $this->ctx->SiteUserTable->getSiteUserListWithRelation($this->userId, $i, $i + 20);

            if ($userList) {

                foreach ($userList as $user) {
                    if ($this->userId == $user['userId']) {
                        continue;
                    }

                    $friendId = $user['friendId'];

                    if (empty($friendId)) {
                        $notFriendProfile = $user;
                        return $notFriendProfile;
                    }

                }

                if (!empty($notFriendProfile)) {
                    break;
                }

            } else {
                break;
            }

        }

        return $notFriendProfile;
    }

    private function getMyGroupProfile()
    {
        $myGroupList = $this->ctx->SiteGroupUserTable->getUserGroups($this->userId);

        if (!empty($myGroupList)) {
            return $myGroupList[0];
        }
        return false;
    }

    private function getNotMyGroupProfile()
    {
        $notMyGroupProfile = $this->ctx->SiteGroupUserTable->getNotUserGroup($this->userId, 0, 1);

        if ($notMyGroupProfile) {
            return $notMyGroupProfile[0];
        }
        return false;
    }

}
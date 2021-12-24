<?php
/**
 * Manual 抽象类，提供公共方法
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/13
 * Time: 11:23 AM
 */

abstract class Manual_Common
{
    protected $logger;
    protected $ctx;

    public function __construct(BaseCtx $ctx)
    {
        $this->logger = $ctx->getLogger();
        $this->ctx = $ctx;
    }

    protected function finish_request()
    {
        if (!function_exists("fastcgi_finish_request")) {
            function fastcgi_finish_request()
            {
            }
        }
        fastcgi_finish_request();
    }


    /*****************************************用户相关方法*******************************************/

    /**
     * @param $userId
     * @return null
     */
    protected function getUserName($userId)
    {
        $userInfo = $this->ctx->SiteUserTable->getUserByUserId($userId);

        if (!empty($userInfo)) {
            $userName = $userInfo['nickname'];

            if (empty($userName)) {
                $userName = $userInfo['loginName'];
            }

            return $userName;
        } else {
            return null;
        }
    }



    /*****************************************群组相关方法*******************************************/

    /**
     * 通过群ID获取群组资料
     * @param $groupId
     * @return bool|mixed
     * @throws Exception
     */
    protected function getGroupProfile($groupId, $lang)
    {
        $groupInfo = $this->ctx->SiteGroupTable->getGroupInfo($groupId);
        if (!$groupInfo) {
            $tag = __CLASS__ . '-' . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, " errorGroupExist group id = " . $groupId);
            $exText = ZalyText::getText("text.group.notExists", $lang);
            throw new Exception($exText);
        }
        return $groupInfo;
    }

    protected function getGroupUserList($groupId, $offset, $pageSize)
    {
        return $this->ctx->SiteGroupUserTable->getGroupUserList($groupId, $offset, $pageSize);
    }

    //是否是群管理员
    protected function isGroupAdmin($groupId, $userId)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;

        if (!$userId) {
            return false;
        }

        try {
            //管理员，或者群主
            $ownerType = \Zaly\Proto\Core\GroupMemberType::GroupMemberOwner;
            $adminType = \Zaly\Proto\Core\GroupMemberType::GroupMemberAdmin;

            $user = $this->ctx->SiteGroupUserTable->getGroupAdmin($groupId, $userId, $adminType, $ownerType);
            if ($user) {
                return true;
            }

        } catch (Exception $e) {
            $this->logger->error($tag . " " . $this->action, $e);
        }
        return false;
    }

    ////是否是群成员
    public function isGroupMember($groupId, $userId)
    {
        $groupMember = $this->ctx->SiteGroupUserTable->getGroupUser($groupId, $userId);
        if ($groupMember && !empty($groupMember['userId'])) {
            return true;
        }
        return false;
    }

    public function getGroupMemberCount($groupId)
    {
        return $this->ctx->SiteGroupUserTable->getGroupUserCount($groupId);
    }

    protected function updateGroupAvatar($groupId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {//query old 9 groupMember to make group avatar
            $this->ctx->Wpf_Logger->info("Group-Avatar", "update groupId=" . $groupId);

            $groupMemberAvatars = $this->getOldest9GroupMemberAvatars($groupId);
            $newGroupAvatar = $this->ctx->File_Manager->buildGroupAvatar($groupMemberAvatars);

            $this->ctx->Wpf_Logger->info("Group-Avatar", "update avatarFileId=" . $newGroupAvatar);
            if ($newGroupAvatar) {
                $data = [
                    'avatar' => $newGroupAvatar
                ];
                $where = [
                    'groupId' => $groupId
                ];
                $this->ctx->SiteGroupTable->updateGroupInfo($where, $data);
            }
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->info($tag, $e);
        }
    }

    /**
     * @param $groupId
     * @return array
     */
    protected function getOldest9GroupMemberAvatars($groupId)
    {
        $avatars = [];

        $offset = 0;
        $startCount = 9;
        $loopNum = 0;
        while (true) {
            $loopNum++;

            $hasCount = count($avatars);
            $pageCount = $startCount - $hasCount;

            $groupMembers = $this->getGroupUserList($groupId, $offset, $pageCount);

            $offset += $pageCount;

            $this->ctx->Wpf_Logger->info("Group-Avatar", "hasCount2=" . count($avatars));

            if (empty($groupMembers) || count($groupMembers) == 0) {
                break;
            }

            foreach ($groupMembers as $groupMember) {
                $userAvatar = $groupMember['avatar'];
                //判断头像存在
                $isExists = $this->ctx->File_Manager->fileIsExists($userAvatar);
                if ($isExists) {
                    $avatars[] = $userAvatar;
                }
            }

            if ($loopNum >= 10 || count($avatars) >= 9) {
                break;
            }
        }
        return $avatars;
    }


    /*****************************************消息代发相关方法*******************************************/

    /**
     * @param $currentUserId
     * @param $groupInfo
     * @param $userIds
     * @return bool
     */
    protected function proxyGroupDescriptionNotice($currentUserId, $groupInfo, $userIds)
    {
        $groupId = $groupInfo['groupId'];
        $desc = $groupInfo['description'];
        $descType = $groupInfo['descriptionType'];

        if (empty($desc) || Zaly\Proto\Core\GroupDescriptionType::GroupDescriptionMarkdown == $descType) {
            return false;
        }

        $desc = "[群介绍]" . $desc;

        foreach ($userIds as $memberId) {
            $this->ctx->Message_Client->proxyGroupAsU2NoticeMessage($memberId, $currentUserId, $groupId, $desc);
        }

    }

}
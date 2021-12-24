<?php
/**
 * Site default friends ,groups
 * User: anguoyue
 * Date: 07/09/2018
 * Time: 8:24 PM
 */

class Site_Default
{
    private $logger;
    private $ctx;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
        $this->logger = new Wpf_Logger();
    }

    public function addDefaultFriendsAndGroups($userId)
    {
        try {
            $config = $this->ctx->Site_Config->getSiteDefaultFriendsAndGroups();

            if (!empty($config)) {
                $this->addDefaultFriends($userId, $config[SiteConfig::SITE_DEFAULT_FRIENDS]);

                $this->addDefaultGroups($userId, $config[SiteConfig::SITE_DEFAULT_GROUPS]);
            }

        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error("add default friends and groups", $ex);
        }

    }

    private function addDefaultFriends($userId, $defaultFriendString)
    {

        if (empty($defaultFriendString) || empty($userId)) {
            return;
        }

        $defaultFriendArray = explode(",", $defaultFriendString);

        foreach ($defaultFriendArray as $defaultFriend) {
            try {
                $result = $this->saveFriend($userId, $defaultFriend);
                $this->ctx->SiteUserTable->updateNextFriendVersion($defaultFriend);

                if ($result) {
                    $this->proxyNewFriendMessage($userId, $defaultFriend);
                }
            } catch (Exception $e) {
                $this->logger->error("add.default.friends", $e);
            }
        }
        //统一更新 userId profile version
        $this->ctx->SiteUserTable->updateNextFriendVersion($userId);
    }

    private function addDefaultGroups($userId, $defaultGroups)
    {
        if (empty($defaultGroups) || empty($userId)) {
            return;
        }

        $defaultGroups = explode(",", $defaultGroups);

        foreach ($defaultGroups as $defaultGroup) {
            if ($this->addUserToGroup($userId, $defaultGroup)) {
                $this->proxyNewGroupMemberMessage($userId, $defaultGroup);
            }
        }

    }

    protected function saveFriend($userId, $defaultFriend)
    {
        $success = $this->ctx->SiteUserFriendTable->saveUserFriend($userId, $defaultFriend);
        $success = $this->ctx->SiteUserFriendTable->saveUserFriend($defaultFriend, $userId) && $success;
        return $success;
    }

    private function proxyNewFriendMessage($userId, $defaultUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        $text = ZalyText::$keyDefaultFriendsText;

        try {
            $this->ctx->Message_Client->proxyU2TextMessage($userId, $defaultUserId, $userId, $text, true);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
    }

    private function addUserToGroup($userId, $groupId)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        try {

            $groupPointer = $this->ctx->SiteGroupMessageTable->queryMaxIdByGroup($groupId);

            $this->ctx->SiteGroupMessageTable->updatePointer($groupId, $userId, "", $groupPointer);

            //insert into siteGroupUser
            $groupUserInfo = [
                'groupId' => $groupId,
                'userId' => $userId,
                'memberType' => \Zaly\Proto\Core\GroupMemberType::GroupMemberNormal,
                'timeJoin' => $this->ctx->ZalyHelper->getMsectime()
            ];

            return $this->ctx->SiteGroupUserTable->insertGroupUserInfo($groupUserInfo);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error("add.default.groups.error", $ex);
        }
        return false;
    }

    /**
     * @param $userId
     * @param $groupId
     */
    private function proxyNewGroupMemberMessage($userId, $groupId)
    {
        $noticeText = $this->buildUserNotice($userId);
        if (empty($noticeText)) {
            return;
        }

        $this->ctx->Message_Client->proxyGroupNoticeMessage($userId, $groupId, $noticeText);
    }

    private function buildUserNotice($userId)
    {
        if (empty($userId)) {
            return "";
        }

        $nameBody = ZalyText::$keyDefaultGroupsText;

        if (isset($userId)) {
            $nameBody = $this->getUserName($userId);
        }

        $nameBody .= ZalyText::$keyGroupJoin;
        return $nameBody;
    }

    /**
     * @param $userId
     * @return null
     */
    private function getUserName($userId)
    {
        $userInfo = $this->ctx->SiteUserTable->getUserByUserId($userId);

        if (!empty($userInfo)) {
            $userName = $userInfo['nickname'];

            if (empty($userName)) {
                $userName = $userInfo['loginName'];
            }

            return $userName;
        }

        return "new member";
    }
}
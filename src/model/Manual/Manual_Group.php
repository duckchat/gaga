<?php
/**
 * 内部接口开发手册，群组相关内部接口
 *  1.查找群组
 *  2.批量获取群组资料
 *  3.加群组
 * Author: SAM<an.guoyue254@gmai.com>
 * Date: 2018/11/13
 * Time: 11:00 AM
 */

interface Group
{
    /**
     * 更具$search查找群组
     * @param $search   查找的内容
     * @param int $pageNum 第几页，从1开始
     * @param int $pageSize 每页面数量
     * @return array
     */
    public function search($search, $pageNum = 1, $pageSize = 20);

    /**
     * @param $currentUserId
     * @param array $groupIds 批量获取的群组ID数组
     * @return array 返回群组资料的数组
     */
    public function getProfiles($currentUserId, array $groupIds);


    /**
     * 检测用户是否是群组成员
     * @param $groupId
     * @param $userId
     * @return mixed
     */
    public function isMember($groupId, $userId);

    /**
     * $userId 加入 $groupId，这里需要做群组的权限控制，群组允许加群才可以入群
     * @param $groupId 加入的群组
     * @param array $userId
     * @param $joinNotice
     * @param bool $inviteUserId
     * @param int $lang
     * @return bool
     */
    public function joinGroup($groupId, array $userId, $joinNotice, $inviteUserId = false, $lang = Zaly\Proto\Core\UserClientLangType::UserClientLangZH);

}


class Manual_Group extends Manual_Common implements Group
{

    /**
     * 更具$search查找群组
     * @param $search   查找的内容
     * @param int $pageNum 第几页，从1开始
     * @param int $pageSize 每页面数量
     * @return array
     */
    public function search($search, $pageNum = 1, $pageSize = 20)
    {
        $search = trim($search);
        $likeGroupList = $this->ctx->SearchSiteTable->getGroupProfileByName($search, $pageNum, $pageSize);
        return $likeGroupList;
    }

    /**
     * @param $currentUserId
     * @param array $groupIds 批量获取的群组ID数组
     * @return array 返回群组资料的数组
     */
    public function getProfiles($currentUserId, array $groupIds)
    {
        //可能需要$currentUserId，获取用户是否在群中
        $groupList = $this->ctx->SiteGroupTable->getGroupListByGroupIds($groupIds);

        $returnProfiles = [];
        if (!empty($groupList)) {
            foreach ($groupList as $group) {
                $groupId = $group["groupId"];
                if ($this->isGroupMember($groupId, $currentUserId)) {
                    $group['isMember'] = true;
                } else {
                    $group['isMember'] = false;
                }
                $returnProfiles[] = $group;
            }
        }

        return $returnProfiles;
    }

    /**
     * 检测用户是否是群组成员
     * @param $groupId
     * @param $userId
     * @return mixed
     */
    public function isMember($groupId, $userId)
    {
        return $this->isGroupMember($groupId, $userId);
    }

    /**
     * $userId 加入 $groupId，这里需要做群组的权限控制，群组允许加群才可以入群
     * @param $groupId 加入的群组
     * @param array $userIds 加入群组的用户
     * @param $joinNotice
     * @param bool $inviteUserId
     * @param int $lang
     * @return bool
     * @throws ZalyException
     */
    public function joinGroup($groupId, array $userIds, $joinNotice, $inviteUserId = false, $lang = Zaly\Proto\Core\UserClientLangType::UserClientLangZH)
    {
        if (empty($userIds)) {
            return false;
        }

        //获取群组资料信息
        $groupInfo = $this->getGroupProfile($groupId, $lang);
        if ($groupInfo === false) {
            return false;
        }

        $memberIds = [];
        foreach ($userIds as $newMemberId) {
            if (!$this->isGroupMember($groupId, $newMemberId)) {
                $memberIds[] = $newMemberId;
            }
        }

        if (empty($memberIds)) {
            throw new ZalyException(ZalyError::ErrorGroupIsMember);
        }

        switch ($groupInfo['permissionJoin']) {
            case  \Zaly\Proto\Core\GroupJoinPermissionType::GroupJoinPermissionAdmin:
                if (!$this->isGroupAdmin($groupId, $inviteUserId)) {
                    throw new ZalyException(ZalyError::ErrorGroupAdmin);
                }

                break;
            case \Zaly\Proto\Core\GroupJoinPermissionType::GroupJoinPermissionMember:
                if (!$this->isGroupMember($groupId, $inviteUserId)) {
                    throw new ZalyException(ZalyError::ErrorGroupMember);
                }
                break;
            case \Zaly\Proto\Core\GroupJoinPermissionType::GroupJoinPermissionPublic:
                break;
            default:
                break;
        }

        //current group count
        $groupUserCount = $this->getGroupMemberCount($groupId);
        $siteMaxGroupMembers = $groupInfo['maxMembers'];

        //default -1 < 0
        if ($siteMaxGroupMembers < 0) {
            $siteMaxGroupMembers = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_MAX_GROUP_MEMBERS);
        }

        $newGroupUserCount = $groupUserCount + count($memberIds);
        if ($siteMaxGroupMembers <= $groupUserCount || $siteMaxGroupMembers < $newGroupUserCount) {
            $errorInfo = $lang == 1 ? "因群组最大成员限制（{$siteMaxGroupMembers}），此操作未成功"
                : "failed due to the max members limit of the group ({$siteMaxGroupMembers})";
            throw new ZalyException(ZalyError::ErrorGroupMemberCount, $errorInfo);
        }

        /// 公开的直接进入
        $this->addMemberToGroup($memberIds, $groupId);

        $this->finish_request();

        //更新群头像
        if ($groupUserCount < 9) {
            $this->updateGroupAvatar($groupId);
        }

        //代码入群消息
        $this->ctx->Message_Client->proxyGroupNoticeMessage("", $groupId, $joinNotice);

        //代发群组公告notice
        //proxy send group-description notice to group
        $this->proxyGroupDescriptionNotice($inviteUserId, $groupInfo, $userIds);

        return true;
    }

    private function addMemberToGroup($userIds, $groupId)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        try {

            $existsUserId = $this->ctx->SiteGroupUserTable->getUserIdExistInGroup($userIds, $groupId);
            $notExistsUserId = $userIds;
            if ($existsUserId) {
                $notExistsUserId = array_diff($userIds, $existsUserId);
            }
            if (!count($notExistsUserId)) {
                return true;
            }

            //$groupPointer
            $groupPointer = $this->ctx->SiteGroupMessageTable->queryMaxIdByGroup($groupId);

            $this->ctx->BaseTable->db->beginTransaction();
            foreach ($notExistsUserId as $userId) {

                //insert group Message pointer
                //$groupMessagePointerInfo = [];
                $this->ctx->SiteGroupMessageTable->updatePointer($groupId, $userId, "", $groupPointer);

                //insert into siteGroupUser
                $groupUserInfo = [
                    'groupId' => $groupId,
                    'userId' => $userId,
                    'memberType' => \Zaly\Proto\Core\GroupMemberType::GroupMemberNormal,
                    'timeJoin' => $this->ctx->ZalyHelper->getMsectime()
                ];
                $this->ctx->BaseTable->insertData($this->ctx->SiteGroupUserTable->table, $groupUserInfo, $this->ctx->SiteGroupUserTable->columns);
            }
            $this->ctx->BaseTable->db->commit();
        } catch (Exception $ex) {
            $this->ctx->BaseTable->db->rollback();
            $this->logger->error($tag, $ex->getMessage() . $ex->getTraceAsString());
            throw new Exception("invite failed");
        }
    }

}
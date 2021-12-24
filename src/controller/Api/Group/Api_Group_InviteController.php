<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 20/07/2018
 * Time: 4:11 PM
 */

class Api_Group_InviteController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupInviteRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupInviteResponse';
    public $userId;
    public $defaultMaxGroupMembers = -1;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiGroupInviteRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $groupId = $request->getGroupId();
            $userIds = $request->getUserIds();
            if (!$groupId) {
                $errorCode = $this->zalyError->errorGroupIdExists;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }

            $userList = [];
            foreach ($userIds as $key => $val) {
                $userList[] = $val;
            }

            if (!$userIds) {
                $errorCode = $this->zalyError->errorUserIdExists;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }

            //获取群组资料信息
            $groupInfo = $this->getGroupInfo($groupId);
            if ($groupInfo === false) {
                return;
            }

            //TODO 判断当前群组 进人规则.只能群主拉人，需要判断是不是群主
            //// 成员拉人，判断拉人者 是不是该群成员
            switch ($groupInfo['permissionJoin']) {
                case  \Zaly\Proto\Core\GroupJoinPermissionType::GroupJoinPermissionAdmin:

                    $isAdmin = $this->isGroupAdminMember($groupId, $this->userId);

                    if (!$isAdmin) {

                        $errorInfo = ZalyText::getText(ZalyText::$textGroupAdminInvite, $this->language);
                        $this->returnErrorCodeRPC("error.group.notAdmin", $errorInfo);

                        return;
                    }

                    break;
                case \Zaly\Proto\Core\GroupJoinPermissionType::GroupJoinPermissionMember:
                    $this->isGroupMember($groupId);
                    break;
            }

            //current group count
            $groupUserCount = $this->getGroupUserCount($groupId);
            $siteMaxGroupMembers = $groupInfo['maxMembers'];

            //default -1
            if ($siteMaxGroupMembers == $this->defaultMaxGroupMembers) {
                $siteMaxGroupMembers = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_MAX_GROUP_MEMBERS);
            }

            $newGroupUserCount = $groupUserCount + count($userIds);
            if ($siteMaxGroupMembers <= $groupUserCount || $siteMaxGroupMembers < $newGroupUserCount) {
                $errorCode = $this->zalyError->errorGroupMemberCount;
                $errorInfo = $this->language == 1 ? "因群组最大成员限制（{$siteMaxGroupMembers}），此操作未成功"
                    : "failed due to the max members limit of the group ({$siteMaxGroupMembers})";
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }

            /// 公开的直接进入
            $this->addMemberToGroup($userList, $groupId);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());

            $this->finish_request();

            //更新群头像
            if ($groupUserCount < 9) {
                $this->updateGroupAvatar($groupId);
            }

            //代码入群消息
            $this->proxyNewGroupMemberMessage($this->userId, $groupId, $userList);

            //代发群组公告notice
            //proxy send group-description notice to group
            $this->proxyGroupDescriptionNotice($this->userId, $groupInfo, $userList);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    private function addMemberToGroup($userIds, $groupId)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        try {
            if (!$userIds) {
                $errorCode = $this->zalyError->errorUserIdExists;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }
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
//            $groupMessagePointerInfo = [];
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
            $this->ctx->Wpf_Logger->error($tag, $ex->getMessage());
            $errorCode = $this->zalyError->errorGroupInvite;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("invite failed");
        }
    }


    //currentUserId ==proxy send to ==>userIds
    //"description",
    //"descriptionType",
    private function proxyGroupDescriptionNotice($currentUserId, $groupInfo, $userIds)
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

    //proxy join group members Message

    /**
     * @param $currentUserId
     * @param $groupId
     * @param $userIds
     */
    private function proxyNewGroupMemberMessage($currentUserId, $groupId, $userIds)
    {
        $fromUserId = $currentUserId;
        $msgType = Zaly\Proto\Core\MessageType::MessageNotice;

        $noticeText = $this->buildUserNotice($fromUserId, $userIds);

        if (empty($noticeText)) {
            return;
        }

        $this->ctx->Message_Client->proxyGroupNoticeMessage($fromUserId, $groupId, $noticeText);
    }

    private function buildUserNotice($fromUserId, $userIds)
    {

        if (empty($userIds)) {
            return "";
        }

        $nameBody = "";

        if (isset($fromUserId)) {
            $name = $this->getUserName($fromUserId);
            if ($name) {
                $nameBody .= $name . ZalyText::$keyGroupInvite;
            }
        }


        foreach ($userIds as $num => $userId) {

            $name = $this->getUserName($userId);

            if ($name) {
                if ($num == 0) {
                    $nameBody .= $name;
                } else {
                    $nameBody .= "," . $name;
                }
            }

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
        } else {
            return null;
        }

    }
}
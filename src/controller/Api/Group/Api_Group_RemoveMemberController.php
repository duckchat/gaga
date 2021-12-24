<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 25/07/2018
 * Time: 10:19 AM
 */

class Api_Group_RemoveMemberController extends Api_Group_BaseController
{

    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupRemoveMemberRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupRemoveMemberResponse';

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
                $errorCode = $this->zalyError->errorGroupRemoveGroupId;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }
            $groupInfo = $this->getGroupInfo($groupId);
            if($groupInfo === false) {
                return;
            }

            //TODO 判断当前群组.只能管理员
            $this->isGroupAdmin($groupId);

            $userList = [];
            foreach ($userIds as $key => $val) {
                $userList[] = $val;
            }

            if (!$userIds) {
                $errorCode = $this->zalyError->errorGroupRemoveUserId;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }
            $this->ctx->Wpf_Logger->info($tag, "userIds ==" . json_encode($userList) . " groupId ==" . $groupId);
            ////判断群是否存在
            $groupInfo = $this->getGroupInfo($groupId);
            $ownerUserId = $groupInfo['owner'];

            //get group member
            $groupMembers = $this->getGroupUserList($groupId, 0, 9);

            // 删除群成员
            $this->removeMemberFromGroup($userList, $ownerUserId, $groupId);
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());

            $this->finish_request();

            //update groupAvatar
            $this->updateGroupAvatarWhenRemoveUser($groupId, $userList, $groupMembers);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    private function updateGroupAvatarWhenRemoveUser($groupId, $userList, $groupMembers)
    {
        $needUpdate = false;

        $this->ctx->Wpf_Logger->info("api.group.removeMember", "groupId=" . $groupId);
        $this->ctx->Wpf_Logger->info("api.group.removeMember", "remove User=" . json_encode($userList));
        $this->ctx->Wpf_Logger->info("api.group.removeMember", "group member=" . json_encode($groupMembers));

        if ($groupMembers) {
            foreach ($groupMembers as $groupMember) {
                $memberUserId = $groupMember['userId'];
                if (in_array($memberUserId, $userList)) {
                    $needUpdate = true;
                    break;
                }
            }
        }

        $this->ctx->Wpf_Logger->info("api.group.removeMember", "need update Avatar =" . $needUpdate);
        if ($needUpdate) {
            $this->updateGroupAvatar($groupId);
        }
    }

    private function removeMemberFromGroup($userList, $ownerUserId, $groupId)
    {
        ////把自己剔除， 自己不能移除自己,  不能移除群主,管理员不能剔除管理员
        ///
        $adminType = \Zaly\Proto\Core\GroupMemberType::GroupMemberAdmin;
        $adminIds = $this->ctx->SiteGroupUserTable->getGroupUserByMemberType($groupId, $adminType, ["userId"]);
        if(!empty($adminIds)) {
            $adminIds = array_column($adminIds, "userId");
        }
        $exceptUserId = $this->userId == $ownerUserId ? [$this->userId] : array_merge([$this->userId, $ownerUserId],$adminIds);
        $userIds = array_diff($userList, $exceptUserId);

        if (!count($userIds)) {
            $errorCode = $this->zalyError->errorGroupRemoveMemberType;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception($errorInfo);
        }
        $flag = $this->ctx->SiteGroupUserTable->removeMemberFromGroup($userIds, $groupId);
        if (!$flag) {
            $errorCode = $this->zalyError->errorGroupRemove;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception($errorInfo);
        }
    }
}
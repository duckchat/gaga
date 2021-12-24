<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 23/07/2018
 * Time: 7:10 PM
 */

class Api_Group_InvitableFriendsController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupInvitableFriendsRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupInvitableFriendsResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiGroupInvitableFriendsRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $offset = $request->getOffset() ? $request->getOffset() : 0;
            $pageSize = $request->getCount();
            $groupId = $request->getGroupId();

            //获取群组资料信息
            $groupInfo = $this->getGroupInfo($groupId);
            if($groupInfo === false) {
                return;
            }
            //TODO 判断当前群组
            //// 成员拉人，判断拉人者 是不是该群成员
            switch ($groupInfo['permissionJoin']) {
                case  \Zaly\Proto\Core\GroupJoinPermissionType::GroupJoinPermissionAdmin:
                    $this->isGroupAdmin($groupId);
                    break;
                case \Zaly\Proto\Core\GroupJoinPermissionType::GroupJoinPermissionMember:
                    $this->isGroupMember($groupId);
                    break;
            }

            if (!$groupId) {
                $errorCode = $this->zalyError->errorGroupIdExists;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }

            $results = $this->getInvitableFriendsFromDB($this->userId, $groupId, $offset, $pageSize);
            $userCount = $this->getUserCount($groupId);

            $response = $this->buildGroupInvitableFriendsResponse($results, $userCount);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, $ex);
            $this->setRpcError("error.alert", $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }

    }


    private function getInvitableFriendsFromDB($userId, $groupId, $offset, $pageSize)
    {
        $result = $this->ctx->SiteUserFriendTable->getUserFriendListNotInGroup($userId, $groupId, $offset, $pageSize);
        return $result;
    }

    private function getUserCount($groupId)
    {
        return $this->ctx->SiteUserTable->getUserCount($groupId);
    }

    public function buildGroupInvitableFriendsResponse($results, $userCount)
    {
        $list = [];
        foreach ($results as $user) {
            $publicUserProfile = $this->getPublicUserProfile($user);
            $list[] = $publicUserProfile;
        }
        $response = new \Zaly\Proto\Site\ApiGroupInvitableFriendsResponse();
        $response->setList($list);
        $response->setTotalCount($userCount);
        return $response;
    }
}
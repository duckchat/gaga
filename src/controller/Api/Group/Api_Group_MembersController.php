<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 25/07/2018
 * Time: 8:43 PM
 */

class Api_Group_MembersController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupMembersRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupMembersResponse';
    public $userId;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiGroupMembersRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        try {
            $offset = $request->getOffset() ? $request->getOffset() : 0;
            $pageSize = $request->getCount() > $this->defaultPageSize || !$request->getCount() ? $this->defaultPageSize : $request->getCount();
            $groupId = $request->getGroupId();

            if (!$groupId) {
                $errorCode = $this->zalyError->errorGroupIdExists;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }

            $isGroupExists = $this->getGroupInfo($groupId);
            if($isGroupExists === false) {
                return;
            }

            //check permission , userId is groupMember

            if (!$this->isMemberOfGroup($this->userId, $groupId)) {
                $errInfo = "you're not a group member";
                if (Zaly\Proto\Core\UserClientLangType::UserClientLangZH == $this->language) {
                    $errInfo = "非群成员，无法查看";
                }
                throw new Exception($errInfo);
            }

            $userMemberCount = $this->getGroupUserCount($groupId);
            $userMembers = $this->getGroupUserList($groupId, $offset, $pageSize);
            $response = $this->getApiGroupMemberResponse($userMembers, $userMemberCount);

            $this->returnSuccessRPC($response);
        } catch (Exception $e) {
            //error.log
            $this->ctx->Wpf_Logger->error($tag, $e);
            //error.alert
            $this->returnErrorRPC(new $this->classNameForResponse(), $e);
        }
    }

    private function getApiGroupMemberResponse($userMembers, $userMemberCount)
    {
        $response = new \Zaly\Proto\Site\ApiGroupMembersResponse();
        $response->setTotalCount($userMemberCount);
        $list = [];
        foreach ($userMembers as $user) {
            $list[] = $this->getGroupMemberUserProfile($user);
        }
        $response->setList($list);
        return $response;
    }

    private function isMemberOfGroup($userId, $groupId)
    {
        $groupMember = $this->ctx->SiteGroupUserTable->getGroupUser($groupId, $userId);

        if ($groupMember && $groupMember['userId']) {
            return true;
        }

        return false;
    }

}
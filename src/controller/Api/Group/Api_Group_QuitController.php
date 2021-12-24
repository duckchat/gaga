<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 25/07/2018
 * Time: 7:10 PM
 */

class Api_Group_QuitController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupQuitRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupQuitResponse';
    public $userId;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiGroupProfileRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $groupId = $request->getGroupId();
            if (!$groupId) {
                $errorCode = $this->zalyError->errorGroupProfile;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }

            $groupInfo = $this->getGroupInfo($groupId);
            if($groupInfo === false) {
                return;
            }

            $this->quitGroup($groupId);

            $this->updateGroupAvatar($groupId);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), new \Zaly\Proto\Site\ApiGroupQuitResponse());
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new \Zaly\Proto\Site\ApiGroupQuitResponse());
        }
    }

    private function quitGroup($groupId)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $groupProfile = $this->getGroupProfile($groupId);
            if ($groupProfile['owner'] == $this->userId) {
                $errorCode = $this->zalyError->errorGroupQuitOwner;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }
            $userInfo = $this->ctx->SiteGroupUserTable->getGroupUser($groupId, $this->userId);
            if (!$userInfo) {
                return;
            }
            $userIds = [$this->userId];
            $this->ctx->SiteGroupUserTable->removeMemberFromGroup($userIds, $groupId);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg " . $e);
            throw new Exception($errorInfo);
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 20/07/2018
 * Time: 3:41 PM
 */

class Api_Group_DeleteController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupDeleteRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupDeleteResponse';
    public $userId;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $groupId = $request->getGroupId();
            if (!$groupId) {
                $errorCode = $this->zalyError->errorGroupDelete;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }

            $this->isGroupOwner($groupId);

            $this->deleteGroupInfo($groupId);

            // proxy group dissolution message
            $this->dissolutionProxyMessage($groupId);

            //remove group member
            $this->removeGroupMember($groupId);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    private function dissolutionProxyMessage($groupId)
    {

        try {
            $notice = ZalyText::$keyGroupDelete;
            $groupMembers = $this->ctx->SiteGroupUserTable->getGroupAllUser($groupId);
            if ($groupMembers) {

                foreach ($groupMembers as $groupMember) {

                    $memberId = $groupMember["userId"];
                    $this->ctx->Message_Client->proxyGroupAsU2NoticeMessage($memberId, $this->userId, $groupId, $notice);

                }

            }
        } catch (Exception $e) {
            $this->logger->error($this->action, $e);
        }
    }

    private function removeGroupMember($groupId)
    {
        try {

            $result = $this->ctx->SiteGroupUserTable->deleteGroupMembers($groupId);

        } catch (Exception $e) {
            $this->logger->error($this->action, $e->getMessage() . " " . $e->getTraceAsString());
        }
    }

}
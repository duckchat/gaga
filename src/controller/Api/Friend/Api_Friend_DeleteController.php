<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 09/08/2018
 * Time: 3:42 PM
 */

class Api_Friend_DeleteController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiFriendDeleteRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiFriendDeleteResponse';
    private $deleteFriend = 2;
    private $friend = 1;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiFriendDeleteRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $friendUserId = $request->getToUserId();
            $this->deleteFriend($friendUserId);
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        } catch (Exception $ex) {
            $errorCode = $this->zalyError->errorFriendDelete;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    /**
     * delete friend $friendUserId
     * @param $friendUserId
     * @throws Exception
     */
    private function deleteFriend($friendUserId)
    {
        //$this->userId follow $friendUserId?
        //relation  = 1 userId follow friendUserId or else
        $relation = $this->ctx->SiteUserFriendTable->isFollow($this->userId, $friendUserId);

        if ($relation == 0) {//0 is not friend
            return;
        }
        $this->ctx->SiteUserFriendTable->deleteFriend($this->userId, $friendUserId);
    }
}
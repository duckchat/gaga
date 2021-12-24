<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 02/08/2018
 * Time: 10:02 AM
 */

class Api_Friend_AcceptController extends BaseController
{

    protected $action = "api.friend.accept";
    private $classNameForRequest = '\Zaly\Proto\Site\ApiFriendAcceptRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiFriendAcceptResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiFriendAcceptRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $userId = $this->userId;
        $applyUserId = $request->getApplyUserId();
        $isAgree = $request->getAgree();

        try {
            $result = false;
            if ($userId == $applyUserId) {
                $eMessage = $this->language == 1 ? "请勿添加自己为好友" : "unable add yourself as friend";
                throw new Exception($eMessage);
            }
            if ($isAgree) {
                $result = $this->agreeFriendApply($userId, $applyUserId);

                if ($result) {
                    $this->removeFriendApply($applyUserId, $userId);
                    $this->removeFriendApply($userId, $applyUserId);
                }

            } else {
                $result = $this->removeFriendApply($applyUserId, $userId);
            }
            if ($result) {
                $this->setRpcError("success", "");
            } else {
                $this->setRpcError("error.alert", "");
            }
            $this->setRpcError($this->defaultErrorCode, "");
        } catch (Exception $e) {
            $this->setRpcError("error.alert", $e->getMessage());
            $this->logger->error($this->action, $e);
        }
        $this->rpcReturn($this->action, new $this->classNameForResponse());
        return;
    }

    protected function agreeFriendApply($userId, $applyUserId)
    {
        //查询 version

        $relation1 = $this->ctx->SiteUserFriendTable->isFollow($userId, $applyUserId);

        if ($relation1 != 1) {
            $success = $this->ctx->SiteUserFriendTable->saveUserFriend($userId, $applyUserId);

            if ($success) {
                //更新 version
            } else {
                return false;
            }
        }

        $relation2 = $this->ctx->SiteUserFriendTable->isFollow($applyUserId, $userId);

        if ($relation2 != 1) {
            //查询version
            $success = $this->ctx->SiteUserFriendTable->saveUserFriend($applyUserId, $userId);


            if ($success) {
                //更新version
            } else {
                return false;
            }

        }

        $applyData = $this->ctx->SiteFriendApplyTable->getApplyData($userId, $applyUserId);
        $greetings = $applyData['greetings'];

        $this->proxyNewFriendMessage($userId, $applyUserId, $greetings);
        return true;
    }

    /**
     * from apply to
     *
     * @param $fromUserId
     * @param $toUserId
     * @return bool
     */
    protected function removeFriendApply($fromUserId, $toUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteFriendApplyTable->deleteApplyData($fromUserId, $toUserId);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, e);
        }
        return false;
    }

    private function proxyNewFriendMessage($fromUserId, $toUserId, $greetings)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {

            $text = ZalyText::$keyFriendAcceptFrom;

            $this->ctx->Message_Client->proxyU2TextMessage($toUserId, $fromUserId, $toUserId, $text, true);

        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }

        try {
            if (empty($greetings)) {
                $greetings = $text = ZalyText::$keyFriendAcceptTo;
            }
            $this->ctx->Message_Client->proxyU2TextMessage($fromUserId, $toUserId, $fromUserId, $greetings, true);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }

    }

}
<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 28/09/2018
 * Time: 5:01 PM
 */

class MiniProgram_Square_ApplyController extends MiniProgram_BaseController
{
    private $squarePluginId = 199;

    protected function getMiniProgramId()
    {
        return $this->squarePluginId;
    }

    /**
     * 在处理正式请求之前，预处理一些操作，比如权限校验
     * @return bool
     */
    protected function preRequest()
    {
        //do nothing

        return true;
    }

    /**
     * 处理正式的请求逻辑，比如跳转界面，post获取信息等
     */
    protected function doRequest()
    {
        $friendId = $_POST['friendId'];
        $greeting = $_POST['greeting'];

        $result = [
            "errCode" => "error"
        ];


        try {//check site allow addfriend
            $this->checkSiteAddFriendConfig($this->userId);//check is friend before is friend,with exception
            $this->checkIsFriend($friendId);//save data

            if ($this->addApplyData($friendId, $greeting)) {
                $result['errCode'] = "success";
            }
            echo json_encode($result);

            $this->finish_request();//代发消息 && push
            $this->ctx->Message_Client->proxyNewFriendApplyMessage($friendId, $this->userId, $friendId);
            return;
        } catch (Exception $e) {
            $result['errInfo'] = $e->getMessage();
            $this->logger->error("miniProgram.square.apply", $e);
        }
        echo json_encode($result);
        return;
    }

    /**
     * preRequest && doRequest 发生异常情况，执行
     * @param $ex
     * @return mixed
     */
    protected function requestException($ex)
    {
        // TODO: Implement requestException() method.
    }

    private function checkSiteAddFriendConfig($userId)
    {
        $enableAddFriend = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ENABLE_ADD_FRIEND);

        if (empty($enableAddFriend)) {

            $isManager = $this->ctx->Site_Config->isManager($userId);
            if (!$isManager) {
                throw new Exception("site disable add friend");
            }

        }
    }

    private function checkIsFriend($toUserId)
    {
        $isFriend = $this->ctx->SiteUserFriendTable->isFriend($this->userId, $toUserId);
        if ($isFriend) {
            $errorCode = $this->zalyError->errorFriendApplyFriendExists;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception($errorInfo);
        }
    }

    /**
     * @param $toUserId
     * @param $greetings
     * @return bool
     * @throws Exception
     */
    private function addApplyData($toUserId, $greetings)
    {
        $result = false;
        if (empty($greetings)) {
            $greetings = "";
        } else {
            $greetings = trim($greetings);
        }

        try {
            $data = [
                "userId" => $this->userId,
                "friendId" => $toUserId,
                "greetings" => $greetings,
                "applyTime" => ZalyHelper::getMsectime(),
            ];
            $result = $this->ctx->SiteFriendApplyTable->insertApplyData($data);
        } catch (Exception $ex) {
            $where = [
                "userId" => $this->userId,
                "friendId" => $toUserId,
            ];
            $data = [
                "applyTime" => ZalyHelper::getMsectime(),
            ];

            if (isset($greetings)) {
                $data['greetings'] = $greetings;
            }
            $result = $this->ctx->SiteFriendApplyTable->updateApplyData($where, $data);
        }

        return $result;
    }
}
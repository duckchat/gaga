<?php
/**
 * checkout message status by client
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/17
 * Time: 7:07 PM
 */

class Api_Message_CheckStatusController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiMessageCheckStatusRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiMessageCheckStatusResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiMessageCheckStatusRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $response = new Zaly\Proto\Site\ApiMessageCheckStatusResponse();
        try {
            $msgIdList = $request->getMsgIds();
            $roomType = $request->getRoomType();

            if (!empty($msgIdList)) {

                $msgIdArray = [];

                foreach ($msgIdList as $msgId) {
                    $msgIdArray[] = $msgId;
                }

                if (Zaly\Proto\Core\MessageRoomType::MessageRoomU2 == $roomType) {

                    $existMsgIdList = $this->getU2MsgIdList($msgIdArray);

                    $statusMessageList = $this->buildStatusMessageList($existMsgIdList);
                    $response->setStatusMessages($statusMessageList);
                } elseif (Zaly\Proto\Core\MessageRoomType::MessageRoomGroup == $roomType) {

                    $existMsgIdList = $this->getGroupMsgIdList($msgIdArray);

                    $statusMessageList = $this->buildStatusMessageList($existMsgIdList);
                    $response->setStatusMessages($statusMessageList);
                }
            }
            $this->returnSuccessRPC($response);
        } catch (Exception $e) {
            $this->logger->error($this->action, $e);
            $this->returnErrorRPC($response, $e);
        }

        return;
    }


    private function getU2MsgIdList(array $msgIds)
    {
        $result = $this->ctx->SiteU2MessageTable->queryColumnMsgIdByMsgId($msgIds);
        return $result;
    }

    private function getGroupMsgIdList(array $msgIds)
    {
        $result = $this->ctx->SiteGroupMessageTable->queryColumnMsgIdByMsgId($msgIds);
        return $result;
    }

    private function buildStatusMessageList(array $msgIdList)
    {
        $statusResult = [];

        if (empty($msgIdList)) {
            return $statusResult;
        }

        foreach ($msgIdList as $msgId) {
            $statusMessage = new Zaly\Proto\Core\StatusMessage();
            $statusMessage->setMsgId($msgId);
            $statusMessage->setStatus(Zaly\Proto\Core\MessageStatus::MessageStatusServer);
            $statusResult[] = $statusMessage;
        }
        return $statusResult;
    }

}
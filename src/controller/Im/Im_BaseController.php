<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 13/07/2018
 * Time: 6:32 PM
 */

use Zaly\Proto\Core\TransportData;
use Zaly\Proto\Core\TransportDataHeaderKey;

abstract class Im_BaseController extends BaseController
{
    private $imStcMessagAction = "im.stc.message";

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $this->keepSocket();//keep socket
        $this->doRequest($request, $transportData);
    }

    /**
     * 己收到请求，并且校验完成session，准备处理具体逻辑
     * 当执行到doRealRpc() ,开始处理各自的具体业务逻辑
     *
     * @param \Google\Protobuf\Internal\Message $request
     * @param TransportData $transportData
     * @return mixed
     */
    public abstract function doRequest(\Google\Protobuf\Internal\Message $request, Zaly\Proto\Core\TransportData $transportData);

    /**
     * @param $sessionId
     * @param $msgId
     * @param $msgRoomType
     * @param bool $result
     */
    protected function returnMessageStatus($sessionId, $msgId, $msgRoomType, $result)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $this->ctx->Wpf_Logger->info($tag, "sessionId=" . $sessionId);
        $responseStatusMessage = new Zaly\Proto\Client\ImStcMessageRequest();

        $statusMsg = new \Zaly\Proto\Core\StatusMessage();
        $statusMsg->setMsgId($msgId);
        $status = $result ? \Zaly\Proto\Core\MessageStatus::MessageStatusServer : \Zaly\Proto\Core\MessageStatus::MessageStatusFailed;
        $statusMsg->setStatus($status);

        $message = new \Zaly\Proto\core\Message();
        $message->setMsgId($msgId);
        $message->setRoomType($msgRoomType);
        $message->setTimeServer($this->ctx->ZalyHelper->getMsectime());
        $message->setType(\Zaly\Proto\Core\MessageType::MessageEventStatus);
        $message->setStatus($statusMsg);

        $list = [$message];
        $responseStatusMessage->setList($list);

        $this->setRpcError($this->defaultErrorCode, "");
        $this->rpcReturn($this->imStcMessagAction, $responseStatusMessage);
    }

    // socket
    public function notKeepSocket()
    {
        header("KeepSocket: false");
    }

    public function getRpcError()
    {
        return isset($this->headers[TransportDataHeaderKey::HeaderErrorCode]) ? $this->headers[TransportDataHeaderKey::HeaderErrorCode] : "";
    }
}
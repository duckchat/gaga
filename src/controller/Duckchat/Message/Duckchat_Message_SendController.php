<?php
/**
 * duckchat对外接口
 * User: SAM<an.guoyue254@gmail.com>
 * Date: 04/09/2018
 * Time: 4:27 PM
 */

class Duckchat_Message_SendController extends Duckchat_MiniProgramController
{
    private $classNameForRequest = '\Zaly\Proto\Plugin\DuckChatMessageSendRequest';
    private $classNameForResponse = '\Zaly\Proto\Plugin\DuckChatMessageSendResponse';

    private $isGroupRoom = false;
    private $toId;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Plugin\DuckChatMessageSendRequest $request
     * @param \Zaly\Proto\Core\TransportData $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        try {

            $this->returnSuccessRPC(new \Zaly\Proto\Plugin\DuckChatMessageSendResponse());

            $this->ctx->DuckChat_Message->send($this->pluginMiniProgramId, $request);
        } catch (Exception $e) {
            $this->logger->error($this->action, $e);
            $this->returnErrorRPC(new \Zaly\Proto\Plugin\DuckChatMessageSendResponse(), $e);
        }

        return;
    }

}
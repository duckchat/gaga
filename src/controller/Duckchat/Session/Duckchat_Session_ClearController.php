<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 08/09/2018
 * Time: 12:11 PM
 */

class Duckchat_Session_ClearController extends Duckchat_MiniProgramController
{
    private $classNameForRequest = '\Zaly\Proto\Plugin\DuckChatSessionClearRequest';
    private $classNameForResponse = '\Zaly\Proto\Plugin\DuckChatSessionClearResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Plugin\DuckChatSessionClearRequest $request
     * @param \Zaly\Proto\Core\TransportData $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        try {
            $response = $this->ctx->DuckChat_Session->clearSession($this->pluginMiniProgramId, $request);

            if ($response) {
                $this->returnSuccessRPC($response);
            } else {
                throw new Exception("clear user session result=false");
            }

        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($this->action, $ex->getMessage() . $ex->getTraceAsString());
            $this->returnErrorRPC(new \Zaly\Proto\Plugin\DuckChatSessionClearResponse(), $ex);
        }
        return;
    }

}
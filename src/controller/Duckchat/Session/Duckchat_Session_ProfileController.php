<?php
/**
 * 通过duckchat_sessionid 校验用户身份
 * User: SAM<an.guoyue254@gmail.com>
 * Date: 04/09/2018
 * Time: 4:27 PM
 */

class Duckchat_Session_ProfileController extends Duckchat_MiniProgramController
{
    private $classNameForRequest = '\Zaly\Proto\Plugin\DuckChatSessionProfileRequest';
    private $classNameForResponse = '\Zaly\Proto\Plugin\DuckChatSessionProfileResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Plugin\DuckChatSessionProfileRequest $request
     * @param \Zaly\Proto\Core\TransportData $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        try {
            $pluginId = $this->pluginMiniProgramId;

            $response = $this->ctx->DuckChat_Session->getProfile($pluginId, $request);

            $this->returnSuccessRPC($response);
        } catch (Exception $e) {
            $this->setRpcError("error.alert", $e->getMessage() . $e->getTraceAsString());
            $this->returnErrorRPC(new Zaly\Proto\Plugin\DuckChatSessionProfileResponse(), $e);
        }
        return;
    }

}
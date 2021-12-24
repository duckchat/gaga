<?php
/**
 * 获取用户资料
 * User: SAM<an.guoyue254@gmail.com>
 * Date: 04/09/2018
 * Time: 4:37 PM
 */

class Duckchat_User_ProfileController extends Duckchat_MiniProgramController
{
    private $classNameForRequest = '\Zaly\Proto\Plugin\DuckChatUserProfileRequest';
    private $classNameForResponse = '\Zaly\Proto\Plugin\DuckChatUserProfileResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Plugin\DuckChatUserProfileRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {

            $response = $this->ctx->DuckChat_User->getProfile($this->pluginMiniProgramId, $request);

            $this->returnSuccessRPC($response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error=" . $ex->getMessage() . $ex->getTraceAsString());
            $this->returnErrorRPC(new $this->classNameForResponse(), $ex);
        }
    }

}
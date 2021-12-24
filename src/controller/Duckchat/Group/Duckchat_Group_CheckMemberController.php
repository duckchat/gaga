<?php
/**
 * duckchat 对外接口
 * User: SAM<an.guoyue254@gmail.com>
 * Date: 04/09/2018
 * Time: 4:29 PM
 */

class Duckchat_Group_CheckMemberController extends Duckchat_MiniProgramController
{
    private $classNameForRequest = '\Zaly\Proto\Plugin\DuckChatGroupCheckMemberRequest';
    private $classNameForResponse = '\Zaly\Proto\Plugin\DuckChatGroupCheckMemberResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Plugin\DuckChatGroupCheckMemberRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $response = $this->ctx->DuckChat_Group->checkMember($this->pluginMiniProgramId, $request);
            $this->returnSuccessRPC($response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error=" . $ex->getMessage() . "\n" . $ex->getTraceAsString());
            $this->returnErrorRPC(new $this->classNameForResponse(), $ex);
        }

    }

}
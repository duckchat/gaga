<?php

/**
 * 获取用户关系
 * User: SAM<an.guoyue254@gmail.com>
 * Date: 04/09/2018
 * Time: 6:15 PM
 */
class Duckchat_User_RelationController extends Duckchat_MiniProgramController
{
    private $classNameForRequest = '\Zaly\Proto\Plugin\DuckChatUserRelationRequest';
    private $classNameForResponse = '\Zaly\Proto\Plugin\DuckChatUserRelationResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Plugin\DuckChatUserRelationRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $response = $this->ctx->DuckChat_User->getRelation($this->pluginMiniProgramId, $request);
            $this->returnSuccessRPC($response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error=" . $ex->getMessage() . "\n" . $ex->getTraceAsString());
            $this->returnErrorRPC(new $this->classNameForResponse(), $ex);
        }
        return;
    }

}
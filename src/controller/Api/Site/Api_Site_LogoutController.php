<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 18/07/2018
 * Time: 8:32 AM
 */


class Api_Site_LogoutController extends \BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiSiteLogoutRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiSiteLogoutResponse';
    private $logoutAction = "api.site.logout";

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;

        $this->ctx->Wpf_Logger->info($tag, "do something for logout");

        //remove sessionId
        $this->ctx->SiteSessionTable->deleteSession($this->userId, $this->sessionId);

        $this->setRpcError($this->defaultErrorCode, "");
        $this->rpcReturn($this->logoutAction, new \Zaly\Proto\Site\ApiSiteLogoutResponse());
        return;
    }

}


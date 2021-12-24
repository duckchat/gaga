<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/11/2018
 * Time: 4:56 PM
 */

class Site_CustomerSessionVerify
{

    private $ctx;
    private $logger;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
        $this->logger = $ctx->getLogger();
    }

    //本地passport登陆，校验preSession
    public function doLocalVerify($preSessionId)
    {
        $preSessionId = trim($preSessionId);

        if (!$preSessionId) {
            throw new Exception("preSessionId is 404 ");
        }

        $userInfo = $this->ctx->PassportCustomerServicePreSessionTable->getInfoByPreSessionId($preSessionId);

        if (!$userInfo || !$userInfo['userId']) {
            throw new Exception("user info is empty by preSessionId");
        }

        $loginProfile = $this->buildLoginUserProfile($userInfo);

        $this->deletePreSession($preSessionId);

//        $this->logger->error("site.session.verify=========", 'VERIFY PROFILE=' . $loginProfile->serializeToJsonString());
        return $loginProfile;
    }


    //Api passport登陆，校验preSession，提供API对外接口中使用
    //API的参数需要加密校验
    public function doApiVerify($preSessionId)
    {
        $preSessionId = trim($preSessionId);

        if (!$preSessionId) {
            throw new Exception("preSessionId is 404 ");
        }

        $userInfo = $this->ctx->PassportCustomerServicePreSessionTable->getInfoByPreSessionId($preSessionId);

        if (!$userInfo || !$userInfo['userId']) {
            throw new Exception("user info is empty by preSessionId");
        }

        $sitePubkPem =  $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ID_PUBK_PEM);

        $loginProfile = $this->buildLoginUserProfile($userInfo);

        $this->deletePreSession($preSessionId);

//        $this->logger->error("api.session.verify=========", 'VERIFY PROFILE=' . $loginProfile->serializeToJsonString());
        return [
            "loginProfile" => $loginProfile,
            "sitePubkPem" => $sitePubkPem,
        ];
    }


    /**
     * @param $userInfo
     * @return \Zaly\Proto\Platform\LoginUserProfile
     * @throws Exception
     */
    private function buildLoginUserProfile($userInfo)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        try {
            $loginName = $userInfo['loginName'];
            $nickname = $userInfo['loginName'];
            $userId = $userInfo['userId'];
            $userProfile = new \Zaly\Proto\Platform\LoginUserProfile();
            $userProfile->setUserId($userId);
            $userProfile->setLoginName($loginName);
            $userProfile->setNickName($nickname);
            $this->ctx->Wpf_Logger->info("site: api.session.verify", "proto profile=" . $userProfile->serializeToJsonString());
            return $userProfile;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->info($tag, $ex);
            throw $ex;
        }
    }

    private function deletePreSession($preSessionId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->PassportCustomerServicePreSessionTable->delInfoByPreSessionId($preSessionId);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
    }

}
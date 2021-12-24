<?php
/**
 * api.session.verify 接口的本地实现方式
 *
 * 1.API接口，api.session.verify
 * 2.DB接口，Site_SessionVerify
 *
 * User: anguoyue
 * Date: 2018/11/7
 * Time: 5:20 PM
 */

class Site_SessionVerify
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

        $userInfo = $this->ctx->PassportPasswordPreSessionTable->getInfoByPreSessionId($preSessionId);

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

        $userInfo = $this->ctx->PassportPasswordPreSessionTable->getInfoByPreSessionId($preSessionId);

        if (!$userInfo || !$userInfo['userId']) {
            throw new Exception("user info is empty by preSessionId");
        }

        $sitePubkPem = base64_decode($userInfo['sitePubkPem']);
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
            $nickname = $userInfo['nickname'];
            $userId = $userInfo['userId'];
            $userProfile = new \Zaly\Proto\Platform\LoginUserProfile();
            $userProfile->setUserId($userId);
            $userProfile->setLoginName($loginName);
            $userProfile->setNickName($nickname);
            $userProfile->setInvitationCode($userInfo['invitationCode']);

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
            return $this->ctx->PassportPasswordPreSessionTable->delInfoByPreSessionId($preSessionId);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
    }

}
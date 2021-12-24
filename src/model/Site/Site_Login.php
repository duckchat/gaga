<?php
/**
 * 登陆站点操作
 * User: zhangjun
 * Date: 06/08/2018
 * Time: 11:45 PM
 */

class Site_Login
{
    private $ctx;
    private $zalyError;
    private $pinyin;
    private $timeOut = 10;
    private $logger;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
        $this->logger = $ctx->getLogger();
        $this->zalyError = $this->ctx->ZalyErrorZh;
        $this->pinyin = new \Overtrue\Pinyin\Pinyin();
    }

    /**
     * @param $thirdPartyKey loginKey to choose loginWay
     * @param $preSessionId
     * @param $devicePubkPem
     * @param $clientType
     * @param $userCustomArray
     * @return array|void
     * @throws Exception
     */
    public function doLogin($thirdPartyKey, $preSessionId, $devicePubkPem, $clientType, $userCustomArray)
    {
        $userProfile = false;

        if (empty($thirdPartyKey)) {
            //do site passport login
            $userProfile = $this->loginBySitePassport($preSessionId, $devicePubkPem, $clientType);
        } else {
            if($thirdPartyKey == 'DuckChat_CustomerService') {
                $userProfile = $this->loginByDuckChatCustomerService($thirdPartyKey, $preSessionId, $devicePubkPem, $clientType);
            } else {
                //get third login by thirdPartyKey
                $userProfile = $this->loginByThirdParty($thirdPartyKey, $preSessionId, $devicePubkPem, $clientType);
            }
        }

        if ($userProfile && !empty($userCustomArray)) {
            //login success, save custom
            $this->saveUserCustoms($userProfile["userId"], $userCustomArray);
        }

        return $userProfile;
    }

    //site passport login【本地登陆】
    private function loginBySitePassport($preSessionId, $devicePubkPem, $clientSideType = Zaly\Proto\Core\UserClientType::UserClientMobileApp)
    {
        //实现本地api.session.verify 逻辑
        $loginUserProfile = $this->ctx->Site_SessionVerify->doLocalVerify($preSessionId);

        //if loginUserProfile exist
        if (!$loginUserProfile || empty($loginUserProfile->getLoginName()) || empty($loginUserProfile->getLoginName())) {
            throw new Exception("get user profile error from site passport");
        }

        //get intivation first
        $uicInfo = $this->getIntivationCode($loginUserProfile->getInvitationCode());

        $userProfile = $this->doSiteLoginAction(false, $loginUserProfile, $devicePubkPem, $uicInfo, $clientSideType, "");

        return $userProfile;
    }

    /**
     * third party login【第三方登陆】
     *
     * @param $thirdPartyLoginKey
     * @param $preSessionId
     * @param string $devicePubkPem
     * @param int $clientSideType
     * @return array
     * @throws Exception
     */
    public function loginByThirdParty($thirdPartyLoginKey, $preSessionId, $devicePubkPem = "", $clientSideType = Zaly\Proto\Core\UserClientType::UserClientMobileApp)
    {
        try {
            //get site config::publicKey
            $sitePriKeyPem = $this->getSiteConfigPriKeyFromDB();

            $sessionVerifyUrl = ZalyLogin::getVerifyUrl($thirdPartyLoginKey);

            //get userProfile from platform
            $loginUserProfile = $this->getUserProfileFromThirdParty($preSessionId, $sitePriKeyPem, $sessionVerifyUrl);

            //if loginUserProfile exist
            if (!$loginUserProfile || empty($loginUserProfile->getUserId()) || empty($loginUserProfile->getLoginName())) {
                throw new Exception("get user profile error from third party");
            }

            $newLoginName = $thirdPartyLoginKey . "_" . $loginUserProfile->getLoginName();
            //update loginName
            $loginUserProfile->setLoginName($newLoginName);

            $thirdPartyLoginUserId = $loginUserProfile->getUserId();
            $thirdPartyInfo = $this->getThirdPartyAccount($thirdPartyLoginKey, $thirdPartyLoginUserId);

            $siteUserId = false;

            if ($thirdPartyInfo) {
                $siteUserId = $thirdPartyInfo['userId'];
            }

            //get intivation first
            $uicInfo = $this->getIntivationCode($loginUserProfile->getInvitationCode());

            $userProfile = $this->doSiteLoginAction($siteUserId, $loginUserProfile, $devicePubkPem, $uicInfo, $clientSideType, $thirdPartyLoginKey);

            //save to thirdParty login table
            $this->bindThirdPartyAccount($siteUserId, $thirdPartyLoginKey, $userProfile);

            return $userProfile;
        } catch (Exception $ex) {
            $tag = __CLASS__ . "-" . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, " errorMsg = " . $ex->getMessage() . $ex->getTraceAsString());
            throw $ex;
        }
    }

    /**
     * third party login【第三方登陆】
     *
     * @param $thirdPartyLoginKey
     * @param $preSessionId
     * @param string $devicePubkPem
     * @param int $clientSideType
     * @return array
     * @throws Exception
     */
    public function loginByDuckChatCustomerService($thirdPartyLoginKey, $preSessionId, $devicePubkPem = "", $clientSideType = Zaly\Proto\Core\UserClientType::UserClientMobileApp)
    {
        try {
            //get site config::publicKey
            $sitePriKeyPem = $this->getSiteConfigPriKeyFromDB();

            //实现本地api.session.verify 逻辑
            $loginUserProfile = $this->ctx->Site_CustomerSessionVerify->doLocalVerify($preSessionId);

            //if loginUserProfile exist
            if (!$loginUserProfile || empty($loginUserProfile->getUserId()) || empty($loginUserProfile->getLoginName())) {
                throw new Exception("get user profile error from third party");
            }

            $newLoginName = $thirdPartyLoginKey . "_" . $loginUserProfile->getLoginName();
            //update loginName
            $loginUserProfile->setLoginName($newLoginName);

            $thirdPartyLoginUserId = $loginUserProfile->getUserId();
            $thirdPartyInfo = $this->getThirdPartyAccount($thirdPartyLoginKey, $thirdPartyLoginUserId);

            $siteUserId = false;

            if ($thirdPartyInfo) {
                $siteUserId = $thirdPartyInfo['userId'];
            }

            //get intivation first
            $uicInfo = $this->getIntivationCode($loginUserProfile->getInvitationCode());

            $userProfile = $this->doSiteLoginAction($siteUserId, $loginUserProfile, $devicePubkPem, $uicInfo, $clientSideType, $thirdPartyLoginKey);

            //save to thirdParty login table
            $this->bindThirdPartyAccount($siteUserId, $thirdPartyLoginKey, $userProfile);

            return $userProfile;
        } catch (Exception $ex) {
            $tag = __CLASS__ . "-" . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, " errorMsg = " . $ex->getMessage() . $ex->getTraceAsString());
            throw $ex;
        }
    }

    /**
     * 获取站点设置
     * @return string
     */
    private function getSiteConfigPriKeyFromDB()
    {
        try {
            $prikKeyPem = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ID_PRIK_PEM);
            return $prikKeyPem;
        } catch (Exception $ex) {
            $tag = __CLASS__ . "-" . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, "errorMsg = " . $ex->getMessage());
            return '';
        }
    }

    /**
     * @param $preSessionId
     * @param $sitePrikPem
     * @param $sessionVerifyUrl
     * @return \Zaly\Proto\Platform\LoginUserProfile
     * @throws Exception
     */
    private function getUserProfileFromThirdParty($preSessionId, $sitePrikPem, $sessionVerifyUrl)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $sessionVerifyRequest = new \Zaly\Proto\Platform\ApiSessionVerifyRequest();
            $sessionVerifyRequest->setPreSessionId($preSessionId);

            $sessionVerifyUrl = ZalyHelper::getFullReqUrl($sessionVerifyUrl);

//            $this->logger->error("==============request to api.session.verify Url=", $sessionVerifyUrl);

            $response = $this->ctx->ZalyCurl->httpRequestByAction('POST', $sessionVerifyUrl, $sessionVerifyRequest, $this->timeOut);

            ///获取数据
            $key = $response->getKey();

            $aesData = $response->getEncryptedProfile();
            $randomKey = $this->ctx->ZalyRsa->decrypt($key, $sitePrikPem);

            $serialize = $this->ctx->ZalyAes->decrypt($aesData, $randomKey);

            //获取LoginUserProfile
            $loginUserProfile = unserialize($serialize);

            return $loginUserProfile;
        } catch (Exception $ex) {
            $errorCode = $this->zalyError->errorSession;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->ctx->Wpf_Logger->error($tag, "api.site.login error=" . $ex);
            throw new Exception($errorInfo);
        }
    }

    /**
     * 处理站点登陆具体逻辑
     *
     * @param $siteUserId
     * @param Zaly\Proto\Platform\LoginUserProfile $loginUserProfile
     * @param $devicePubkPem
     * @param $uicInfo
     * @param $clientSideType
     * @param $loginKeyId
     * @return array
     * @throws Exception
     */
    private function doSiteLoginAction($siteUserId, $loginUserProfile, $devicePubkPem, $uicInfo, $clientSideType, $loginKeyId)
    {
        if (!$loginUserProfile) {
            $errorCode = $this->zalyError->errorSession;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            throw new Exception($errorInfo);
        }

        $sitePubkPem = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ID_PUBK_PEM);
        $sourceLoginUserId = $loginUserProfile->getUserId();

        $tmpUserId = $sourceLoginUserId . "@" . $sitePubkPem;

        if (!empty($loginKeyId)) {
            $tmpUserId .= "@" . $loginKeyId;
        }

        $userId = sha1($tmpUserId);

        $nameInLatin = $this->pinyin->permalink($loginUserProfile->getNickName(), "");
        $countryCode = $loginUserProfile->getPhoneCountryCode();
        if (!$countryCode) {
            $countryCode = "86";
        }

        $userProfile = [
            "userId" => $userId,
            "loginName" => $loginUserProfile->getLoginName(),
            "loginNameLowercase" => strtolower($loginUserProfile->getLoginName()),
            "nickname" => $loginUserProfile->getNickname(),
            "nicknameInLatin" => $nameInLatin,
            "countryCode" => $countryCode,
            "phoneId" => $loginUserProfile->getPhoneNumber(),
            "timeReg" => $this->ctx->ZalyHelper->getMsectime(),
        ];

        //if $siteUserId is empty, use $userId
        if (empty($siteUserId)) {
            $siteUserId = $userId;
        }

        $siteUser = $this->checkUserExists($siteUserId);

        if (!$siteUser) {
            //no user ,register new user
            //check user invitation code and realName for phonenumber
            $this->verifyUicAndRealName($loginUserProfile->getUserId(), $loginUserProfile->getPhoneNumber(), $uicInfo);

            //save profile to db
            $userProfile['availableType'] = \Zaly\Proto\Core\UserAvailableType::UserAvailableNormal;
            $userProfile['avatar'] = ZalyAvatar::getRandomAvatar();

            $result = $this->insertSiteUserProfile($userProfile);

            if ($result) {
                $this->ctx->Site_Default->addDefaultFriendsAndGroups($userProfile['userId']);
            } else {
                throw new Exception("save new user profile to db error");
            }
        } else {
            //user exists ,set user avatar
            $userProfile['avatar'] = $siteUser['avatar'];
        }

        //这里
        $sessionInfo = $this->insertOrUpdateUserSession($userProfile, $devicePubkPem, $clientSideType, $loginKeyId);
        $userProfile['sessionId'] = $sessionInfo['sessionId'];
        $userProfile['deviceId'] = $sessionInfo['deviceId'];
        $userProfile['sourceLoginUserId'] = $sourceLoginUserId;
        return $userProfile;
    }

    private function getThirdPartyAccount($thirdPartyLoginKey, $thirdPartyLoginUserId)
    {
        return $this->ctx->SiteThirdPartyLoginTable->getAccountInfo($thirdPartyLoginKey, $thirdPartyLoginUserId);
    }

    private function bindThirdPartyAccount($siteUserId, $thirdPartyLoginKey, $userProfile)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        if (!$userProfile) {
            $this->logger->error($tag, "bind third party account error");
            return false;
        }

        $this->logger->error('bind thirdParty account', "siteUserId=========" . $siteUserId);
        if (!empty($siteUserId)) {
            //if go here means 1.already bind account 2.already save account
            return true;
        }

        try {
            $data = [
                "userId" => $userProfile["userId"],
                "loginKey" => $thirdPartyLoginKey,
                "loginUserId" => $userProfile["sourceLoginUserId"],
            ];
            return $this->ctx->SiteThirdPartyLoginTable->saveAccountInfo($data);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
        return false;
    }


    //support site admins add custom items for users
    private function saveUserCustoms($userId, array $userCustoms)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $userCustoms["userId"] = $userId;
            return $this->ctx->SiteUserCustomTable->insertCustomProfile($userCustoms);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
        return false;
    }

    private function getIntivationCode($invitationCode)
    {
        if (empty($invitationCode)) {
            return false;
        }
        return $this->ctx->SiteUicTable->queryUicByCode($invitationCode);
    }

    private function checkUserExists($userId)
    {
        try {
            $user = $this->ctx->SiteUserTable->getUserByUserId($userId);

            if ($user && isset($user['userId']) && isset($user['loginName'])) {
                return $user;
            }

            return false;
        } catch (Exception $ex) {
            throw new Exception("check user is fail");
        }
    }

    /**
     * @param $userId
     * @param $phoneNumber
     * @param $uicInfo
     * @throws Exception
     */
    private function verifyUicAndRealName($userId, $phoneNumber, $uicInfo)
    {
        $configKeys = [SiteConfig::SITE_ENABLE_INVITATION_CODE, SiteConfig::SITE_ENABLE_REAL_NAME];
        $config = $this->ctx->SiteConfigTable->selectSiteConfig($configKeys);

        if ($config[SiteConfig::SITE_ENABLE_INVITATION_CODE]) {

            if (empty($uicInfo) || $uicInfo['status'] == 0 || $uicInfo['userId']) {
                $errorCode = $this->zalyError->errorInvitationCode;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                throw new Exception($errorInfo);
            }

            //update uic used
            $this->ctx->SiteUicTable->updateUicUsed($uicInfo['code'], $userId);
        }

        if ($config[SiteConfig::SITE_ENABLE_REAL_NAME]) {
            if (!$phoneNumber || !ZalyHelper::isPhoneNumber($phoneNumber)) {
                throw new Exception("phone number is error");
            }
        }
    }

    /**
     * save user profile
     *
     * @param $userProfile
     * @return bool
     * @throws Exception
     */
    private function insertSiteUserProfile($userProfile)
    {
        try {
            return $this->ctx->SiteUserTable->insertUserInfo($userProfile);
        } catch (Exception $e) {
            throw new Exception("insert user is fail");
        }
    }

    /**
     * 更新站点session
     *
     * @param $userProfile
     * @param $devicePubkPem
     * @param $clientSideType
     * @param $loginKeyId
     * @return array
     */
    private function insertOrUpdateUserSession($userProfile, $devicePubkPem, $clientSideType, $loginKeyId)
    {
        $sessionId = $this->ctx->ZalyHelper->generateStrId();
        $deviceId = sha1($devicePubkPem);
        //add session
        $userId = $userProfile["userId"];

        if (!empty($devicePubkPem)) {
            //get session by deviceId
            $this->ctx->SiteSessionTable->deleteSessionByDeviceId($deviceId);
        } else {
            $this->ctx->SiteSessionTable->deleteSessionByUserIdAndDeviceId($userId, $deviceId);
        }

        try {
            $sessionInfo = [
                "sessionId" => $sessionId,
                "userId" => $userId,
                "deviceId" => $deviceId,
                "devicePubkPem" => $devicePubkPem,
                "timeWhenCreated" => $this->ctx->ZalyHelper->getMsectime(),
                "ipActive" => ZalyHelper::getIp(),
                "timeActive" => $this->ctx->ZalyHelper->getMsectime(),
                "ipActive" => ZalyHelper::getIp(),
                "clientSideType" => $clientSideType,
                "loginPluginId" => $loginKeyId,//thirdParty key=loginKey=loginId, same meaning
            ];
            $this->ctx->SiteSessionTable->insertSessionInfo($sessionInfo);
        } catch (Exception $ex) {
            //update session
            $userId = $userProfile["userId"];
            $sessionInfo = [
                "sessionId" => $sessionId,
                "timeActive" => $this->ctx->ZalyHelper->getMsectime(),
                "ipActive" => ZalyHelper::getIp(),
                "clientSideType" => $clientSideType,
                "loginPluginId" => $loginKeyId,
            ];
            $where = [
                "userId" => $userId,
                "deviceId" => $deviceId,
            ];
            $this->ctx->SiteSessionTable->updateSessionInfo($where, $sessionInfo);
        }

        $sessionInfo['sessionId'] = $sessionId;
        $sessionInfo['deviceId'] = $deviceId;
        return $sessionInfo;
    }

}
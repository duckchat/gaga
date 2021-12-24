<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 18/07/2018
 * Time: 8:32 AM
 */


class Api_Site_LoginController extends \BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiSiteLoginRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiSiteLoginResponse';
    private $sessionVerifyAction = "api.session.verify";
    private $pinyin;
    public $siteCookieName = "zaly_site_user";
    private $cookieTimeOut = 2592000;//30天 单位s
    private $customerServiceKey='DuckChat_CustomerService';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiSiteLoginRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        header('Access-Control-Allow-Origin: *');

        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $loginName = $request->getLoginName();
            $userExists = $this->checkUserExists($loginName);
            $isRegister = $request->getIsRegister();

            if (!$userExists && $isRegister == false) {
                $errorCode = $this->zalyError->errorUserNeedRegister;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
                return;
            }

            $preSessionId = $request->getPreSessionId();
            $devicePubkPem = trim($request->getDevicePubkPem());

            //get user profile from platform clientSiteType=1:mobile client
            $clientType = $this->getUserClient($devicePubkPem);

            if (empty($preSessionId) || (empty($devicePubkPem) && Zaly\Proto\Core\UserClientType::UserClientWeb != $clientType)) {
                throw new Exception("with error parameters");
            }

            $this->ctx->Wpf_Logger->info("api.site.login", "preSessionId=" . $preSessionId);

            if (!$preSessionId) {
                $errorCode = $this->zalyError->errorSiteLogin;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }


            $thirdPartyKey = trim($request->getThirdPartyKey());
            $userCustoms = $request->getUserCustoms();

            $customData = [];
            if (!empty($userCustoms)) {
                foreach ($userCustoms as $custom) {
                    $key = trim($custom->getCustomKey());
                    $customValue = $custom->getCustomValue();
                    $value = isset($customValue) ? trim($customValue) : "";
                    $customData[$key] = $value;
                }
            }

            $userProfile = $this->ctx->Site_Login->doLogin($thirdPartyKey, $preSessionId, $devicePubkPem, $clientType, $customData);

            $realSessionId = $userProfile['sessionId'];
            $response = $this->buildApiSiteLoginResponse($userProfile, $realSessionId);

            if ($clientType == \Zaly\Proto\Core\UserClientType::UserClientWeb) {
                if($thirdPartyKey != $this->customerServiceKey) {
                    setcookie($this->siteCookieName, $realSessionId, time() + $this->cookieTimeOut, "/", "", false, true);
                }
            } else {
                //clearLimitSession
                $this->clearLimitSession($userProfile['userId'], $userProfile['deviceId']);
            }
            //back to request
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error=" . $ex);
            $errorCode = $this->zalyError->errorSiteLogin;
//                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }

        return;
    }

    private function buildApiSiteLoginResponse($userInfo, $sessionId)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;

        try {
            $publicUserProfile = $this->getPublicUserProfile($userInfo);

            if (\Zaly\Proto\Core\UserAvailableType::UserAvailableBlocked == $publicUserProfile->getAvailableType()) {
                throw new Exception("user is blocked");
            }
            $allUserProfile = new \Zaly\Proto\Core\AllUserProfile();
            $allUserProfile->setPublic($publicUserProfile);

            $response = new \Zaly\Proto\Site\ApiSiteLoginResponse();
            $response->setProfile($allUserProfile);
            $response->setSessionId($sessionId);
            return $response;
        } catch (Exception $ex) {
            $errorCode = $this->zalyError->errorSiteLogin;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            throw new Exception("response is fail");
        }
    }

    private function checkUserExists($loginName)
    {
        try {
            $user = $this->ctx->SiteUserTable->getUserByLoginName($loginName);

            if ($user) {
                return true;
            }
            return false;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error("api.site.login", "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    private function clearLimitSession($userId, $deviceId)
    {

        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {//limit mobile
            $limitMobileNum = $this->siteConfig[SiteConfig::SITE_MOBILE_NUM];
            $sessionInfos = $this->ctx->SiteSessionTable->getUserSessionsByUserId($userId);
            if (count($sessionInfos) > $limitMobileNum) {
                $deleteDeviceIds = [];
                $num = 1;
                foreach ($sessionInfos as $sessionInfo) {
                    $delDeviceId = $sessionInfo['deviceId'];
                    if ($num >= $limitMobileNum) {
                        //需要删除的

                        if ($delDeviceId != $deviceId) {
                            $deleteDeviceIds[] = $delDeviceId;
                        }
                    } else {
                        //不需要删除
                        if ($delDeviceId != $deviceId) {
                            $num++;
                        }
                    }
                }

                $result = $this->ctx->SiteSessionTable->removeLimitSession($userId, $deleteDeviceIds);
                $this->logger->error($tag, "clear limit session result=" . $result);
            }
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }

    }

    private function getUserClient($devicePubkPem)
    {
        if (empty($devicePubkPem)) {
            return Zaly\Proto\Core\UserClientType::UserClientWeb;
        } else {
            return Zaly\Proto\Core\UserClientType::UserClientMobileApp;
        }
//        $userAgent = $this->getUserAgent();
//        error_log("=================client userAgent=" . $userAgent);
//        if (empty($userAgent) || strstr($userAgent, "DuckChat")) {
//            return Zaly\Proto\Core\UserClientType::UserClientMobileApp;
//        } else {
//            return Zaly\Proto\Core\UserClientType::UserClientWeb;
//        }
    }
}


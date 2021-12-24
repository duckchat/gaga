<?php
/**
 * 客户端获取站点相关配置.
 * User: childeYin<尹少爷>
 * Date: 13/07/2018
 * Time: 11:20 AM
 */

use Zaly\Proto\Core\TransportDataHeaderKey;
use Zaly\Proto\Site\ApiSiteConfigResponse;
use Zaly\Proto\Core\PublicSiteConfig;

class Api_Site_ConfigController extends \BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiSiteConfigRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiSiteConfigResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiSiteConfigRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;

        try {
            $requestHeader = $transportData->getHeader();
            $hostUrl = $requestHeader[TransportDataHeaderKey::HeaderHostUrl];

            $url = parse_url($hostUrl);

            if (empty($url)) {
                throw new Exception("api.site.config with no requestUrl in header");
            }

            $scheme = $url['scheme'];
            $host = isset($url['host']) ? $url['host'] : "";
            $port = isset($url['port']) ? $url['port'] : "";
            if (empty($host)) {
                throw new Exception("api.site.config with error url");
            }

            if (empty($host)) {
                throw new Exception("request config with no host");
            }

            $randomValue = $request->getRandom();

            $sessionId = $this->getSessionId($transportData);

            $isValid = $this->checkSessionValid($sessionId);

            $configData = $this->siteConfig;

            $randomBase64 = $this->buildRandomBase64($randomValue, $configData[SiteConfig::SITE_ID_PRIK_PEM]);

            $loginPluginProfile = $this->getPluginProfileFromDB($configData[SiteConfig::SITE_LOGIN_PLUGIN_ID]);

            $response = $this->buildSiteConfigResponse($scheme, $host, $port, $configData, $isValid, $randomBase64, $loginPluginProfile);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error=" . $ex);
            $this->setRpcError("error.alert", $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }

    }


    /**
     * @param $sessionId
     * @return bool
     */
    private function checkSessionValid($sessionId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $this->ctx->Wpf_Logger->error($tag, "check session id ");
        $requestTransportData = $this->requestTransportData;
        $headers = $requestTransportData->getHeader();

        if (!isset($headers[TransportDataHeaderKey::HeaderSessionid])) {
            return false;
        }

        $this->sessionId = $headers[TransportDataHeaderKey::HeaderSessionid];

        $sessionInfo = $this->ctx->SiteSessionTable->getSessionInfoBySessionId($this->sessionId);
        if (!$sessionInfo) {
            return false;
        }
        $timeActive = $sessionInfo['timeActive'];
        $nowTime = $this->ctx->ZalyHelper->getMsectime();

        if (($nowTime - $timeActive) > $this->sessionIdTimeOut) {
            $this->ctx->Wpf_Logger->error($tag, "session  time out  , session id = " . $sessionId);
            return false;
        }

        $this->userId = $sessionInfo['userId'];
        $this->deviceId = $sessionInfo['deviceId'];
        $this->userInfo = $this->ctx->SiteUserTable->getUserByUserId($this->userId);
        if (!$this->userInfo) {
            return false;
        }

        return true;
    }

    private function getPluginProfileFromDB($loginPluginId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $pluginProfile = $this->ctx->SitePluginTable->getPluginById($loginPluginId);
        $this->ctx->Wpf_Logger->info($tag, "pluginProfile=" . json_encode($pluginProfile));
        return $pluginProfile;
    }


    private function buildRandomBase64($random, $siteIdPrikBase64)
    {
        try {
            $signatureRandom = $this->ctx->ZalyRsa->sign($random, $siteIdPrikBase64);
            $base64Value = base64_encode($signatureRandom);
            return $base64Value;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->info("api.site.config", $e);
            throw $e;
        }
        return '';
    }

    /**
     * 生成 transData 数据
     * @param $scheme
     * @param $host
     * @param $port
     * @param $configData
     * @param bool $isValid
     * @param $randomBase64
     * @param $pluginProfile
     * @return ApiSiteConfigResponse
     * @throws Exception
     */
    private function buildSiteConfigResponse($scheme, $host, $port, $configData, $isValid, $randomBase64, $pluginProfile)
    {
        if (empty($scheme)) {
            $scheme = "http";
        }

        if (empty($port) && $scheme == "http") {
            $port = 80;
        } else if (empty($port) && $scheme == "https") {
            $port = 443;
        }

        ////ApiSiteConfigResponse 对象
        $response = new ApiSiteConfigResponse();

        try {
            $config = new PublicSiteConfig();
            $config->setName($configData[SiteConfig::SITE_NAME]);
            $config->setLogo($configData[SiteConfig::SITE_LOGO]);//        //notice

            if (isset($configData[SiteConfig::SITE_OWNER])) {

                $siteAdmins = [
                    $configData[SiteConfig::SITE_OWNER]
                ];

                $managersValueStr = $configData[SiteConfig::SITE_MANAGERS];

                if (isset($managersValueStr)) {
                    $managersArray = explode(",", $managersValueStr);
                    $siteAdmins = array_merge($siteAdmins, $managersArray);
                    $siteAdmins = array_unique($siteAdmins);
                    $siteAdmins = array_filter($siteAdmins);
                }
                $config->setMasters(json_encode($siteAdmins));
            }

            $zalyPort = isset($configData[SiteConfig::SITE_ZALY_PORT]) ? $configData[SiteConfig::SITE_ZALY_PORT] : "";
            $wsPort = isset($configData[SiteConfig::SITE_WS_PORT]) ? $configData[SiteConfig::SITE_WS_PORT] : "";
            $wsAddress = isset($configData[SiteConfig::SITE_WS_ADDRESS]) ? $configData[SiteConfig::SITE_WS_ADDRESS] : "";

            $addressForAPi = "";
            $addressForIM = "";
            if (!empty($zalyPort) && is_numeric($zalyPort) && $zalyPort > 0 && $zalyPort < 65535) {
                //support zaly protocol
                $addressForAPi = $this->buildAddress("zaly", $host, $zalyPort);
                $addressForIM = $this->buildAddress("zaly", $host, $zalyPort);
            } elseif (!empty($wsAddress)) {
                $addressForAPi = $this->buildAddress($scheme, $host, $port);
                $addressForIM = $wsAddress;
            } elseif (!empty($wsPort) && is_numeric($wsPort) && $wsPort > 0 && $wsPort < 65535) {
                //兼容旧的设计模式，使用zalyPort自动组装
                //support ws protocol
                $addressForAPi = $this->buildAddress($scheme, $host, $port);
                $addressForIM = $this->buildAddress("ws", $host, $wsPort);
            } else {
                //support http protocol
                $addressForAPi = $this->buildAddress($scheme, $host, $port);
                $addressForIM = $addressForAPi;
            }

//            $this->ctx->Wpf_Logger->error("api.site.config", "================addressForAPi=" . $addressForAPi);
//            $this->ctx->Wpf_Logger->error("api.site.config", "================addressForIM=" . $addressForIM);

            $config->setServerAddressForApi($addressForAPi);
            $config->setServerAddressForIM($addressForIM);

            $config->setLoginPluginId($configData[SiteConfig::SITE_LOGIN_PLUGIN_ID]);
            $config->setEnableCreateGroup($configData[SiteConfig::SITE_ENABLE_CREATE_GROUP]);
            $config->setEnableAddFriend($configData[SiteConfig::SITE_ENABLE_ADD_FRIEND]);
            $config->setEnableTmpChat($configData[SiteConfig::SITE_ENABLE_TMP_CHAT]);
            $config->setEnableInvitationCode($configData[SiteConfig::SITE_ENABLE_INVITATION_CODE]);
            $config->setEnableRealName($configData[SiteConfig::SITE_ENABLE_REAL_NAME]);
            $config->setEnableWidgetWeb($configData[SiteConfig::SITE_ENABLE_WEB_WIDGET]);
            $config->setSiteIdPubkBase64($configData[SiteConfig::SITE_ID_PUBK_PEM]);
            $config->setAccountSafePluginId($configData[SiteConfig::SITE_PASSPORT_ACCOUNT_SAFE_PLUGIN_ID]);

            if (isset($configData[SiteConfig::SITE_HIDDEN_HOME_PAGE])) {
                $config->setHiddenHomePage($configData[SiteConfig::SITE_HIDDEN_HOME_PAGE]);
            } else {
                $config->setHiddenHomePage(false);
            }

            if (!$config->getHiddenHomePage()) {//show home page
                if (isset($configData[SiteConfig::SITE_FRONT_PAGE])) {
                    $config->setFrontPage($configData[SiteConfig::SITE_FRONT_PAGE]);
                } else {
                    $config->setFrontPage(\Zaly\Proto\Core\FrontPage::FrontPageDefault);
                }
            } else {
                $frontPageValue = $configData[SiteConfig::SITE_FRONT_PAGE];
                if (isset($frontPageValue) && $frontPageValue != \Zaly\Proto\Core\FrontPage::FrontPageHome) {
                    $config->setFrontPage($configData[SiteConfig::SITE_FRONT_PAGE]);//不显示首页，一定显示第二页
                } else {
                    $config->setFrontPage(\Zaly\Proto\Core\FrontPage::FrontPageChats);//不显示首页，一定显示第二页
                }
            }

            if (isset($configData[SiteConfig::SITE_OPEN_WATERMARK])) {
                $config->setOpenWaterMark($configData[SiteConfig::SITE_OPEN_WATERMARK]);
            } else {
                $config->setOpenWaterMark(false);
            }

            $config->setVersion($this->getSiteVersion());
            $currentVersionCode = ZalyConfig::getConfig(ZalyConfig::$configSiteVersionCodeKey);
            $config->setVersionCode($currentVersionCode);

            $response->setConfig($config);

//            $this->ctx->Wpf_Logger->info("api.site.config", 'responseJson=' . $response->serializeToString());
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error("api.site.config", $e);
            throw new Exception('get site config profile error');
        }

        //login profile
        try {
            $loginPluginProfile = new Zaly\Proto\Core\PluginProfile();
            $loginPluginProfile->setId($pluginProfile['pluginId']);
            $loginPluginProfile->setLogo($pluginProfile['logo']);
            $loginPluginProfile->setName($pluginProfile['name']);
            $loginPluginProfile->setLandingPageUrl($pluginProfile['landingPageUrl']);
            $loginPluginProfile->setLandingPageWithProxy($pluginProfile['landingPageWithProxy']);
            $loginPluginProfile->setLoadingType($pluginProfile['loadingType']);
            $loginPluginProfile->setOrder($pluginProfile['sort']);
            $loginPluginProfile->setUsageTypes([$pluginProfile['usageType']]);
            $loginPluginProfile->setPermissionType($pluginProfile['permissionType']);
            $response->setLoginPluginProfile($loginPluginProfile);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->info("api.site.config.plugin", $e);
            throw new Exception('get config login plugin profile error');
        }

        $response->setRandomSignBase64($randomBase64);
        $response->setIsSessionValid($isValid);

        return $response;
    }

    private function buildAddress($scheme, $host, $port)
    {
        if ("http" == $scheme && $port == 80) {
            $requestUri = isset($_SERVER['REQUEST_URI']) ? str_replace(array("\\", "//"), array("/", "/"), $_SERVER['REQUEST_URI']) : "";
            $requestUris = explode("/", $requestUri);
            array_pop($requestUris);
            $requestUriPath = "";
            if (count($requestUris)) {
                $requestUriPath = implode("/", $requestUris);
            }
            return $scheme . "://" . "$host" . $requestUriPath;
        } elseif ("https" == $scheme && $port == 443) {
            return $scheme . "://" . "$host";
        }

        return $scheme . "://" . "$host" . ":" . $port;
    }

    private function getSiteVersion()
    {
        $versionName = ZalyConfig::getConfig(ZalyConfig::$configSiteVersionNameKey);

        $versionList = [];
        if (empty($versionName)) {
            $versionList = [0, 0, 0];
        } else {
            $versionList = explode(".", $versionName);
        }

        $version = new Zaly\Proto\Core\Version();
        $version->setFirst($versionList[0]);
        $version->setSecond($versionList[1]);
        $version->setThird($versionList[2]);

        return $version;
    }

}


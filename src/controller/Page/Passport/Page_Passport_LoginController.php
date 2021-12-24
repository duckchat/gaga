<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/08/2018
 * Time: 11:18 AM
 */

class Page_Passport_LoginController extends HttpBaseController
{

    public function index()
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;
        try {
            isset($_GET['token']) ? $this->checkUserToken($_GET['token']) : $this->checkUserCookie(); ;
            if ($this->userId) {
                $jumpPage = $this->getJumpUrlFromParams();
                $apiPageIndex = ZalyConfig::getApiIndexUrl();
                if ($jumpPage) {
                    if (strpos($apiPageIndex, "?")) {
                        $apiPageIndex .= "&" . $jumpPage;
                    } else {
                        header("Location:" . $apiPageIndex . "?" . $jumpPage);
                        $apiPageIndex .= "?" . $jumpPage;
                    }
                }
                header("Location:" . $apiPageIndex);
                exit();
            }
        } catch (Exception $ex) {
            $this->logger->error($tag, "page.passport.login error=" . $ex->getMessage());
        }

        $cookieStr = isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : "";
        $isDuckchat = 0;

        if (strpos($cookieStr, "duckchat_sessionid") !== false) {
            $isDuckchat = 1;
        }

        $siteLogo = $this->siteConfig[SiteConfig::SITE_LOGO];
        $siteName = $this->siteConfig[SiteConfig::SITE_NAME];

        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();

        $loginNameAliasConfig = isset($loginConfig[LoginConfig::LOGIN_NAME_ALIAS]) ? $loginConfig[LoginConfig::LOGIN_NAME_ALIAS] : "";
        $loginNameAlias = isset($loginNameAliasConfig["configValue"]) ? $loginNameAliasConfig["configValue"] : "";
        $passwordResetWayConfig = isset($loginConfig[LoginConfig::PASSWORD_RESET_WAY]) ? $loginConfig[LoginConfig::PASSWORD_RESET_WAY] : "";
        $passwordRestWay = isset($passwordResetWayConfig["configValue"]) ? $passwordResetWayConfig["configValue"] : "";

        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();
        $passwordResetRequiredConfig = isset($loginConfig[LoginConfig::PASSWORD_RESET_REQUIRED]) ? $loginConfig[LoginConfig::PASSWORD_RESET_REQUIRED] : "";
        $passwordResetRequired = isset($passwordResetRequiredConfig["configValue"]) ? $passwordResetRequiredConfig["configValue"] : "";

        $loginWelcomeTextConfig = isset($loginConfig[LoginConfig::LOGIN_PAGE_WELCOME_TEXT]) ? $loginConfig[LoginConfig::LOGIN_PAGE_WELCOME_TEXT] : "";
        $loginWelcomeText = isset($loginWelcomeTextConfig["configValue"]) ? $loginWelcomeTextConfig["configValue"] : "";

        $loginBackgroundColorConfig = isset($loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_COLOR]) ? $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_COLOR] : "";
        $loginBackgroundColor = isset($loginBackgroundColorConfig["configValue"]) ? $loginBackgroundColorConfig["configValue"] : "";

        $loginBackgroundImageConfig = isset($loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE]) ? $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE] : "";
        $loginBackgroundImage = isset($loginBackgroundImageConfig["configValue"]) ? $loginBackgroundImageConfig["configValue"] : "";

        $loginBackgroundImageDisplayConfig = isset($loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE_DISPLAY]) ? $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE_DISPLAY] : "";
        $loginBackgroundImageDisplay = isset($loginBackgroundImageDisplayConfig["configValue"]) ? $loginBackgroundImageDisplayConfig["configValue"] : "";

        $siteVersionName = ZalyConfig::getConfig(ZalyConfig::$configSiteVersionNameKey);
        $thirdPartyLoginOptions = ZalyLogin::getThirdPartyConfigWithoutVerifyUrl();

        $enableInvitationCode = $this->getSiteConfigFromDB(SiteConfig::SITE_ENABLE_INVITATION_CODE);
        $enableRealName = $this->getSiteConfigFromDB(SiteConfig::SITE_ENABLE_REAL_NAME);

        $title = ZalyText::getText("text.login", $this->language)."-".$siteName;

        $params = [
            'title' => $title,
            'siteLogo'   => $this->ctx->File_Manager->getCustomPathByFileId($siteLogo),
            'isDuckchat' => $isDuckchat,
            'siteVersionName'  => $siteVersionName,
            'loginWelcomeText' => $loginWelcomeText,
            'loginBackgroundColor' => $loginBackgroundColor,
            'loginBackgroundImage' => $this->ctx->File_Manager->getCustomPathByFileId($loginBackgroundImage),

            'loginBackgroundImageDisplay' => $loginBackgroundImageDisplay,

            'loginNameAlias'   => $loginNameAlias,
            'passwordFindWay'  => $passwordRestWay,
            'passwordResetWay' => $passwordRestWay,
            'passwordResetRequired'  => $passwordResetRequired,
            'thirdPartyLoginOptions' => $thirdPartyLoginOptions,

            'enableInvitationCode' => $enableInvitationCode,
            'enableRealName'       => $enableRealName,
        ];
        echo $this->display("passport_login", $params);
        return;
    }

}
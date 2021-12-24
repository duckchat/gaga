<?php
/**
 * passport_register on local site
 * User: anguoyue
 * Date: 2018/11/8
 * Time: 2:57 PM
 */

class Page_Passport_RegisterController extends HttpBaseController
{

    public function index()
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;
        try {
            isset($_GET['token']) ? $this->checkUserToken($_GET['token']) : $this->checkUserCookie();;
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
            $this->logger->error($tag, $ex);
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

        $nicknameRequiredConfig = isset($loginConfig[LoginConfig::NICK_NAME_REQUIRED]) ? $loginConfig[LoginConfig::NICK_NAME_REQUIRED] : [];
        $nicknameRequired = isset($nicknameRequiredConfig["configValue"]) ? $nicknameRequiredConfig["configValue"] : false;

        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();

        $loginWelcomeTextConfig = isset($loginConfig[LoginConfig::LOGIN_PAGE_WELCOME_TEXT]) ? $loginConfig[LoginConfig::LOGIN_PAGE_WELCOME_TEXT] : "";
        $loginWelcomeText = isset($loginWelcomeTextConfig["configValue"]) ? $loginWelcomeTextConfig["configValue"] : "";

        $loginBackgroundColorConfig = isset($loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_COLOR]) ? $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_COLOR] : "";
        $loginBackgroundColor = isset($loginBackgroundColorConfig["configValue"]) ? $loginBackgroundColorConfig["configValue"] : "";

        $loginBackgroundImageConfig = isset($loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE]) ? $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE] : "";
        $loginBackgroundImage = isset($loginBackgroundImageConfig["configValue"]) ? $loginBackgroundImageConfig["configValue"] : "";

        $loginBackgroundImageDisplayConfig = isset($loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE_DISPLAY]) ? $loginConfig[LoginConfig::LOGIN_PAGE_BACKGROUND_IMAGE_DISPLAY] : "";
        $loginBackgroundImageDisplay = isset($loginBackgroundImageDisplayConfig["configValue"]) ? $loginBackgroundImageDisplayConfig["configValue"] : "";

        $siteVersionName = ZalyConfig::getConfig(ZalyConfig::$configSiteVersionNameKey);

        $loginNameMinLengthConfig = isset($loginConfig[LoginConfig::LOGINNAME_MINLENGTH]) ? $loginConfig[LoginConfig::LOGINNAME_MINLENGTH] : "";
        $loginNameMinLength = isset($loginNameMinLengthConfig["configValue"]) ? $loginNameMinLengthConfig["configValue"] : 1;

        $loginNameMaxLengthConfig = isset($loginConfig[LoginConfig::LOGINNAME_MAXLENGTH]) ? $loginConfig[LoginConfig::LOGINNAME_MAXLENGTH] : "";
        $loginNameMaxLength = isset($loginNameMaxLengthConfig["configValue"]) ? $loginNameMaxLengthConfig["configValue"] : 24;

        $pwdMaxLengthConfig = isset($loginConfig[LoginConfig::PASSWORD_MAXLENGTH]) ? $loginConfig[LoginConfig::PASSWORD_MAXLENGTH] : "";
        $pwdMaxLength = isset($pwdMaxLengthConfig["configValue"]) ? $pwdMaxLengthConfig["configValue"] : 32;

        $pwdMinLengthConfig = isset($loginConfig[LoginConfig::PASSWORD_MINLENGTH]) ? $loginConfig[LoginConfig::PASSWORD_MINLENGTH] : "";
        $pwdMinLength = isset($pwdMinLengthConfig["configValue"]) ? $pwdMinLengthConfig["configValue"] : 6;

        $pwdContainCharactersConfig = isset($loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTERS]) ? $loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTERS] : "";
        $pwdContainCharacters = isset($pwdContainCharactersConfig["configValue"]) ? $pwdContainCharactersConfig["configValue"] : "";

        $enableInvitationCode = $this->getSiteConfigFromDB(SiteConfig::SITE_ENABLE_INVITATION_CODE);
        $enableRealName = $this->getSiteConfigFromDB(SiteConfig::SITE_ENABLE_REAL_NAME);


        $loginNameTip = ZalyText::getText("text.length", $this->language) . " " . $loginNameMinLength . "-" . $loginNameMaxLength;
        $pwdTip = ZalyText::getText("text.length", $this->language) . " " . $pwdMinLength . "-" . $pwdMaxLength;
        if ($pwdContainCharacters) {
            $pwdTip = $pwdContainCharacters . "," . ZalyText::getText("text.length", $this->language) . " " . $pwdMinLength . "-" . $pwdMaxLength;
        }
        $pwdTip = str_replace(["letter", "number", "special_characters"], ["字母", "数字", "特殊字符"], $pwdTip);
        $title = ZalyText::getText("text.register", $this->language) . "-" . $siteName;

        $registerCustoms = $this->getRegisterCustoms();
        $params = [
            'title' => $title,
            'siteLogo' => $this->ctx->File_Manager->getCustomPathByFileId($siteLogo),
            'siteVersionName' => $siteVersionName,
            'isDuckchat' => $isDuckchat,
            'loginWelcomeText' => $loginWelcomeText,
            'loginBackgroundColor' => $loginBackgroundColor,
            'loginBackgroundImage' => $this->ctx->File_Manager->getCustomPathByFileId($loginBackgroundImage),

            "pwdMaxLength" => $pwdMaxLength,
            "pwdMinLength" => $pwdMinLength,
            "loginNameMinLength" => $loginNameMinLength,
            "loginNameMaxLength" => $loginNameMaxLength,
            "pwdContainCharacters" => $pwdContainCharacters,
            "loginNameTip" => $loginNameTip,
            "pwdTip" => $pwdTip,

            'loginBackgroundImageDisplay' => $loginBackgroundImageDisplay,

            'loginNameAlias' => $loginNameAlias,
            'enableInvitationCode' => $enableInvitationCode,
            'enableRealName' => $enableRealName,
            'nicknameRequired' => $nicknameRequired,
            'registerCustoms' => $registerCustoms
        ];

        echo $this->display("passport_register", $params);
        return;
    }

    //获取注册
    private function getRegisterCustoms()
    {
        $registerCustoms = $this->ctx->SiteUserCustomTable->getColumnInfosForRegister();
        foreach ($registerCustoms as $key => $custom) {
            if (isset($custom['keyIcon'])) {
                $custom['keyIcon'] = $this->ctx->File_Manager->getCustomPathByFileId($custom['keyIcon']);
            }
            $registerCustoms[$key] = $custom;
        }
        return $registerCustoms;
    }
}
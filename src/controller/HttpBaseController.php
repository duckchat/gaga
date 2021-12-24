<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 13/07/2018
 * Time: 6:32 PM
 */

use Google\Protobuf\Any;
use Zaly\Proto\Core\TransportData;
use Zaly\Proto\Core\TransportDataHeaderKey;
use Google\Protobuf\Internal\Message;

abstract class HttpBaseController extends \Wpf_Controller
{
    protected $logger;
    protected $userId;
    protected $userInfo;
    protected $sessionId;
    public $defaultErrorCode = "success";
    public $errorCode = "fail";
    protected $sessionIdTimeOut = 3600000; //1个小时的毫秒
    public $defaultFilePath = "files";
    public $whiteAction = [
        "page.login",
        "page.js",
        "page.siteConfig",
        "page.passport.login",
        "page.passport.register",
        "page.jump",
        "page.version.check",
    ];
    public $upgradeAction = [
        'page.version.check',
        'page.version.password',
        'page.version.upgrade',
    ];
    private $groupType = "groupMsg";
    private $u2Type = "u2Msg";
    private $jumpRoomType = "";
    private $jumpRoomId = "";
    private $jumpRelation = "";
    public $siteCookieName = "zaly_site_user";
    public $language = "";
    private $cookieTimeOut = 2592000;//30天 单位s

    protected $siteConfig;

    protected $ctx;

    public function __construct(BaseCtx $context)
    {
        if (!$this->checkDBIsExist()) {
            $initUrl = ZalyConfig::getConfig("apiPageSiteInit");
            header("Location:" . $initUrl);
            exit();
        }
        $this->logger = $context->getLogger();
        $this->ctx = $context;

    }

    abstract public function index();

    /**
     * 处理方法， 根据bodyFormatType, 获取transData
     * @return string|void
     */
    public function doIndex()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        try {
            parent::doIndex();

            $action = isset($_GET['action']) ? $_GET['action'] : "";

            $this->getAndSetClientLang();
            if (!in_array($action, $this->upgradeAction)) {
                $this->checkIsNeedUpgrade();
            }

            $this->siteConfig = $this->ctx->Site_Config->getAllConfig();

            if (!in_array($action, $this->whiteAction)) {
                $flag = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_OPEN_WEB_EDITION);
                if ($flag != 1) {
                    echo ZalyText::getText("text.open.web", $this->language);
                    die();
                }
                $this->getUserIdByCookie();
            }

            $this->getJumpUrl();
            $this->index();
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error msg =" . $ex->getMessage());
            $this->setLogout();
        }
    }

    //TODO check need upgrade
    protected function checkIsNeedUpgrade()
    {
        $sampleFileName = dirname(__FILE__) . "/../config.sample.php";
        $sampleConfig = require($sampleFileName);
        $sampleVersionCode = isset($sampleConfig['siteVersionCode']) ? $sampleConfig['siteVersionCode'] : 0;
        $configVersionCode = ZalyConfig::getConfig('siteVersionCode');
        if ($sampleVersionCode > $configVersionCode) {
            $upgradeUrl = './index.php?action=page.version.check';
            header("Location:" . $upgradeUrl);
            exit;
        }
    }

    protected function getAndSetClientLang()
    {
        $headLang = isset($_GET['lang']) ? $_GET['lang'] : "";
        if (isset($headLang) && $headLang == Zaly\Proto\Core\UserClientLangType::UserClientLangZH) {
            $this->language = Zaly\Proto\Core\UserClientLangType::UserClientLangZH;
        } else {
            $this->language = Zaly\Proto\Core\UserClientLangType::UserClientLangEN;
        }
    }

    public function getJumpUrl()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        try {
            $x = isset($_GET['x']) ? $_GET['x'] : "";
            $page = isset($_GET['page']) ? $_GET['page'] : "";
            if (!$page) {
                return;
            }
            if ($x == $this->userId) {
                return;
            }
            if ($page == $this->groupType) {
                $isInGroupFlag = $this->ctx->SiteGroupTable->getGroupProfile($x, $this->userId);
                $this->jumpRelation = $isInGroupFlag != false ? 1 : 0;
            } elseif ($page == $this->u2Type) {
                $this->jumpRelation = 0;
            }
            $this->jumpRoomType = $page;
            $this->jumpRoomId = $x;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error msg =" . $ex->getMessage());
        }
    }

    public function getUserIdByCookie()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        try {
            $this->checkUserCookie();
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error msg =" . $ex->getMessage());
            $this->setLogout();
        }
    }

    public function checkUserCookie()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $this->sessionId = isset($_COOKIE[$this->siteCookieName]) ? $_COOKIE[$this->siteCookieName] : "";
        if (!$this->sessionId) {
            throw new Exception("cookie is not found");
        }

        $this->sessionInfo = $this->ctx->SiteSessionTable->getSessionInfoBySessionId($this->sessionId);
        if (!$this->sessionInfo) {
            throw new Exception("session is not ok");
        }
        $timeActive = $this->sessionInfo['timeActive'];

        $nowTime = $this->ctx->ZalyHelper->getMsectime();

        if (($nowTime - $timeActive) > $this->sessionIdTimeOut * 24 * 365) {
            throw new Exception("session expired");
        }

        $this->userInfo = $this->ctx->SiteUserTable->getUserByUserId($this->sessionInfo['userId']);
        if (!$this->userInfo) {
            throw new Exception("user is not ok");
        }

        $this->sessionId = $this->sessionInfo['sessionId'];
        $this->userId = $this->userInfo['userId'];

    }

    protected function checkUserToken($token)
    {
        $this->sessionInfo = $this->ctx->SiteSessionTable->getSessionInfoBySessionId($token);
        if (!$this->sessionInfo) {
            throw new Exception("session is not ok");
        }
        $timeActive = $this->sessionInfo['timeActive'];

        $nowTime = $this->ctx->ZalyHelper->getMsectime();

        if (($nowTime - $timeActive) > $this->sessionIdTimeOut * 24 * 365) {
            throw new Exception("session expired");
        }

        $this->userInfo = $this->ctx->SiteUserTable->getUserByUserId($this->sessionInfo['userId']);
        if (!$this->userInfo) {
            throw new Exception("user is not ok");
        }
        setcookie($this->siteCookieName, $token, time() + $this->cookieTimeOut, "/", "", false, true);
        $this->sessionId = $this->sessionInfo['sessionId'];
        $this->userId = $this->userInfo['userId'];
    }

    public function setLogout()
    {
        $jumpPage = $this->getJumpUrlFromParams();
        setcookie($this->siteCookieName, "", time() - 3600, "/", "", false, true);
//        $apiPageLogin = ZalyConfig::getConfig("apiPageLogin");
        $apiPageLogin = "./index.php?action=page.passport.login";

        if ($jumpPage) {
            if (strpos($apiPageLogin, "?")) {
                header("Location:" . $apiPageLogin . "&" . $jumpPage);
            } else {
                header("Location:" . $apiPageLogin . "?" . $jumpPage);
            }
        } else {
            if (strpos($apiPageLogin, "?")) {
                header("Location:" . $apiPageLogin);
            } else {
                header("Location:" . $apiPageLogin);
            }
        }
        exit();
    }

    public function getJumpUrlFromParams()
    {
        $x = isset($_GET['x']) ? $_GET['x'] : "";
        $page = isset($_GET['page']) ? $_GET['page'] : "";
        $jumpPage = "";
        if ($page) {
            $jumpPage = "page=" . $page . "&x=" . $x;
        }
        return $jumpPage;
    }

    public function setTransDataHeaders($key, $val)
    {
        $key = "_{$key}";
        $this->headers[$key] = $val;
    }

    public function setHeader()
    {
        $this->setTransDataHeaders(TransportDataHeaderKey::HeaderSessionid, $this->sessionId);
        $this->setTransDataHeaders(TransportDataHeaderKey::HeaderUserAgent, $_SERVER['HTTP_USER_AGENT']);
    }

    public function display($viewName, $params = [])
    {
        try {
            $siteName = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_NAME);
        } catch (Exception $ex) {
            $siteName = "";
        }
        // 自己实现实现一下这个方法，加载view目录下的文件
        $params['session_id'] = $this->sessionId;
        $params['user_id'] = $this->userId;
        $params['nickname'] = $this->userInfo['nickname'] ? $this->userInfo['nickname'] : "匿名";
        $params['loginName'] = ZalyHelper::hideMobile($this->userInfo['loginName']);
        $params['avatar'] = $this->userInfo['avatar'];
        $params['jumpPage'] = ZalyConfig::getApiPageJumpUrl();
        if (!isset($params['login'])) {
            $params['login'] = '';
        }
        $params['jumpRoomId'] = $this->jumpRoomId;
        $params['jumpRoomType'] = $this->jumpRoomType;
        $params['jumpRelation'] = $this->jumpRelation;
        $params['versionCode'] = ZalyConfig::getConfig("siteVersionCode");
        $params['siteName'] = $siteName;
        $params['siteAddress'] = ZalyHelper::getRequestAddressPath();
        return parent::display($viewName, $params);
    }

    /**
     * 查库操作
     * @param $columns
     * @return array
     */
    public function getSiteConfigFromDB($columns)
    {
        try {
            $results = $this->ctx->Site_Config->getConfigValue($columns);
            return $results;
        } catch (Exception $e) {
            $tag = __CLASS__ . "-" . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, " errorMsg = " . $e->getMessage());
            return [];
        }
    }
}
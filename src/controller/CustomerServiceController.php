<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/11/2018
 * Time: 10:27 AM
 */

abstract  class CustomerServiceController extends \Wpf_Controller
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

    ];

    public $siteCookieName = "duckchat_service_cookie";
    public $language = "";
    private $cookieTimeOut = 2592000;//30天 单位s

    protected $siteConfig;

    protected $ctx;

    public function __construct(BaseCtx $context)
    {
        if (!$this->checkDBIsExist()) {
            header('HTTP/1.0 404 Not Found');
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
            $this->getAndSetClientLang();
            $this->index();
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error msg =" . $ex->getMessage());
            $this->setLogout();
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


    public function getUserIdByServiceCookie()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        try {
            $this->checkUserServiceCookie();
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error msg =" . $ex->getMessage());
        }
    }

    public function checkUserServiceCookie()
    {
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
        $params['sessionId'] = $this->sessionId;
        $params['userId'] = $this->userId;
        $params['versionCode'] = ZalyConfig::getConfig("siteVersionCode");
        $params['siteName'] = $siteName;
        $params['siteAddress'] = ZalyHelper::getRequestAddressPath();
        return parent::display($viewName, $params);
    }

}
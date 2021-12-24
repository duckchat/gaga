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

abstract class BaseController extends \Wpf_Controller
{
    protected $logger;

    protected $httpHeader = ["KeepSocket" => true];
    protected $headers = [];
    protected $bodyFormatType;
    protected $bodyFormatArr = [
        "json",
        "pb",
        "base64pb"
    ];
    protected $defaultBodyFormat = "json";
    protected $action = '';
    protected $requestTransportData;
    private $whiteAction = [
        "api.site.config",
        "api.site.login",
        "im.cts.ping",
        "api.passport.passwordReg",
        "api.passport.passwordLogin",
        "api.session.verify",
        "api.passport.passwordFindPassword",
        "api.passport.passwordResetPassword",
        "api.passport.passwordUpdateInvitationCode",
        "api.passport.passwordModifyPassword",
        "api.plugin.proxy",
    ];
    protected $sessionIdTimeOut = 3600000; //一个小时
    protected $userId;
    protected $sessionId;
    protected $deviceId;
    protected $userInfo;
    public $defaultErrorCode = "success";
    public $defaultPageSize = 200;
    public $defaultPage = 1;
    public $errorSiteInit = "error.site.init";

    protected $siteConfig;

    protected $language = Zaly\Proto\Core\UserClientLangType::UserClientLangEN;

    protected $clientType;

    /**
     * @var BaseCtx
     */
    protected $ctx;

    // return the name for parse json to Any.
    abstract public function rpcRequestClassName();

    // waiting for son~
    abstract public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData);

    /**
     * 设置transData header
     * @param $key
     * @param $val
     */
    public function setTransDataHeaders($key, $val)
    {
        $key = "_{$key}";
        $this->headers[$key] = $val;
    }

    public function keepSocket()
    {
        header("KeepSocket: true");
    }

    //set errCode && errInfo by error
    public function setZalyError($error)
    {
        $errCode = ZalyError::getErrCode($error);
        $errInfo = ZalyError::getErrorInfo($error, $this->language);
        $this->setRpcError($errCode, $errInfo);
    }

    //set errCode && errInfo by error && throw exception
    public function throwZalyException($error)
    {
        $errCode = ZalyError::getErrCode($error);
        $errInfo = ZalyError::getErrorInfo($error, $this->language);
        $this->setRpcError($errCode, $errInfo);
        throw new ZalyException($errCode, $errInfo);
    }

    //only set errCode ,errInfo
    public function setRpcError($errorCode, $errorInfo)
    {
        $this->setTransDataHeaders(TransportDataHeaderKey::HeaderErrorCode, $errorCode);
        $this->setTransDataHeaders(TransportDataHeaderKey::HeaderErrorInfo, $errorInfo);
    }

    public function getRpcError()
    {
        return isset($this->headers[TransportDataHeaderKey::HeaderErrorCode]) ? $this->headers[TransportDataHeaderKey::HeaderErrorCode] : false;
    }

    public function getRequestAction()
    {
        return $this->action;
    }

    /**
     * 返回需要格式的数据
     * @param $action
     * @param \Google\Protobuf\Internal\Message $response
     */
    public function rpcReturn($action, $response)
    {
        $transData = new TransportData();
        $transData->setAction($action);

        if (null != $response) {
            $anyBody = new Any();
            $anyBody->pack($response);
            $transData->setBody($anyBody);
        }

        $transData->setHeader($this->headers);
        $transData->setPackageId($this->requestTransportData->getPackageId());
        $body = "";
        if ("json" == $this->bodyFormatType) {
            $body = $transData->serializeToJsonString();
            $body = trim($body);
        } elseif ("pb" == $this->bodyFormatType) {
            $body = $transData->serializeToString();
        } elseif ("base64pb" == $this->bodyFormatType) {
            $body = $transData->serializeToString();
            $body = base64_encode($body);
        } else {
            return;
        }
        echo $body;
        return;
    }

    //rpc response return success
    public function returnSuccessRPC($response)
    {
        $this->setRpcError($this->defaultErrorCode, "");
        $this->rpcReturn($this->action, $response);
    }

    //return rpc exception
    public function returnErrorRPC($response, $e)
    {
        $this->setRpcError("error.alert", $e->getMessage());
        $this->rpcReturn($this->action, $response);
    }

    public function returnErrorCodeRPC($errCode, $errInfo, $response = null)
    {
        $this->setRpcError($errCode, $errInfo);
        $this->rpcReturn($this->action, $response);
    }

    // ignore.~
    public function __construct(Wpf_Ctx $context)
    {
        if (!$this->checkDBIsExist()) {
            $this->action = $_GET['action'];
            $this->requestTransportData = new \Zaly\Proto\Core\TransportData();
            $pageSiteInit = ZalyHelper::getFullReqUrl(ZalyConfig::getConfig("apiPageSiteInit"));
            $this->setRpcError($this->errorSiteInit, $pageSiteInit);
            $this->bodyFormatType = isset($_GET['body_format']) ? $_GET['body_format'] : "";
            $this->bodyFormatType = strtolower($this->bodyFormatType);
            if (!in_array($this->bodyFormatType, $this->bodyFormatArr)) {
                $this->bodyFormatType = $this->defaultBodyFormat;
            }
            $this->rpcReturn($this->action, null);
            exit();
        }

        $this->ctx = new BaseCtx();
        $this->logger = new Wpf_Logger();
    }

    /**
     * 处理方法， 根据bodyFormatType, 获取transData
     * @return string|void
     */
    public function doIndex()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        parent::doIndex();

        $this->ctx = new BaseCtx();

        // 判断请求格式 json， pb, pb64
        // body_format 只从$_GET中接收
        $this->action = $_GET['action'];
//        $this->checkDBCanWrite();

        $this->bodyFormatType = isset($_GET['body_format']) ? $_GET['body_format'] : "";
        $this->bodyFormatType = strtolower($this->bodyFormatType);

        if (!in_array($this->bodyFormatType, $this->bodyFormatArr)) {
            $this->bodyFormatType = $this->defaultBodyFormat;
        }

        // 接收的数据流
        $reqData = file_get_contents("php://input");


        // 将数据转为TransportData
        $this->requestTransportData = new \Zaly\Proto\Core\TransportData();

        if (!ZalyHelper::checkOpensslEncryptExists()) {
            $this->ctx->Wpf_Logger->error($tag, "none has openssl function exists");
            // disabled the rpcReturn online.
            $this->setRpcError("error.proto.parse", "openssl_encrypt is not exists");
            $this->rpcReturn($this->action, null);
            die();
        }

        ////判断 request proto 类 是否存在。
        $requestClassName = $this->rpcRequestClassName();
        if (class_exists($requestClassName, true)) {
            $usefulForProtobufAnyParse = new $requestClassName();
        } else {
            trigger_error("no request proto class: " . $requestClassName, E_USER_ERROR);
            die();
        }
        try {

            if ("json" == $this->bodyFormatType) {
                if (empty($reqData)) {
                    $reqData = "{}";
                }
                $this->requestTransportData->mergeFromJsonString($reqData);
            } elseif ("pb" == $this->bodyFormatType) {
                $this->requestTransportData->mergeFromString($reqData);
            } elseif ("base64pb" == $this->bodyFormatType) {
                $realData = base64_decode($reqData);
                $this->requestTransportData->mergeFromString($realData);
            }

        } catch (Exception $e) {
            $error = sprintf("parse proto error, format: %s, error: %s", $this->bodyFormatType, $e->getMessage());
            $this->ctx->Wpf_Logger->error($tag, $error);
            // disabled the rpcReturn online.
            $this->setRpcError("error.proto.parse", $error);
            $this->rpcReturn($this->action, null);
            die();
        }
        $requestMessage = $usefulForProtobufAnyParse;
        ////解析请求数据，
        ///
        if (null !== $this->requestTransportData->getBody()) {
            $requestMessage = $this->requestTransportData->getBody()->unpack();
        }

        // $this->ctx->Wpf_Logger->error($tag, "request  packageId =" . $this->requestTransportData->getPackageId());
        $this->handleHeader();

        $this->getAndSetClientLang();

        $this->checkSessionId($this->action);
        $this->rpc($requestMessage, $this->requestTransportData);
    }

    private function handleHeader()
    {
        $headers = $this->requestTransportData->getHeader();

        foreach ($headers as $key => $val) {
            $key = str_replace("_", "", $key);
            $headers[$key] = $val;
        }
        $this->requestTransportData->setHeader($headers);
    }

    /**
     * @param Message $transportData
     * @return string
     */
    public function getSessionId(\Google\Protobuf\Internal\Message $transportData)
    {
        $header = $transportData->getHeader();
        $sessionId = $header[TransportDataHeaderKey::HeaderSessionid];
        return $sessionId;
    }

    public function checkSessionId($action)
    {
        $this->siteConfig = $this->ctx->Site_Config->getAllConfig();

        if (empty($this->siteConfig)) {
            $this->returnErrorSession("site config is empty");
        }

        $tag = __CLASS__ . "-" . __FUNCTION__;
        $requestTransportData = $this->requestTransportData;
        $headers = $requestTransportData->getHeader();
        if (in_array($action, $this->whiteAction)) {
            return;
        }

        if (!isset($headers[TransportDataHeaderKey::HeaderSessionid])) {
            $this->returnErrorSession();
        }

        $this->sessionId = $headers[TransportDataHeaderKey::HeaderSessionid];

        //check session by sessionId
        $sessionInfo = $this->ctx->SiteSessionTable->getSessionInfoBySessionId($this->sessionId);

        if (empty($sessionInfo) || !$this->checkSessionByTime($sessionInfo)) {
            $this->returnErrorSession();
        }

        $this->userId = $sessionInfo['userId'];
        $this->deviceId = $sessionInfo['deviceId'];
        $this->userInfo = $this->ctx->SiteUserTable->getUserByUserId($this->userId);
        if (empty($this->userInfo)) {
            $this->returnErrorSession();
        }

        //update session
        $this->ctx->SiteSessionTable->updateSessionActive($this->sessionId);
    }

    private function checkSessionByTime($sessionInfo)
    {
        //check session by session time
        $currentTime = $this->ctx->ZalyHelper->getMsectime();
        $timeActive = $sessionInfo['timeActive'];

        if (($currentTime - $timeActive) < $this->sessionIdTimeOut * 24 * 365) {
            return true;
        }
        return false;
    }

    public function getUserAgent()
    {
        $requestHeader = $this->requestTransportData->getHeader();
        $userAgent = $requestHeader[TransportDataHeaderKey::HeaderUserAgent];
        return $userAgent;
    }

    public function getPublicUserProfile($userInfo)
    {
        try {
            $publicUserProfile = new \Zaly\Proto\Core\PublicUserProfile();
            $avatar = isset($userInfo['avatar']) ? $userInfo['avatar'] : "";
            $publicUserProfile->setAvatar($avatar);
            $publicUserProfile->setUserId($userInfo['userId']);
            $publicUserProfile->setLoginname($userInfo['loginName']);
            $publicUserProfile->setNickname($userInfo['nickname']);
            $publicUserProfile->setNicknameInLatin($userInfo['nicknameInLatin']);

            if (isset($userInfo['availableType'])) {
                $publicUserProfile->setAvailableType($userInfo['availableType']);
            } else {
                $publicUserProfile->setAvailableType(\Zaly\Proto\Core\UserAvailableType::UserAvailableNormal);
            }
            return $publicUserProfile;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error("get public user profile", $ex);
            $publicUserProfile = new \Zaly\Proto\Core\PublicUserProfile();
            return $publicUserProfile;
        }
    }

    public function getGroupMemberUserProfile($user)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $this->ctx->Wpf_Logger->info($tag, "user userMemberType  = " . $user['memberType']);

        $publicUserProfile = $this->getPublicUserProfile($user);

        $groupMemberUserProfile = new \Zaly\Proto\Site\ApiGroupMembersUserProfile();
        $groupMemberUserProfile->setProfile($publicUserProfile);

        $groupMemberUserProfile->setType((int)$user['memberType']);

        return $groupMemberUserProfile;
    }

    protected function finish_request()
    {
        if (!function_exists("fastcgi_finish_request")) {
            function fastcgi_finish_request()
            {
            }
        }
        fastcgi_finish_request();
    }

    protected function getAndSetClientLang()
    {
        $requestTransportData = $this->requestTransportData;
        $headers = $requestTransportData->getHeader();

        $headLang = isset($headers[TransportDataHeaderKey::HeaderUserClientLang]) ? $headers[TransportDataHeaderKey::HeaderUserClientLang] : "";

        if (isset($headLang) && $headLang == Zaly\Proto\Core\UserClientLangType::UserClientLangZH) {
            $this->language = Zaly\Proto\Core\UserClientLangType::UserClientLangZH;
            $this->zalyError = $this->ctx->ZalyErrorZh;

        } else {
            $this->language = Zaly\Proto\Core\UserClientLangType::UserClientLangEN;
            $this->zalyError = $this->ctx->ZalyErrorEn;
        }
    }

    private function returnErrorSession($errorInfo = false)
    {
        $errorCode = $this->zalyError->errorSession;
        if (empty($errorInfo)) {
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
        }
        $this->setRpcError($errorCode, $errorInfo);
        $this->rpcReturn($this->action, null);
        die();
    }
}
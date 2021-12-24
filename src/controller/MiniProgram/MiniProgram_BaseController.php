<?php
/**
 * 小程序基础类
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:58 AM
 */

abstract class MiniProgram_BaseController extends \Wpf_Controller
{
    protected $logger;

    protected $action;
    /**
     * @var \Zaly\Proto\Core\PublicUserProfile
     */
    protected $userProfile;
    protected $userId;
    protected $loginName;

    protected $success = "success";
    protected $error = "error.alert";

    protected $ctx;

    protected $language = Zaly\Proto\Core\UserClientLangType::UserClientLangZH;
    protected $requestData;
    protected $whiteAction = [
        "miniProgram.gif.info"
    ];


    public function __construct(Wpf_Ctx $context)
    {
        $this->ctx = new BaseCtx();
        $this->logger = new Wpf_Logger();
    }


    protected abstract function getMiniProgramId();

    //for permission

    /**
     * 在处理正式请求之前，预处理一些操作，比如权限校验
     * @return bool
     */
    protected abstract function preRequest();

    /**
     * 处理正式的请求逻辑，比如跳转界面，post获取信息等
     */
    protected abstract function doRequest();

    /**
     * preRequest && doRequest 发生异常情况，执行
     * @param $ex
     * @return mixed
     */
    protected abstract function requestException($ex);

    /**
     * 根据http request cookie中的duckchat_sessionId 做权限判断
     * @return string|void
     */
    public function doIndex()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        try {
            parent::doIndex();

            // 接收的数据流
            $this->requestData = file_get_contents("php://input");

            $this->logger->info("site.manage.base", "cookie=" . json_encode($_COOKIE));

            $action = $_GET['action'];
            $this->action = $action;
            if (!in_array($action, $this->whiteAction)) {
                $duckchatSessionId = $_COOKIE["duckchat_sessionid"];

                if (empty($duckchatSessionId)) {
                    throw new Exception("duckchat_sessionid is empty in cookie");
                }
                $miniProgramId = $this->getMiniProgramId();
                //get user profile from duckchat_sessionid
                $userPublicProfile = $this->getDuckChatUserProfileFromSessionId($duckchatSessionId, $miniProgramId);

                if (empty($userPublicProfile) || empty($userPublicProfile->getUserId())) {
                    throw new Exception("get empty user profile by duckchat_sessionid error");
                }

                $this->userProfile = $userPublicProfile;
                $this->userId = $userPublicProfile->getUserId();
                $this->loginName = $userPublicProfile->getLoginName();
                $this->ctx->Wpf_Logger->info("", "Mini Program Request UserId=" . $this->userId);

            }

            $this->getAndSetClientLang();

            $this->preRequest();
            $this->doRequest();
        } catch (Exception $ex) {
            echo $ex->getMessage();
            $this->ctx->Wpf_Logger->error($tag, "error msg =" . $ex);
            $this->requestException($ex);
        }

    }

    /**
     * @param $duckchatSessionId
     * @return \Zaly\Proto\Core\PublicUserProfile
     * @throws Exception
     */
    public function getDuckChatUserProfileFromSessionId($duckchatSessionId, $miniProgramId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        try {

            $action = "duckchat.session.profile";
            $requestData = new Zaly\Proto\Plugin\DuckChatSessionProfileRequest();
            $requestData->setEncryptedSessionId($duckchatSessionId);

            $response = $this->ctx->DuckChat_Client->doRequest($miniProgramId, $action, $requestData);

            if (empty($response)) {
                throw new Exception("get empty response by duckchat_sessionid error");
            }
            $userProfile = $response->getProfile();
            return $userProfile->getPublic();
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error msg =" . $ex);
            throw $ex;
        }
    }


    /**
     * 通用本地小程序，发送请求调用 DuckChat inner api
     * @param $miniProgramId
     * @param $action
     * @param $requestProtoData
     * @return \Google\Protobuf\unpacked
     * @throws Exception
     */
    public function requestDuckChatInnerApi($miniProgramId, $action, $requestProtoData)
    {
        $response = $this->ctx->DuckChat_Client->doRequest($miniProgramId, $action, $requestProtoData);
        return $response;
    }

    private function getHeaderValue($header, $key)
    {
        if (empty($header)) {

        }
        return $header['_' . $key];
    }

    protected function showPermissionPage()
    {
//        $apiPageLogin = ZalyConfig::getConfig("apiPageLogin");
//        header("Location:" . "http://www.akaxin.com/");
        exit();
    }

    protected function getAndSetClientLang()
    {
        $headLang = isset($_GET['lang']) ? $_GET['lang'] : "";

        if (isset($headLang) && $headLang == Zaly\Proto\Core\UserClientLangType::UserClientLangZH) {
            $this->language = Zaly\Proto\Core\UserClientLangType::UserClientLangZH;
            $this->zalyError = $this->ctx->ZalyErrorZh;
        } else {
            $this->language = Zaly\Proto\Core\UserClientLangType::UserClientLangEN;
            $this->zalyError = $this->ctx->ZalyErrorEn;
        }
    }

    protected function getCurrentTimeMills()
    {
        return ZalyHelper::getMsectime();
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

    protected function getLanguageText($zhText, $enText)
    {
        return $this->language == Zaly\Proto\Core\UserClientLangType::UserClientLangZH ? $zhText : $enText;
    }

}
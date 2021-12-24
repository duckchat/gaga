<?php
/**
 *
 * User: anguoyue
 * Date: 2018/10/26
 * Time: 12:29 PM
 */

class DC_Demo_Controller
{

    private $dcApi;

    function __construct()
    {
        $serverAddress = "http://192.168.3.4:8888"; //搭建的DuckChat服务器的地址
        $miniProgramId = 100;   //小程序的Id
        $secretKey = "XXXXXXXXX";   //小程序的密钥
        $this->dcApi = new DC_Open_Api($serverAddress, $miniProgramId, $secretKey);
    }

    public function index()
    {

        if (file_exists('/../class.dc_demo_controller')) {
            require_once '/../class.dc_demo_controller';
        } else {
            $this->server_error("can't find sdk file");
        }


        $classMethod = $_GET['class_method'];

        if ($classMethod) {
            $classMethod = "index";
        }

        $methodName = 'dc_' . $classMethod;

        if (!method_exists($this, $methodName)) {
            throw new Exception("request not class method");
        }

        $dcSessionId = $_COOKIE["duckchat_sessionid"];


        //格式为gotoUrl格式
        $dcPageUrl = $_COOKIE["duckchat_pageurl"];


        if ($dcPageUrl == "u2Msg") {
            //二人消息
        } elseif ($dcPageUrl == "groupMsg") {
            //群组消息
        }

//        call_user_func(array($this, $methodName));

        call_user_func($methodName, $dcSessionId);
    }


    protected function dc_index($dcSessionId)
    {
        echo "test index()";
    }

    protected function dc_sessionProfile($dcSessionId)
    {

        $profile = $this->dcApi->getSessionProfile($dcSessionId);


    }

    protected function dc_userProfile($dcSessionId)
    {
        //小程序内部业务获取
        $userId = "";

        $profile = $this->dcApi->getUserProfile($userId);

    }

    protected function dc_userRelation($dcSessionId)
    {
        //小程序内部业务获取
        $userId = "";
        $oppositeUserId = "";

        $relation = $this->dcApi->getUserRelation($userId, $oppositeUserId);

    }

    protected function dc_sendMessage_Text($dcSessionId)
    {
        $fromUserId = "";
        $toUserId = "";

        $textBody = "test u2 text message";
        //发送二人消息
        $this->dcApi->sendTextMessage(false, $fromUserId, $toUserId, $textBody);

        $toGroupId = "";
        $textBody = "test group text message";
        //发送群组消息
        $this->dcApi->sendTextMessage(true, $fromUserId, $toGroupId, $textBody);
    }

    protected function dc_sendMessage_Notice($dcSessionId)
    {
        $fromUserId = "";
        $toUserId = "";

        $noticeBody = "test u2 notice message";

        $this->dcApi->sendNoticeMessage(false, $fromUserId, $toUserId, $noticeBody);

        $toGroupId = "";
        $noticeBody = "test group notice message";
        //发送群组消息
        $this->dcApi->sendNoticeMessage(true, $fromUserId, $toGroupId, $noticeBody);

    }

    protected function dc_sendMessage_Web($dcSessionId)
    {

        $fromUserId = "";
        $toUserId = "";

        $title = "test title";
        $webHtmlCode = "";
        $width = 100;
        $height = 200;
        $this->dcApi->sendWebMessage(false, $fromUserId, $toUserId, $title, $webHtmlCode, $width, $height);

        $toGroupId = "";
        //发送群组Web消息
        $this->dcApi->sendWebMessage(true, $fromUserId, $toGroupId, $title, $webHtmlCode, $width, $height);

    }

    protected function dc_sendMessage_WebNotice($dcSessionId)
    {

        $fromUserId = "";
        $toUserId = "";

        $title = "test title";
        $webHtmlCode = "";//这里是html代码
        $height = 200;
        $this->dcApi->sendWebNoticeMessage(false, $fromUserId, $toUserId, $title, $webHtmlCode, $height);

        $toGroupId = "";
        //发送群组Web消息
        $this->dcApi->sendWebNoticeMessage(true, $fromUserId, $toGroupId, $title, $webHtmlCode, $height);

    }


    protected function server_error($err)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 MiniProgram Server Error',
            true, 500);
        die('<h2>500 MiniProgram Server Error</h2>' . $err);
    }

}
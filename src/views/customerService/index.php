<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $chatTitle;?></title>
    <!-- Latest compiled and minified CSS -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<!--    <link rel=stylesheet href="../../public/css/zaly_msg.css" />-->
    <link rel=stylesheet href="../../public/css/loading.css" />

    <style>
        html,body {
            height:100%;
            width:100%;
            font-size: 10.66px;
        }
        .container {
            height:100%;
            width:100%;
            position:relative;
            z-index:99999;
            display: flex;
            align-items: center;
        }
        .box {
            position: absolute;
            right:0px;
            top:0px;
            bottom:0px;
            margin:auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .msg-avatar img {
            width: 4rem;
            height: 4rem;
            border-radius: 10%;
        }
        .close_chat {
            width:40px;
            height:182px;
            background:rgba(52,54,60,1);
            position: relative;
            cursor: pointer;
        }
        .line {
            width:40px;
            height:1px;
            background:rgba(255,255,255,1);
            position: absolute;
            top:40px;
        }
        .chat_png_div {
            display: flex;
            justify-content: center;
            align-items: center;
            width:40px;
            height:44px;
        }
        .chat_png_div img {
            width:24px;
            height:24px;
        }
        .chat_tip {
            margin-top: 8px;
            font-size:14px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(255,255,255,1);
            text-align: center;
        }
        .chat_dialog_div{
            width:320px;
            height:500px;
            background:rgba(255,255,255,1);
            box-shadow:0px 2px 16px 0px rgba(223,223,223,1);
            border-radius:10px 10px 0px 0px;
            position: absolute;
            right:60px;
            bottom:0px;
            margin:0 auto ;
            display: none;
            border:1px solid #cccc;

        }
        .chat_title {
            width:320px;
            height:40px;
            background:rgba(52,54,60,1);
            border-radius:10px 10px 0px 0px;
            font-size:16px;
            font-family:PingFangSC-Medium;
            font-weight:500;
            color:rgba(255,255,255,1);
            line-height: 40px;
            text-align: center;
            position: relative;
        }
        .close_chat_png {
            width: 14px;
            height:14px;
            position: absolute;
            top:10px;
            right:10px;
            margin:auto;
            cursor: pointer;
        }
        .service_right-chatbox {
            width: 100%;
            height:386px;
            flex-grow: 1;
            flex-shrink: 1;
            overflow-y: scroll;
            height: 1;
            overflow-x: hidden;
        }
        .chat_line {
            width:100%;
            height:1px;
            background:rgba(223,223,223,1);
        }
        .chat_bottom_line {
            width:260px;
            height:1px;
            background:rgba(201,201,201,1);
            position: absolute;
            bottom:5px;
            left: 10px;
            margin:auto;
        }
        .chat_box {
            position: relative;
            height:50px;
        }
        .send_msg {
            width: 35px;
            height:35px;
            position: absolute;
            right:10px;
            top:10px;
            margin: 0;
        }
        .msg_content {
            outline: none;
            resize: none;
            border: none;
            flex-grow: 1;
            font-size:14px;
            line-height:20px;
            padding-top: 20px;
            margin-left: 10px;
            height:20px;
            width:260px;
        }
        .msg_status {
            display: flex;
            flex-direction: row-reverse;
        }
        .msg-right .msg-content {
            color: #FFFFFF;
            background: rgba(92,72,207,1);
            border-radius:4px 0px 4px 4px;
        }
        .msg-content {
            font-size: 14px;
            color: #141030;
            width: auto;
            display: inline-block;
            padding: 10px;
            background: rgba(244,244,249,1);
            border-radius:0px 4px 4px 4px;
        }
        .showbox {
            position: relative;
            margin-right: 30px;
        }

        .msg_status_img {
            position: relative;
            display: none;
        }
        .msg-row {
            margin-top: 20px;
            display: flex;
            flex-direction: row;
        }
        .msg-right {
            display: flex;
            flex-direction: row-reverse;
            padding-right: 10px;
            margin-bottom: 20px;
        }
        .text-align-left, .text-align-right {
            text-align: left;
            position: relative;
            word-break: break-all;
            cursor: pointer;
        }

        .text-align-left-text pre, .text-align-right-text pre {
            margin: 0px;
            padding: 0px;
            display: inline;
            white-space: pre-wrap;
        }

        .msg-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 10%;
        }

        .text-align-right {
            text-align: right;
            word-break: break-all;
        }

        .msg-body, .right-msg-body {
            flex-grow: 1;
            padding: 0 10px;
        }

        .msg-left {
            padding-left: 10px;
            padding-right: 10px;
            margin-bottom: 20px;
        }

        msg_status_img {
            position: relative;
            display: none;
        }
        .msg_status_img img {
            position: absolute;
            bottom:0;
            right:1rem;
        }
        .warning_tip {
            height:20px;
            width: 100%;
            justify-content: center;
            align-items: center;
            display: none;
            background: #f4f4f6;
        }

        .showbox {
            position: relative;
            margin-right: 3rem;
        }

        .loader {
            position: absolute;
            bottom:0;
        }

        .loader:before {
            content: '';
            display: block;
            padding-top: 100%;
        }

        .circular {
            animation: rotate 2s linear infinite;
            height: 2rem;
            transform-origin: center center;
            width: 2rem;
            margin: auto;
            bottom:0;
        }

        .path {
            stroke-dasharray: 1, 200;
            stroke-dashoffset: 0;
            animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
            stroke-linecap: round;
        }

        @keyframes rotate {
            100% {
                transform: rotate(360deg);
            }
        }
        @keyframes dash {
            0% {
                stroke-dasharray: 1, 200;
                stroke-dashoffset: 0;
            }
            50% {
                stroke-dasharray: 89, 200;
                stroke-dashoffset: -35px;
            }
            100% {
                stroke-dasharray: 89, 200;
                stroke-dashoffset: -124px;
            }
        }
        @keyframes color {
            100%, 0% {
                stroke: #4C3BB1;
            }
            40% {
                stroke: #4C3BB1;
            }
            66% {
                stroke: #4C3BB1;
            }
            80%, 90% {
                stroke: #4C3BB1;
            }
        }

    </style>
</head>
<body>
<div class="container">
    <div class="box">
        <div class="close_chat">
            <div class="chat_png_div"> <img src="./public/img/service/chat.png"></div>
            <div class="line"></div>
            <div class="chat_tip">在<br>线<br>客<br>服<br>咨<br>询</div>
        </div>
    </div>
    <div class="chat_dialog_div">
        <div class="chat_title"><?php echo $chatTitle; ?><img src="./public/img/service/close.png" class="close_chat_png" type="hide"/></div>
        <div class="warning_tip">当前无在线客服,请稍候再试</div>
        <div class="service_right-chatbox">
        </div>
        <div class="chat_line"></div>
        <div class="chat_box">
            <img class="send_msg" src="./public/img/service/send.png">
            <div class="chat_bottom_line"></div>
            <div id="msgImage"></div>
            <textarea class="input-box-text msg_content" onkeydown="sendMsgByKeyDown(event)"  placeholder="输入消息…."data-local-placeholder="enterMsgContentPlaceholder"  id="msg_content"></textarea>
        </div>
    </div>
</div>

<input type="hidden" data='<?php echo $enableCustomerService;?>' class="enableCustomerService">
<input type="hidden" data='<?php echo $signatureError;?>' class="signatureError">

<input type="hidden" value='<?php echo $thirdLoginKey;?>' class="thirdLoginKey">
<input type="hidden" data='<?php echo $userId;?>' class="service_token">
<input type="hidden" data='' class="service_self_avatar">
<input type="hidden" data='' class="service_loginName">
<input type="hidden" data='' class="service_nickname">
<input type="hidden" data='<?php echo $sessionId;?>' class="service_session_id">
<input type="hidden" value='<?php echo $siteAddress;?>' class="siteAddress">
<?php include(dirname(__DIR__) . '/customerService/template_service.php'); ?>

<script src="./public/js/jquery.min.js"></script>
<script src="./public/js/im/zalyKey.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/template-web.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/zalyjsHelper.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyAction.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/service/zalyServiceClient.js?_version=<?php echo $versionCode?>"></script>

<script src="./public/js/im/zalyBaseWs.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/service/zalyServiceIm.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/service/zalyService.js"></script>

</body>
</html>


<script type="text/javascript">

    var serviceToken = $(".service_token").attr('data');
    var serviceSessionId =  $(".service_session_id").attr("data");
    var serviceAvatar = $(".service_avatar").attr("data");
    var serviceNickname = $(".service_nickname").attr("data");
    var serviceLoginName = $(".service_loginName").attr("data");
    var enableCustomerService = $(".enableCustomerService").attr("data");
    var signatureError = $(".signatureError").attr("data");

    var thirdPartyKey  = $(".thirdLoginKey").val();
    var isRegister = false;

    function getSelfInfoByClassName()
    {
        serviceToken    = $('.service_token').attr("data");
        serviceNickname = $(".service_nickname").attr("data");
        serviceLoginName = $(".service_loginName").attr("data");
        serviceAvatar = $(".service_self_avatar").attr("data");
        serviceSessionId =  $(".service_session_id").attr("data");
    }

    $(document).on("click", ".close_chat", function(){
        displayChatDialogByClick();
    });

    $(document).on("click", ".close_chat_png", function () {
        displayChatDialogByClick();
    });

    function displayChatDialogByClick()
    {

        var type = $(".close_chat_png").attr("type");
        if(type == "hide") {
            $(".close_chat_png").attr("type", "display");
            $(".chat_dialog_div")[0].style.display = "block";

            if(Number(enableCustomerService) != 1) {
                notEnableCustomerServiceFunc();
                return;
            }
            showLoading($(".chat_dialog_div"));

            if(serviceSessionId == undefined || serviceSessionId ==""
                || serviceToken == "" || serviceToken == undefined
            ) {
                createCustomerServiceAccount();
                return;
            }
            getMsgForCustomer();
            return;
        } else {
            $(".close_chat_png").attr("type", "hide");
            $(".chat_dialog_div")[0].style.display = "none";
        }
    }

    function getMsgForCustomer()
    {
        var requestUrl = "./index.php?action=page.customerService.index";
        serviceToken = $(".service_token").attr('data');
        var data = {
            "customerId":serviceToken,
            "operation" :'addFriend'
        }
        serviceAjaxPost(requestUrl, data, handleAddCustomerService);
    }


    function getStartChat(chatSessionId, type) {
        // console.log("getStartChat--"+type);
        getSelfInfoByClassName();
        $(".service_right-chatbox").attr("chat-session-id", chatSessionId);
        hideLoading();
        getMsgFromRoom(chatSessionId);
        getSelfInfo();
    }

    function  handleAddCustomerService(result) {
        try{
            hideLoading();
            var result = JSON.parse(result);
            var chatSessionId = result['customerServiceId'];
            if(chatSessionId == "") {
                notEnableCustomerServiceFunc();
                return;
            }
            localStorage.removeItem(roomKey+chatSessionId);
            localStorage.setItem(chatSessionIdKey, chatSessionId);
            localStorage.setItem(chatSessionId, U2_MSG);
            getStartChat(chatSessionId, 'handleAddCustomerService');
        }catch (error) {
            closeChatDialog();
        }
    }

    function notEnableCustomerServiceFunc()
    {
        if(signatureError == 1) {
            $(".warning_tip").html("签名不对，请稍候再试");
        }
        $(".warning_tip")[0].style.display = "flex";
        $(".msg_content").attr("disabled", "disabled");
        $(".chat_box")[0].style.backgroundColor = "#f4f4f6";
        $(".msg_content")[0].style.backgroundColor = "#f4f4f6";
    }

    function createCustomerServiceAccount()
    {
        var operation = isRegister == true ? 'create' : "login";
        var requestUrl = "./index.php?action=page.customerService.index";
        var data = {
            "operation" :operation
        }
        serviceAjaxPost(requestUrl, data, handleCreateCustomerServiceAccount);
    }

    function handleCreateCustomerServiceAccount(result)
    {
        var result = JSON.parse(result);
        if(result['errorCode'] == 'success') {
            if(result.hasOwnProperty("sessionId") && result['sessionId']) {
                $(".service_session_id").attr("data", result['sessionId']);
                $(".service_token").attr("data", result['userId']);
                getMsgForCustomer();
                return;
            }
            zalyjsServiceApiSiteLogin(result['preSessionId'], result['loginName']);
        } else {
            alert("链接失败，请稍候再试");
        }
    }

    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return "1";
        }
        return "0";
    }

    function zalyjsServiceApiSiteLogin(preSessionId, loginName) {
        var refererUrl = "./index.php";
        var body = {
            "@type":  "type.googleapis.com/site.ApiSiteLoginRequest",
            "preSessionId":preSessionId,
            "loginName":loginName,
            "isRegister":true,
            "thirdPartyKey":thirdPartyKey,
        };

        var header = {};
        header[HeaderHostUrl] = refererUrl;
        header[HeaderUserClientLang] = getLanguage();
        header[HeaderUserAgent] = navigator.userAgent;
        var packageId = localStorage.getItem("packageId");

        var transportData = {
            "action" : "api.site.login",
            "body": body,
            "header" : header,
            "packageId" : Number(packageId),
        };

        var transportDataJson = JSON.stringify(transportData);
        if (refererUrl.indexOf("?") > -1) {
            var url = refererUrl + "&action=api.site.login&body_format=json";
        } else {
            var url = refererUrl + "?action=api.site.login&body_format=json";
        }

        var http = new XMLHttpRequest();
        http.open('POST', url, true);
        // Send the proper header information along with the request
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        http.onreadystatechange = function() {//Call a function when the state changes.
            if(http.readyState == 4 && http.status == 200) {

                var results = JSON.parse(http.responseText);
                if(results.hasOwnProperty("header") && results.header[HeaderErrorCode] == "success") {
                    var sessionId = results.body['sessionId'];
                    serviceToken = results.body.profile.public['userId'];
                    serviceAvatar = results.body.profile.public['avatar'];
                    loginName = results.body.profile.public['loginName'];
                    nickname  = results.body.profile.public['nickname'];

                    setCookie("duckchat_service_cookie", sessionId);

                    $(".service_token").attr('data', serviceToken);
                    $(".service_self_avatar").attr("data",serviceAvatar );
                    $(".service_loginName").attr("data", loginName );
                    $(".service_nickname").attr("data", nickname );
                    $(".service_session_id").attr("data", sessionId);

                    getMsgForCustomer();
                } else {
                    closeChatDialog();
                }
            }
        }
        http.send(transportDataJson);
    }

    function closeChatDialog() {
        // alert("请稍候再试");
        hideLoading();
    }

    function serviceAjaxPost(requestUrl, data, serviceCallBack){
        $.ajax({
            method: "POST",
            url:requestUrl,
            data: data,
            success:function (resp, status, request) {
                serviceCallBack(resp);
            }
        });
    }

</script>

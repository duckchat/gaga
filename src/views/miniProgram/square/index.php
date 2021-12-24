<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-control" content="public,max-age=310">
    <title><?php if ($lang == "1") { ?>用户广场<?php } else { ?>User Square<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <style>

        html, body {
            padding: 0px;
            margin: 0px;
            font-family: PingFangSC-Regular, "MicrosoftYaHei";
            width: 100%;
            background: rgba(245, 245, 245, 1);
            font-size: 14px;
            overflow-x: hidden;
        }

        .wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: stretch;
            overflow-y: scroll;
        }

        .layout-all-row {
            width: 100%;
            /*background: white;*/
            background: rgba(245, 245, 245, 1);;
            display: flex;
            align-items: stretch;
            overflow: hidden;
            flex-shrink: 0;

        }

        .item-row {
            background: rgba(255, 255, 255, 1);
            display: flex;
            flex-direction: row;
            text-align: center;
            height: 60px;
        }

        /*.item-row:hover{*/
        /*background: rgba(255, 255, 255, 0.2);*/
        /*}*/

        .item-row:active {
            background: rgba(255, 255, 255, 0.2);
        }

        .item-header {
            width: 50px;
            height: 50px;
        }

        .user-avatar-image {
            width: 44px;
            height: 44px;
            margin-top: 8px;
            margin-bottom: 8px;
            margin-left: 10px;
            border-radius: 10%;
        }

        .item-body {
            width: 100%;
            height: 50px;
            margin-left: 1rem;
            margin-top: 7px;
            flex-direction: row;
        }

        .list-item-center {
            width: 100%;
            /*height: 11rem;*/
            /*background: rgba(255, 255, 255, 1);*/
            padding-bottom: 11px;
            /*padding-left: 1rem;*/

        }

        .item-body-display {
            display: flex;
            justify-content: space-between;
            /*margin-right: 7rem;*/
            /*margin-bottom: 3rem;*/
            line-height: 3rem;
        }

        .item-body-tail {
            margin-right: 10px;
        }

        .item-body-desc {
            height: 3rem;
            font-size: 16px;
            font-family: PingFangSC-Regular;
            /*color: rgba(76, 59, 177, 1);*/
            margin-left: 11px;
            line-height: 3rem;
        }

        .more-img {
            width: 8px;
            height: 13px;
            /*border-radius: 50%;*/
        }

        .division-line {
            height: 1px;
            background: rgba(243, 243, 243, 1);
            margin-left: 40px;
            overflow: hidden;
        }

        .addButton {
            width: 80px;
            height: 28px;
            background: rgba(76, 59, 177, 1);
            border-radius: 4px;
            border-width: 0;
            font-size: 14px;
            font-family: PingFangSC-Regular;
            font-weight: 400;
            color: rgba(255, 255, 255, 1);
            cursor: pointer;
            outline: none;
        }

        .chatButton {
            width: 80px;
            height: 28px;
            background: rgba(0, 0, 0, 0.09);
            border-radius: 4px;
            border-width: 0;
            font-size: 14px;
            font-family: PingFangSC-Regular;
            font-weight: 400;
            cursor: pointer;
            outline: none;
        }

        /* mask and new window */
        .wrapper-mask {
            background: rgba(0, 0, 0, 0.8);
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            position: fixed;
            z-index: 9999;
            overflow: hidden;
            visibility: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #popup-group {
            width: 320px;
            height: 300px;
            background: rgba(255, 255, 255, 1);
            border-radius: 10px;
        }

        .header_tip_font {
            justify-content: center;
            text-align: center;
            margin-top: 29px;
            height: 24px;
            font-size: 24px;
            font-weight: bold;
            color: #4C3BB1;
            font-family: PingFangSC;
            /*line-height: 3.75rem;*/
        }

        .popup-group-input {
            background-color: #ffffff;
            border-style: none;
            outline: none;
            /*height: 1.88rem;*/
            font-size: 20px;
            font-family: PingFangSC-Regular;
            /*color: rgba(205, 205, 205, 1);*/
            line-height: 22px;
            margin-top: 65px;
            width: 250px;
            height: 30px;
            overflow: auto;
        }

        .create_button,
        .create_button:hover,
        .create_button:focus,
        .create_button:active {
            margin-top: 45px;
            width: 250px;
            height: 50px;
            background: rgba(76, 59, 177, 1);
            border-width: 0px;
            border-radius: 7px;
            font-size: 16px;
            color: rgba(255, 255, 255, 1);
            cursor: pointer;
            outline: none;
        }

        .line {
            width: 250px;
            height: 1px;
            background: rgba(201, 201, 201, 1);
            border: 0.1px solid rgba(201, 201, 201, 1);
            overflow: hidden;
            text-align: center;
            margin: 0 auto;
        }

    </style>

    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css"/>
    <!--    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.css"/>-->
</head>

<body id="square-body">

<div class="wrapper" id="wrapper">

    <input type="hidden" id="myUserId" userId="<?php echo $userId ?>"
           nickname="<?php echo $nickname ?>">

    <div class="layout-all-row">

        <div class="list-item-center">

            <?php foreach ($userList as $i => $user) { ?>
                <div class="item-row">
                    <div class="item-header">
                        <img class="user-avatar-image" avatar="<?php echo $user['avatar'] ?>"
                             src=""
                             onerror="this.src='../../public/img/msg/default_user.png'"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc" onclick="showUserChat('<?php echo $user["userId"] ?>')">
                                <?php
                                $length = mb_strlen($user['nickname']);
                                if ($length > 10) {
                                    echo mb_substr($user['nickname'], 0, 10) . "...";
                                } else {
                                    echo $user['nickname'];
                                }
                                ?>
                            </div>

                            <div class="item-body-tail">

                                <?php if (!$user['isFollow']) { ?>
                                    <button class="addButton applyButton" userId="<?php echo $user['userId'] ?>">
                                        添加好友
                                    </button>
                                <?php } else { ?>
                                    <button class="chatButton" userId="<?php echo $user['userId'] ?>">
                                        发起会话
                                    </button>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>
            <?php } ?>


        </div>

    </div>

</div>

<div class="wrapper-mask" id="wrapper-mask" style="visibility: hidden;"></div>

<div class="popup-template" style="display:none;">

    <div class="config-hidden" id="popup-group">

        <div class="flex-container">
            <div class="header_tip_font popup-group-title"></div>
        </div>

        <div class="" style="text-align: center">
            <input type="text" class="popup-group-input"
                   data-local-placeholder="enterGroupNamePlaceholder" placeholder="please input">
        </div>

        <div class="line"></div>

        <div class="" style="text-align:center;">
            <?php if ($lang == "1") { ?>
                <button id="update-user-button" type="button" class="create_button" data=""
                        onclick="sendRequest();">发送
                </button>
            <?php } else { ?>
                <button id="update-user-button" type="button" class="create_button" data=""
                        onclick="sendRequest();">Send
                </button>
            <?php } ?>
        </div>

    </div>

</div>

<!--<script type="text/javascript" src="../../public/js/jquery.min.js"></script>-->
<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

<script type="text/javascript">

    function showWindow(jqElement) {
        jqElement.css("visibility", "visible");
        $(".wrapper-mask").css("visibility", "visible").append(jqElement);
    }


    function removeWindow(jqElement) {
        jqElement.remove();
        $(".popup-template").append(jqElement);
        $(".wrapper-mask").css("visibility", "hidden");
        $("#update-user-button").attr("data", "");
        $(".popup-group-input").val("");
        $(".popup-template").hide();
    }


    // $(document).mouseup(function (e) {
    $(".wrapper-mask").mouseup(function (e) {
        var targetId = e.target.id;
        var targetClassName = e.target.className;

        if (targetId == "wrapper-mask") {
            var wrapperMask = document.getElementById("wrapper-mask");
            var length = wrapperMask.children.length;
            var i;
            for (i = 0; i < length; i++) {
                var node = wrapperMask.children[i];
                node.remove();
                // addTemplate(node);
                $(".popup-template").append(node);
                $(".popup-template").hide();
            }
            $("#update-user-button").attr("data", "");
            wrapperMask.style.visibility = "hidden";
        }
    });

    $("#square-body").on("click", ".chatButton", function () {
        var friendId = $(this).attr("userId");
        var url = "duckchat://0.0.0.0/goto?page=u2Msg&x=" + friendId;
        try {
            zalyjsGoto(null, "u2Msg", friendId);
        } catch (e) {
            alert(getLanguage() == 1 ? "客户端暂不支持，请升级客户端" : "Please upgrade the client version.");
        }
        return;
    });


    $("#square-body").on("click", '.applyButton', function () {
        var lang = getLanguage();
        var myNickname = $("#myUserId").attr("nickname");
        var title = lang == 1 ? "申请好友" : "Apply Friend";
        var inputBody = "I'm " + myNickname + ",apply for friend";

        if (lang == 1) {
            inputBody = "我是 " + myNickname + ",申请添加好友";
        }

        var friendId = $(this).attr("userId");

        $("#update-user-button").attr("data", friendId);
        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
    });


    function sendRequest() {
        var friendUserId = $("#update-user-button").attr("data");
        var applyInfo = $(".popup-group-input").val();

        var data = {
            'friendId': friendUserId,
            'greeting': applyInfo
        };

        var url = "index.php?action=miniProgram.square.apply";
        zalyjsCommonAjaxPostJson(url, data, applyResponse)

        removeWindow($(".config-hidden"));
    }


    function applyResponse(url, data, result) {
        var res = JSON.parse(result);

        if (res.errCode != "success") {
            alert(res.errInfo);
        }
    }

</script>

<script type="text/javascript">

    var currentPageNum = 2;
    // var pageSize = 12;
    var loading = true;

    $(window).scroll(function () {
        //判断是否滑动到页面底部
        if ($(window).scrollTop() === $(document).height() - $(window).height()) {

            if (!loading) {
                return;
            }

            loadMoreUsers();
        }
    });

    function loadMoreUsers() {

        var data = {
            'pageNum': currentPageNum++,
        };

        var url = "index.php?action=miniProgram.square.index";
        zalyjsCommonAjaxPostJson(url, data, loadMoreResponse)
    }

    $(".user-avatar-image").each(function () {
        var avatar = $(this).attr("avatar");
        var src = " /_api_file_download_/?fileId=" + avatar;
        if (!isMobile()) {
            src = "./index.php?action=http.file.downloadFile&fileId=" + avatar + "&returnBase64=0";
        }
        $(this).attr("src", src);
    });


    function loadMoreResponse(url, data, result) {
        if (result) {
            var res = JSON.parse(result);

            var isloading = res['loading'];
            loading = isloading;
            var data = res['data'];

            // alert(result);
            if (data && data.length > 0) {
                var isMobileClient = isMobile();

                $.each(data, function (index, user) {
                    var src = "./index.php?action=http.file.downloadFile&fileId=" + user['avatar'] + "&returnBase64=0&lang=" + getLanguage();

                    if (isMobileClient) {
                        src = '/_api_file_download_/?fileId=' + user['avatar'];
                    }

                    var userHtml = '<div class="item-row" userId="' + user["userId"] + '" >'
                        + '<div class="item-header">'
                        + '<img class="user-avatar-image" src="' + src + '" onerror="this.src=\'../../public/img/msg/default_user.png\'" />'
                        + '</div>'
                        + '<div class="item-body">'
                        + '<div class="item-body-display">'
                        + '<div class="item-body-desc">' + user["nickname"] + '</div>'
                        + '<div class="item-body-tail">';

                    if (!user['isFollow']) {
                        userHtml += '<button class="addButton applyButton" userId="' + user["userId"] + '" > 添加好友 </button>';
                    } else {
                        userHtml += '<button class="chatButton" userId="' + user["userId"] + '" > 发起会话 </button>';
                        // userHtml += '<button class="chatButton" userId="' + user["userId"] + '" > 已添加 </button>';
                    }


                    userHtml += '</div></div></div></div>';
                    userHtml += '<div class="division-line"></div>';

                    $(".list-item-center").append(userHtml);

                    $(".applyButton").bind("click");
                });
            }

        }

    }

</script>

</body>
</html>





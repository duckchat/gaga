<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>安全配置<?php } else { ?>Security configuration<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>
    <link rel="stylesheet" href="../../public/manage/config.css"/>

    <style>

        .weui_switch {
            margin-top: 0px;
        }

    </style>
</head>

<body>


<div class="wrapper" id="wrapper">
    <div class="layout-all-row" style="margin-top:10px;">

        <div class="list-item-center">
            <div class="item-row" id="quick_configuration">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                快速配置
                            <?php } else { ?>
                                Quick configuration
                            <?php } ?>
                        </div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../public/img/manage/more.png"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="division-line"></div>
        </div>
    </div>
    <div class="layout-all-row">
        <div class="list-item-center">
            <div class="item-row" id="security_configuration">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                用户名、密码策略
                            <?php } else { ?>
                                Username, password policy
                            <?php } ?>
                        </div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../public/img/manage/more.png"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>
        </div>
    </div>
    <div class="layout-all-row">
        <div class="list-item-center">
            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display passwordErrorNum" onclick="showPasswordErrorNum()">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">用户每天密码错误上限</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Password error limit per day</div>
                        <?php } ?>
                        <div class="item-body-tail">
                            <div class="item-body-value" id="passwordErrorNum"> <?php echo $passwordErrorNum; ?></div>
                            <div class="item-body-value">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="division-line"></div>
        </div>
    </div>

    <div class="layout-all-row">
        <div class="list-item-center">
            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display" id="password_error_log">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">密码错误日志</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Password error log</div>
                        <?php } ?>
                        <div class="item-body-tail">
                            <img class="more-img" src="../../public/img/manage/more.png"/>
                        </div>

                    </div>

                </div>
            </div>
            <div class="division-line"></div>

        </div>
    </div>


    <div class="layout-all-row">
        <div class="list-item-center">

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">开启聊天水印</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Open Chat WaterMark</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <?php if ($openWaterMark == 1) { ?>
                                <input id="openWaterMarkSwitch" class="weui_switch" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="openWaterMarkSwitch" class="weui_switch" type="checkbox">
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>
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
            <input type="text" class="popup-group-input" placeholder="please input">
        </div>

        <div class="line"></div>

        <div class="" style="text-align:center;">
            <?php if ($lang == "1") { ?>
                <button id="updatePopupButton" type="button" class="create_button" key-value=""
                        onclick="updateDataValue();">确认
                </button>
            <?php } else { ?>
                <button id="updatePopupButton" type="button" class="create_button" key-value=""
                        onclick="updateDataValue();">Confirm
                </button>
            <?php } ?>
        </div>

    </div>

</div>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

<script type="text/javascript">

    $("#quick_configuration").on("click", function () {
        var url = "index.php?action=manage.security.quick&lang=" + getLanguage();
        zalyjsCommonOpenNewPage(url);
    });
    $("#security_configuration").on("click", function () {
        var url = "index.php?action=manage.security.normal&lang=" + getLanguage();
        zalyjsCommonOpenNewPage(url);
    });

    $("#password_error_log").on("click", function () {
        var url = "index.php?action=manage.security.log&lang=" + getLanguage();
        zalyjsCommonOpenNewPage(url);
    });

    function showPasswordErrorNum() {
        var title = $(".passwordErrorNum").find(".item-body-desc").html();
        var inputBody = $("#passwordErrorNum").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", "passwordErrorNum");
    }

    function showWindow(jqElement) {
        jqElement.css("visibility", "visible");
        $(".wrapper-mask").css("visibility", "visible").append(jqElement);
    }


    function removeWindow(jqElement) {
        jqElement.remove();
        $(".popup-template").append(jqElement);
        $(".wrapper-mask").css("visibility", "hidden");
    }


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
            $(".popup-group-input").val("");
            $("#updatePopupButton").attr("data", "");
            wrapperMask.style.visibility = "hidden";
        }
    });

    function updateDataValue() {

        var key = $("#updatePopupButton").attr("key-value");

        var url = "index.php?action=manage.security.update";

        var value = $.trim($(".popup-group-input").val());

        var data = {
            'key': key,
            'value': value,
        };

        zalyjsCommonAjaxPostJson(url, data, updateResponse);

        // close
        removeWindow($(".config-hidden"));
    }

    function updateResponse(url, data, result) {
        var res = JSON.parse(result);
        if ("success" == res.errCode) {
            window.location.reload();
        } else {
            alert("error : " + res.errInfo);
        }
    }

    $("#openWaterMarkSwitch").change(function () {
        var isChecked = $(this).is(':checked');
        var url = "index.php?action=manage.config.update&key=enableInvitationCode";

        var data = {
            'key': 'openWaterMark',
            'value': isChecked ? 1 : 0,
        };

        zalyjsCommonAjaxPostJson(url, data, enableSwitchResponse);

    });

    function enableSwitchResponse(url, data, result) {
        if (result) {

            var res = JSON.parse(result);

            if ("success" != res.errCode) {
                var errInfo = res.errInfo;
                var errMsg = (getLanguage() == 1 ? "操作失败,原因：" : "update error, cause:") + errInfo;
                alert(errMsg);
            }

        } else {
            alert(getLanguage() == 1 ? "操作失败" : "update error");
        }
    }
</script>

</body>
</html>





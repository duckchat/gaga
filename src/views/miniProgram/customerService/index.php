<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>客服小程序<?php } else { ?>Customer Service<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>

    <link rel="stylesheet" href="../../public/manage/config.css"/>

    <style>
        body, html {
            height:100%;

        }

        .item-row, .create_button, .weui-actionsheet__cell {
            cursor: pointer;
            outline: none;
        }

        .weui_switch {
            margin-top: 0px;
            cursor: pointer;
        }
        .site-rsa-pubk-pem {
            height: 100%;
        }
        .service_code {
            margin-left: 10px;
            height: 80px;
            line-height: 20px;
            text-align: left;
        }
        .wrapper {
            height: 100%;
        }

    </style>

</head>

<body>

<div class="wrapper" id="wrapper">

    <!-- part 3   -->

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">是否开启客服</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Enable Customer Service</div>
                        <?php } ?>


                        <div class="item-body-tail">
                            <?php if ($enableCustomerService == 1) { ?>
                                <input id="enableCustomerService" class="weui_switch" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="enableCustomerService" class="weui_switch" type="checkbox">
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

        </div>

    </div>
<input type="hidden" data="<?php echo $signVerifyKey; ?>" class="signVerifyKey">

    <!--  site basic config  -->
    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row" id="chatTitle" onclick="showTitle()">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">名称</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Title</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value"><?php echo $chatTitle; ?></div>
                            <div class="item-body-value"><img class="more-img"
                                                              src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAnCAYAAAAVW4iAAAABfElEQVRIS8WXvU6EQBCAZ5YHsdTmEk3kJ1j4HDbGxMbG5N7EwkIaCy18DxtygMFopZ3vAdkxkMMsB8v+XqQi2ex8ux/D7CyC8NR1fdC27RoRszAMv8Ux23ccJhZFcQoA9wCQAMAbEd0mSbKxDTzM6wF5nq+CIHgGgONhgIi+GGPXURTlLhDstDRN8wQA5zOB3hljFy66sCzLOyJaL6zSSRdWVXVIRI9EdCaDuOgavsEJY+wFEY8WdmKlS5ZFMo6xrj9AF3EfukaAbcp61TUBdJCdn85J1yzApy4pwJeuRYAPXUqAqy4tgIsubYCtLiOAjS5jgKkuK8BW1w0APCgOo8wKMHcCzoA+AeDSGKA4AXsOEf1wzq/SNH01AtjUKG2AiZY4jj9GXYWqazDVIsZT7sBGizbAVosWwEWLEuCqZRHgQ4sU4EvLLMCnlgnAt5YRYB9aRoD/7q77kivWFlVZ2R2XdtdiyTUNqpNFxl20bBGT7ppz3t12MhctIuwXEK5/O55iCBQAAAAASUVORK5CYII="/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

        </div>

    </div>


    <!--  site basic config  -->
    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row" id="greeting" onclick="showGreeting()">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">打招呼语</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Say Hai</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value"><?php echo $greeting; ?></div>
                            <div class="item-body-value"><img class="more-img"
                                                              src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAnCAYAAAAVW4iAAAABfElEQVRIS8WXvU6EQBCAZ5YHsdTmEk3kJ1j4HDbGxMbG5N7EwkIaCy18DxtygMFopZ3vAdkxkMMsB8v+XqQi2ex8ux/D7CyC8NR1fdC27RoRszAMv8Ux23ccJhZFcQoA9wCQAMAbEd0mSbKxDTzM6wF5nq+CIHgGgONhgIi+GGPXURTlLhDstDRN8wQA5zOB3hljFy66sCzLOyJaL6zSSRdWVXVIRI9EdCaDuOgavsEJY+wFEY8WdmKlS5ZFMo6xrj9AF3EfukaAbcp61TUBdJCdn85J1yzApy4pwJeuRYAPXUqAqy4tgIsubYCtLiOAjS5jgKkuK8BW1w0APCgOo8wKMHcCzoA+AeDSGKA4AXsOEf1wzq/SNH01AtjUKG2AiZY4jj9GXYWqazDVIsZT7sBGizbAVosWwEWLEuCqZRHgQ4sU4EvLLMCnlgnAt5YRYB9aRoD/7q77kivWFlVZ2R2XdtdiyTUNqpNFxl20bBGT7ppz3t12MhctIuwXEK5/O55iCBQAAAAASUVORK5CYII="/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

        </div>

    </div>

        <div class="layout-all-row service_code_div"     <?php if ($enableCustomerService != 1) { ?> style="display: none;"   <?php } ?> >
            <div class="list-item-center">

                <div class="item-row" id="site-rsa-pubk-pem" style="height: 29px;line-height: 29px;">
                    <div class="item-body" style="height: 29px;line-height: 29px;">
                        <div class="item-body-display" style="height: 29px;line-height: 29px;">

                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">客服代码(长按复制)</div>
                            <?php } else { ?>
                                <div class="item-body-desc">Code(Long Click For Copy)</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>
                <div class="item-row" class="site-rsa-pubk-pem" style="height: 80px;line-height: 80px;" >
                    <div class="item-body" style="height: 80px;line-height: 80px;" >
                        <div class="item-body-display service_code" >

                        </div>
                    </div>
                </div>
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
<input type="hidden" data='<?php echo $code;?>' class="customer_service_code">

<script type="text/javascript" src="./public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="./public/jquery/jquery-weui.min.js"></script>
<script type="text/javascript" src="./public/js/jquery-confirm.js"></script>
<script type="text/javascript" src="./public/manage/native.js"></script>
<script type="text/javascript" src="./public/js/template-web.js"></script>
<script id="tpl-code" type="text/html">
    {{codeHtml}}
</script>

<script type="text/javascript">

    function showWindow(jqElement) {
        jqElement.css("visibility", "visible");
        $(".wrapper-mask").css("visibility", "visible").append(jqElement);
    }
    var codeSrc = $('.customer_service_code').attr("data");
    console.log(codeSrc);
    var html = template("tpl-code", {
        codeHtml : codeSrc
    });
    $(".service_code").html(html);

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

        var url = "index.php?action=miniProgram.customerService.index";

        var value = $.trim($(".popup-group-input").val());

        var data = {
            'operation':'update',
            'key': key,
            'value': value,
        };

        zalyjsCommonAjaxPostJson(url, data, updateConfigResponse);

        // close
        removeWindow($(".config-hidden"));
    }

    function updateConfigResponse(url, data, result) {
        var res = JSON.parse(result);
        if ("success" == res.errCode) {
            window.location.reload();
        } else {
            alert("error : " + res.errInfo);
        }
    }

    function showGreeting() {
        var title = $("#greeting").find(".item-body-desc").html();
        var inputBody = $("#greeting").find(".item-body-value").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", "greeting");
    }

    function showTitle() {
        var title = $("#chatTitle").find(".item-body-desc").html();
        var inputBody = $("#chatTitle").find(".item-body-value").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", "chatTitle");
    }

    //enable tmp chat
    $("#enableCustomerService").change(function () {
        var isChecked = $(this).is(':checked');
        var url = "index.php?action=miniProgram.customerService.index";
        var signVerifyKey = $(".signVerifyKey").attr("data");
        var data = {
            "operation" : "update",
            'key': 'enableCustomerService',
            'value': isChecked ? 1 : 0,
            'signVerifyKey': signVerifyKey
        };

        zalyjsCommonAjaxPostJson(url, data, enableCustomerServiceResponse);
    });

    function enableCustomerServiceResponse(url, data, result) {
        if (result) {
            var res = JSON.parse(result);
            if ("success" != res.errCode) {
                alert(getLanguage() == 1 ? "操作失败" : "update error");
                return;
            }
            window.location.reload();
        } else {
            alert(getLanguage() == 1 ? "操作失败" : "update error");
        }
    }


</script>


</body>
</html>





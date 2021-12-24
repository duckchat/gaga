<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>自定义配置<?php } else { ?>Custom Settings<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>
    <link rel="stylesheet" href="../../public/manage/config.css"/>

</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row" id="custom_login">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                登录
                            <?php } else { ?>
                                Login
                            <?php } ?>
                        </div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../public/img/manage/more.png"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row" id="custom_register">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                注册
                            <?php } else { ?>
                                Register
                            <?php } ?>
                        </div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../public/img/manage/more.png"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row" id="custom_user">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                用户资料
                            <?php } else { ?>
                                User Profile
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

            <div class="item-row" id="custom-front-page">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                默认帧
                            <?php } else { ?>
                                Front Page
                            <?php } ?>
                        </div>

                        <div class="item-body-tail">
                            <div class="item-body-value" id="custom-front-page-text">
                                <?php if ($frontPage == "0" || $frontPage == 1) { ?>
                                    <?php if ($lang == "1") { ?> 主页 <?php } else { ?> Home <?php } ?>
                                <?php } else if ($frontPage == "2") { ?>
                                    <?php if ($lang == "1") { ?> 聊天 <?php } else { ?> Chats <?php } ?>
                                <?php } else if ($frontPage == "3") { ?>
                                    <?php if ($lang == "1") { ?> 通讯录/好友 <?php } else { ?> Contacts/Friends <?php } ?>
                                <?php } else if ($frontPage == "4") { ?>
                                    <?php if ($lang == "1") { ?> 我<?php } else { ?> Me <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="item-body-value">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row" id="hidden-home-page">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                是否隐藏主页
                            <?php } else { ?>
                                Hidden Home Page
                            <?php } ?>
                        </div>

                        <div class="item-body-tail">
                            <?php if ($hiddenHomePage == 1) { ?>
                                <input id="hiddenHomeSwitch" class="weui_switch" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="hiddenHomeSwitch" class="weui_switch" type="checkbox">
                            <?php } ?>
                        </div>

                    </div>

                </div>
            </div>
            <div class="division-line"></div>
        </div>

    </div>


    <script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="../../public/manage/native.js"></script>

    <script type="text/javascript" src="../../public/jquery/jquery-weui.min.js"></script>
    <script type="text/javascript" src="../../public/js/jquery-confirm.js"></script>

    <script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

    <script type="text/javascript">

        function getLanguage() {
            var nl = navigator.language;
            if ("zh-cn" == nl || "zh-CN" == nl) {
                return 1;
            }
            return 0;
        }

        $("#custom_login").on("click", function () {
            var url = "index.php?action=manage.custom.login&page=phpinfo&lang=" + getLanguage();
            zalyjsCommonOpenNewPage(url);
        });


        $("#custom_register").on("click", function () {
            var url = "index.php?action=manage.custom.register&page=phpinfo&lang=" + getLanguage();
            zalyjsCommonOpenNewPage(url);
        });

        $("#custom_user").on("click", function () {
            var url = "index.php?action=manage.custom.user&lang=" + getLanguage();
            zalyjsOpenPage(url)
        });

        $("#custom-front-page").click(function () {
            var language = getLanguage();
            $.actions({
                title: "",
                onClose: function () {
                    console.log("close");
                },
                actions: [{
                    text: language == 0 ? "Home" : "主页",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#custom-front-page-text").html(language == 0 ? "Home" : "主页");
                        $("#custom-front-page").attr("data", "1");
                        updateFrontPage(1);
                    }
                }, {
                    text: language == 0 ? "Chats" : "聊天",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#custom-front-page-text").html(language == 0 ? "Chats" : "聊天");
                        $("#custom-front-page").attr("data", "2");
                        updateFrontPage(2);
                    }
                }, {
                    text: language == 0 ? "Contacts/Friends" : "通讯录/好友",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#custom-front-page-text").html(language == 0 ? "Contacts/Friends" : "通讯录/好友");
                        $("#custom-front-page").attr("data", "3");
                        updateFrontPage(3);
                    }
                }
                    // , {
                    //     text: language == 0 ? "Me" : "我",
                    //     className: "select-color-primary",
                    //     onClick: function () {
                    //         $("#custom-front-page-text").html(language == 0 ? "Me" : "我");
                    //         $("#custom-front-page").attr("data", "4");
                    //         updateFrontPage(4);
                    //     }
                    // }
                ]
            });
        });

        function updateFrontPage(frontPageValue) {
            var url = "index.php?action=manage.config.update";

            var data = {
                'key': 'frontPage',
                'value': frontPageValue,
            };

            zalyjsCommonAjaxPostJson(url, data, updateConfigResponse);
        }

        //update invitation code
        $("#hiddenHomeSwitch").change(function () {
            var isChecked = $(this).is(':checked');
            var url = "index.php?action=manage.config.update";

            var data = {
                'key': 'hiddenHomePage',
                'value': isChecked ? 1 : 0,
            };

            zalyjsCommonAjaxPostJson(url, data, updateConfigResponse);

        });

        function updateConfigResponse(url, data, result) {
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





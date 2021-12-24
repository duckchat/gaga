<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>站点管理<?php } else { ?>Site Manage<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <style>

        html, body {
            padding: 0px;
            margin: 0px;
            font-family: PingFangSC-Regular, "MicrosoftYaHei";
            width: 100%;
            height: 100%;
            background: rgba(245, 245, 245, 1);
            font-size: 14px;
            overflow-x: hidden;
            overflow-y: scroll;
        }

        .wrapper_div {
            height: 100%;
        }

        .wrapper {
            width: 100%;
            display: flex;
            align-items: stretch;

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
            height: 50px;
            cursor: pointer;
            /*margin-bottom: 2rem;*/
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

        .site-manage-image {
            width: 40px;
            height: 40px;
            margin-top: 5px;
            margin-bottom: 5px;
            margin-left: 16px;
            border-radius: 50%;
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
            overflow-y: scroll;
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

    </style>
</head>

<body>

<div class="wrapper_div">
    <div class="wrapper" id="wrapper">

        <div class="layout-all-row">

            <div class="list-item-center">

                <div class="item-row" id="site-config-id">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/home_config.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                    站点设置
                                <?php } else { ?>
                                    Site Config
                                <?php } ?>
                            </div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>


                <div class="item-row" id="mini-program-id">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/home_miniProgram.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    小程序管理
                                <?php } else { ?>
                                    Mini Program
                                <?php } ?>
                            </div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>


                <div class="item-row" id="user-manage-id">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/home_user.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    用户管理
                                <?php } else { ?>
                                    User Management
                                <?php } ?>
                            </div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>

                <div class="item-row" id="group-manage-id">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/home_group.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    群组管理
                                <?php } else { ?>
                                    Group Management
                                <?php } ?>
                            </div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>

                <div class="item-row" id="uic-manage-id">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/home_uic.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    邀请码
                                <?php } else { ?>
                                    Invitation Code
                                <?php } ?></div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>


            </div>

        </div>

    </div>

    <div class="wrapper" id="wrapper-middle">
        <div class="layout-all-row">

            <div class="list-item-center">


                <div class="item-row" id="site-custom-page">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/home_page.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    自定义配置
                                <?php } else { ?>
                                    Custom Settings
                                <?php } ?></div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>

                <div class="item-row" id="site-security">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/security.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    安全配置
                                <?php } else { ?>
                                    Security Settings
                                <?php } ?></div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>

                <div class="item-row" id="site-advanced">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/advanced.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    高级
                                <?php } else { ?>
                                    Advanced
                                <?php } ?></div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="division-line"></div>

                <div class="item-row" id="site-clean-data">
                    <div class="item-header">
                        <img class="site-manage-image" src="../../public/img/manage/data_clean.png"/>
                    </div>
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    数据清理
                                <?php } else { ?>
                                    Clean Data
                                <?php } ?></div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="wrapper-bottom" id="wrapper-bottom">
        <div class="layout-all-row">

            <div class="list-item-center">

                <?php foreach ($miniProgramList as $miniProgram) { ?>

                    <div class="item-row">
                        <div class="item-header">
                            <img class="site-manage-image" src="../../public/img/manage/plugin_default.png"/>
                        </div>
                        <div class="item-body" onclick="showPluginAdmin('<?php echo $miniProgram["adminPageUrl"] ?>')">
                            <div class="item-body-display">
                                <div class="item-body-desc">
                                    <?php echo $miniProgram["name"] ?>
                                </div>

                                <div class="item-body-tail">
                                    <img class="more-img" src="../../public/img/manage/more.png"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="division-line"></div>

                <?php } ?>

            </div>

        </div>

    </div>

</div>
<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

<script type="text/javascript">

    function openPage(url) {
        if (isMobile()) {
            zalyjsOpenNewPage(url);
        } else {
            zalyjsOpenPage(url);
        }
    }

    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return 1;
        }
        return 0;
    }

    $("#site-config-id").click(function () {
        var url = "/index.php?action=manage.config&lang=" + getLanguage();
        openPage(url);
    });

    $("#mini-program-id").click(function () {
        var url = "index.php?action=manage.miniProgram&lang=" + getLanguage();
        openPage(url);
    });

    $("#user-manage-id").click(function () {
        var url = "index.php?action=manage.user&lang=" + getLanguage();
        openPage(url);
    });

    $("#group-manage-id").click(function () {
        var url = "index.php?action=manage.group&lang=" + getLanguage();
        openPage(url);
    });

    $("#uic-manage-id").click(function () {
        var url = "index.php?action=manage.uic&page=index&lang=" + getLanguage();
        openPage(url);
    });

    $("#site-clean-data").click(function () {
        var url = "index.php?action=manage.data.clean&lang=" + getLanguage();
        openPage(url);
    });


    $("#stie-data-report").click(function () {
        var url = "index.php?action=manage.data.report";
        openPage(url);
    });

    $("#site-custom-page").click(function () {
        var url = "index.php?action=manage.custom&lang=" + getLanguage();
        openPage(url);
    });

    $("#site-advanced").click(function () {
        var url = "index.php?action=manage.advanced&lang=" + getLanguage();
        openPage(url);
    });

    $("#site-security").on("click", function () {
        var url = "index.php?action=manage.security&lang=" + getLanguage();
        openPage(url);
    });


    function showPluginAdmin(url) {
        var duckchatSessionId = getCookie("duckchat_sessionid");
        if(url.indexOf('?') == -1) {
            url += "?duckchat_sessionid="+duckchatSessionId+"&lang=" + getLanguage();
        } else {
            url += "&duckchat_sessionid="+duckchatSessionId+"&lang=" + getLanguage();
        }
        openPage(url);
    }

    function getCookie(name) {
        try{
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return false;
        }catch(error) {

        }
        return false;
    }

</script>

</body>
</html>





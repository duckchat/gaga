<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>小程序添加<?php } else { ?>Add Mini Program<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>
    <link rel="stylesheet" href="../../public/manage/config.css"/>

    <style>

        .create_button,
        .create_button:hover,
        .create_button:focus,
        .create_button:active {
            margin-top: 2rem;
            width: 100%;
            height: 44px;
            background: rgba(76, 59, 177, 1);
            border-radius: 0.94rem;
            font-size: 16px;
            color: rgba(255, 255, 255, 1);
            line-height: 1.67rem;
        }

        .site-image {
            width: 30px;
            height: 30px;
            margin-top: 12px;
            cursor: pointer;
        }

        .item-body-value {
            margin-right: 5px;
        }

        .select-color-primary {
            color: #4C3BB1;
        }
    </style>


</head>

<body>

<div class="wrapper-mask" id="wrapper-mask" style="display: none;"></div>

<div class="wrapper" id="wrapper">

    <!--  site basic config  -->
    <div class="layout-all-row">

        <div class="list-item-center">

            <!--      part1: site name      -->
            <div class="item-row" id="plugin-name">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">名称</div>
                            <div class="item-body-tail">
                                <input id="mini-program-name-id" type="text" class="plugin-add-input"
                                       placeholder="请输入小程序名称">
                            </div>
                        <?php } else { ?>
                            <div class="item-body-desc">Name</div>
                            <div class="item-body-tail">
                                <input id="mini-program-name-id" type="text" class="plugin-add-input"
                                       placeholder="input mini program name">
                            </div>
                        <?php } ?>


                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row" id="mini-program-icon-id">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">小程序图标</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Icon</div>
                        <?php } ?>


                        <div class="item-body-tail">
                            <div class="item-body-value" id="mini-program-fileid" fileId="">
                                <img class="site-image" id="mini-program-image"
                                     onclick="uploadMiniProgramIcon('mini-program-logo');"
                                     src="../../public/img/manage/plugin_default.png">

                                <input id="mini-program-logo" type="file" onchange="uploadImageFile(this)"
                                       accept="image/gif,image/jpeg,image/jpg,image/png,image/svg"
                                       style="display: none;">
                            </div>

                            <img class="more-img"
                                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAnCAYAAAAVW4iAAAABfElEQVRIS8WXvU6EQBCAZ5YHsdTmEk3kJ1j4HDbGxMbG5N7EwkIaCy18DxtygMFopZ3vAdkxkMMsB8v+XqQi2ex8ux/D7CyC8NR1fdC27RoRszAMv8Ux23ccJhZFcQoA9wCQAMAbEd0mSbKxDTzM6wF5nq+CIHgGgONhgIi+GGPXURTlLhDstDRN8wQA5zOB3hljFy66sCzLOyJaL6zSSRdWVXVIRI9EdCaDuOgavsEJY+wFEY8WdmKlS5ZFMo6xrj9AF3EfukaAbcp61TUBdJCdn85J1yzApy4pwJeuRYAPXUqAqy4tgIsubYCtLiOAjS5jgKkuK8BW1w0APCgOo8wKMHcCzoA+AeDSGKA4AXsOEf1wzq/SNH01AtjUKG2AiZY4jj9GXYWqazDVIsZT7sBGizbAVosWwEWLEuCqZRHgQ4sU4EvLLMCnlgnAt5YRYB9aRoD/7q77kivWFlVZ2R2XdtdiyTUNqpNFxl20bBGT7ppz3t12MhctIuwXEK5/O55iCBQAAAAASUVORK5CYII="/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

        </div>

    </div>


    <!-- part 2  register && login plugin-->
    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                        <div class="item-body-desc">落地页URL</div>
                        <div class="item-body-tail">
                            <input id="mini-program-landing-id" type="text" class="plugin-add-input"
                                   placeholder="纯网页小程序请填写页面完整URL">
                            <?php } else { ?>
                            <div class="item-body-desc">Home Page Url</div>
                            <div class="item-body-tail">
                                <input id="mini-program-landing-id" type="text" class="plugin-add-input"
                                       placeholder="http or proxy url">
                                <?php } ?>

                            </div>

                        </div>

                    </div>
                </div>
                <div class="division-line"></div>

                <!--                <div class="item-row">-->
                <!--                    <div class="item-body">-->
                <!--                        <div class="item-body-display">-->
                <!--                            --><?php //if ($lang == "1") { ?>
                <!--                                <div class="item-body-desc">是否开启站点代理</div>-->
                <!--                            --><?php //} else { ?>
                <!--                                <div class="item-body-desc">Open Site Proxy</div>-->
                <!--                            --><?php //} ?>
                <!---->
                <!--                            <div class="item-body-tail">-->
                <!--                                <input id="openProxySwitch-id" class="weui_switch" type="checkbox">-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!---->
                <!--                    </div>-->
                <!--                </div>-->
                <!--                <div class="division-line"></div>-->

            </div>

        </div>

        <!--   part 3  -->
        <div class="layout-all-row">

            <div class="list-item-center">
                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display mini-program-usage" data="1" onclick="selectMiniProgramUsage()">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">小程序使用类别</div>
                            <?php } else { ?>
                                <div class="item-body-desc">MiniProgram Usage</div>
                            <?php } ?>

                            <div class="item-body-tail">
                                <?php if ($lang == "1") { ?>
                                    <div id="mini-program-usage-text" style="margin-right: 4px">首页小程序</div>
                                <?php } else { ?>
                                    <div id="mini-program-usage-text" style="margin-right: 4px">Home Page</div>
                                <?php } ?>
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>

                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display mini-program-order">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">排序</div>
                            <?php } else { ?>
                                <div class="item-body-desc">Order</div>
                            <?php } ?>

                            <div class="item-body-tail">
                                <input id="mini-program-order-input" class="plugin-add-input" type="text" value="10">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>

                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display mini-program-display" data="0"
                             onclick="selectMiniProgramDisplay()">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">打开方式</div>
                            <?php } else { ?>
                                <div class="item-body-desc">Display Mode</div>
                            <?php } ?>

                            <div class="item-body-tail">
                                <?php if ($lang == "1") { ?>
                                    <div id="mini-program-display-text" style="margin-right: 4px">新页面打开</div>
                                <?php } else { ?>
                                    <div id="mini-program-display-text" style="margin-right: 4px">New Page</div>
                                <?php } ?>

                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>

                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display mini-program-permission" data="1"
                             onclick="selectMiniProgramPermission();">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">使用权限</div>
                            <?php } else { ?>
                                <div class="item-body-desc">Use Permission</div>
                            <?php } ?>

                            <div class="item-body-tail">

                                <div id="mini-program-permission-text" style="margin-right: 4px">
                                    <?php if ($lang == "1") { ?> 所有人可用<?php } else { ?> All Users Available<?php } ?>
                                </div>

                                <img class="more-img"
                                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAnCAYAAAAVW4iAAAABfElEQVRIS8WXvU6EQBCAZ5YHsdTmEk3kJ1j4HDbGxMbG5N7EwkIaCy18DxtygMFopZ3vAdkxkMMsB8v+XqQi2ex8ux/D7CyC8NR1fdC27RoRszAMv8Ux23ccJhZFcQoA9wCQAMAbEd0mSbKxDTzM6wF5nq+CIHgGgONhgIi+GGPXURTlLhDstDRN8wQA5zOB3hljFy66sCzLOyJaL6zSSRdWVXVIRI9EdCaDuOgavsEJY+wFEY8WdmKlS5ZFMo6xrj9AF3EfukaAbcp61TUBdJCdn85J1yzApy4pwJeuRYAPXUqAqy4tgIsubYCtLiOAjS5jgKkuK8BW1w0APCgOo8wKMHcCzoA+AeDSGKA4AXsOEf1wzq/SNH01AtjUKG2AiZY4jj9GXYWqazDVIsZT7sBGizbAVosWwEWLEuCqZRHgQ4sU4EvLLMCnlgnAt5YRYB9aRoD/7q77kivWFlVZ2R2XdtdiyTUNqpNFxl20bBGT7ppz3t12MhctIuwXEK5/O55iCBQAAAAASUVORK5CYII="/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>

                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display mini-program-secret-key">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">是否生成秘钥</div>
                            <?php } else { ?>
                                <div class="item-body-desc">Generate Secret Key</div>
                            <?php } ?>

                            <div class="item-body-tail">
                                <input id="mini-program-secret-key-switch" class="weui_switch" type="checkbox">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>

            </div>

        </div>

        <!-- part 4  miniPrograme management -->
        <div class="layout-all-row">

            <div class="list-item-center">

                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display">

                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">管理地址</div>
                                <div class="item-body-tail">
                                    <input id="mini-program-management" type="text" class="plugin-add-input"
                                           placeholder="管理小程序地址">
                                </div>

                            <?php } else { ?>

                                <div class="item-body-desc">Management Url</div>
                                <div class="item-body-tail">
                                    <input id="mini-program-management" type="text" class="plugin-add-input"
                                           placeholder="management url">
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>

                <div class="division-line"></div>

            </div>
        </div>
    </div>


    <div class="wrapper">

        <?php if ($lang == "1") { ?>
            <button id="addMiniProgramButton" type="button" class="create_button" url-value="">保存</button>
        <?php } else { ?>
            <button id="addMiniProgramButton" type="button" class="create_button" url-value="">Save</button>
        <?php } ?>

    </div>

    <div class="popup-template" style="display: none;">

        <div class="config-hidden" id="popup-group">

            <div class="flex-container">
                <div class="header_tip_font popup-group-title" data-local-value="createGroupTip">创建群组</div>
            </div>

            <div class="" style="text-align: center">
                <input type="text" class="popup-group-input" placeholder="please input">
            </div>

            <div class="line"></div>

            <div class="" style="text-align:center;">
                <?php if ($lang == "1") { ?>
                    <button type="button" class="create_button" url-value="">保存</button>
                <?php } else { ?>
                    <button type="button" class="create_button" url-value="">Save</button>
                <?php } ?>
            </div>

        </div>

    </div>


    <script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="../../public/jquery/jquery-weui.min.js"></script>
    <script type="text/javascript" src="../../public/manage/native.js"></script>

    <script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

    <script type="text/javascript">

        function uploadMiniProgramIcon(obj) {

            if (isAndroid()) {
                zalyjsImageUpload(uploadImageResult);
            } else {
                $("#" + obj).val("");
                $("#" + obj).click();
            }

        }

        function uploadImageResult(result) {

            var fileId = result.fileId;

            var newSrc = "/_api_file_download_/?fileId=" + fileId;

            $(".site-image").attr("src", newSrc);
        }

        downloadFileUrl = "./index.php?action=http.file.downloadFile";


        function uploadImageFile(obj) {
            if (obj) {
                if (obj.files) {
                    var formData = new FormData();

                    formData.append("file", obj.files.item(0));
                    formData.append("fileType", "FileImage");
                    formData.append("isMessageAttachment", false);

                    var src = window.URL.createObjectURL(obj.files.item(0));

                    uploadFileToServer(formData, src);

                    $("#mini-program-image").attr("src", src);
                }
                return obj.value;
            }
        }

        function uploadFileToServer(formData, src) {

            var url = "./index.php?action=http.file.uploadWeb";

            if (isMobile()) {
                url = "/_api_file_upload_/?fileType=1";  //fileType=1,表示文件
            }

            $.ajax({
                url: url,
                type: "post",
                data: formData,
                contentType: false,
                processData: false,
                success: function (imageFileIdResult) {
                    if (imageFileIdResult) {
                        var fileId = imageFileIdResult;
                        if (isMobile()) {
                            var res = JSON.parse(imageFileIdResult);
                            fileId = res.fileId;
                        }
                        // updateServerImage(fileId);
                    } else {
                        alert(getLanguage() == 1 ? "上传返回结果空 " : "empty response");
                    }
                },
                error: function (err) {
                    alert("update image error");
                    return false;
                }
            });
        }

        function showLocalImage(fileId) {
            var requestUrl = downloadFileUrl + "&fileId=" + fileId + "&returnBase64=0";
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && (this.status == 200 || this.status == 304)) {
                    var blob = this.response;
                    var src = window.URL.createObjectURL(blob);
                    console.log("showSiteLogo imageId response src=" + src);
                    $("#mini-program-image").attr("src", src);
                }
            };
            xhttp.open("GET", requestUrl, true);
            xhttp.responseType = "blob";
            // xhttp.setRequestHeader('Cache-Control', "max-age=2592000, public");
            xhttp.send();
        }


        function selectMiniProgramUsage() {
            // PluginUsageIndex = 1;
            // PluginUsageLogin = 2;
            //
            // PluginUsageU2Message = 3;
            // PluginUsageTmpMessage = 4;
            // PluginUsageGroupMessage = 5;
            var language = getLanguage();

            $.actions({
                title: language == 0 ? "select mini program type" : "请选择小程序类别",
                onClose: function () {
                    console.log("close");
                },
                actions: [{
                    text: language == 0 ? "Home Mini Program" : "首页小程序",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#mini-program-usage-text").html(language == 0 ? "Home Page" : "首页小程序");
                        $(".mini-program-usage").attr("data", "1");
                    }
                }, {
                    text: language == 0 ? "Login Mini Program" : "登陆小程序",
                    // className: "color-warning weui-dialog__btn",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#mini-program-usage-text").html(language == 0 ? "Login Mini Program" : "登陆小程序");
                        $(".mini-program-usage").attr("data", "2");
                    }
                }, {
                    text: language == 0 ? "U2 Chat Mini Program" : "二人聊天小程序",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#mini-program-usage-text").html(language == 0 ? "U2 Chat Mini Program" : "二人聊天小程序");
                        $(".mini-program-usage").attr("data", "3");
                    }
                },
                    // {
                    //     text: language == 0 ? "Tmp Chat Mini Program" : "临时会话小程序",
                    //     className: "select-color-primary",
                    //     onClick: function () {
                    //
                    //         $("#mini-program-usage-text").html(language == 0 ? "Tmp Chat Mini Program" : "临时会话小程序");
                    //         $(".mini-program-usage").attr("data", "4");
                    //     }
                    // },
                    {
                        text: language == 0 ? "Group Chat Mini Program" : "群组聊天小程序",
                        className: "select-color-primary",
                        onClick: function () {
                            $("#mini-program-usage-text").html(language == 0 ? "Group Chat Mini Program" : "群组聊天小程序");
                            $(".mini-program-usage").attr("data", "5");
                        }
                    }, {
                        text: language == 0 ? "Account Mini Program" : "账户安全小程序",
                        className: "select-color-primary",
                        onClick: function () {
                            $("#mini-program-usage-text").html(language == 0 ? "Account Mini Program" : "账户安全小程序");
                            $(".mini-program-usage").attr("data", "6");
                        }
                    }, {
                        text: language == 0 ? "Invalid Mini Program" : "无效小程序",
                        className: "select-color-primary",
                        onClick: function () {
                            $("#mini-program-usage-text").html(language == 0 ? "Invalid Mini Program" : "无效小程序");
                            $(".mini-program-usage").attr("data", "0");
                            updateMiniProgramProfile("usageType", "0");
                        }
                    }]
            });
        }

        function selectMiniProgramDisplay() {
            var language = getLanguage();
            // PluginLoadingNewPage = 0;
            // PluginLoadingFloat   = 1;
            // PluginLoadingMask    = 2;
            // PluginLoadingChatbox = 3;
            // PluginLoadingFullScreen = 4;
            $.actions({
                title: language == 0 ? "select mini program open way" : "选择小程序打开方式",
                onClose: function () {
                    console.log("close");
                },
                actions: [{
                    text: language == 0 ? "New Page" : "新页面打开",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#mini-program-display-text").html(language == 0 ? "New Page" : "新页面打开");
                        $(".mini-program-display").attr("data", "0");
                    }
                }, {
                    text: language == 0 ? "Float Page" : "悬浮打开",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#mini-program-display-text").html(language == 0 ? "Float Page" : "悬浮打开打开");
                        $(".mini-program-display").attr("data", "1");
                    }
                },
                    // {
                    //     text: language == 0 ? "Mask Page" : "Mask打开",
                    //     className: "select-color-primary",
                    //     onClick: function () {
                    //         $("#mini-program-display-text").html(language == 0 ? "Mask Page" : "Mask打开");
                    //         $(".mini-program-display").attr("data", "2");
                    //     }
                    // },
                    {
                        text: language == 0 ? "Chatbox Page" : "新页面打开",
                        className: "select-color-primary",
                        onClick: function () {
                            $("#mini-program-display-text").html(language == 0 ? "Chatbox Page" : "新页面打开");
                            $(".mini-program-display").attr("data", "3");
                        }
                    },
                    // {
                    //     text: language == 0 ? "FullScreen" : "全屏打开",
                    //     className: "select-color-primary",
                    //     onClick: function () {
                    //         $("#mini-program-display-text").html(language == 0 ? "FullScreen" : "全屏打开");
                    //         $(".mini-program-display").attr("data", "4");
                    //     }
                    // }
                ]
            });
        }


        $("#addMiniProgramButton").click(function () {
            var miniProgramName = $("#mini-program-name-id").val();

            var imageFileId = $("#mini-program-fileid").attr("fileId");

            var landingPageUrl = $("#mini-program-landing-id").val();
            // var miniProgramProxySwitch = $("#openProxySwitch-id").is(':checked');

            var miniProgramUsage = $(".mini-program-usage").attr('data');
            var miniProgramOrder = $("#mini-program-order-input").val();
            var miniProgramDisplay = $("#mini-program-display").attr('data');
            var miniProgramSecretKey = $("#mini-program-secret-key-switch").is(':checked');

            var miniProgramManagement = $("#mini-program-management").val();

            var miniProgramPermission = $("#mini-program-permission").attr('data');

            if (miniProgramName == null || miniProgramName == "") {
                alert(getLanguage() == 1 ? "请输入小程序名称" : "please input mini program name");
                alert("mini program name must not be null");
                return;
            }

            if (landingPageUrl == null || landingPageUrl == "") {
                alert(getLanguage() == 1 ? "请输入小程序落地页" : "mini program landing url is empty");
                return;
            }

            // if (imageFileId == null || imageFileId == "") {
            //     alert(getLanguage() == 1 ? "请重新选择小程序图标" : "mini program icon is empty");
            //     return;
            // }

            var data = {
                // name: miniProgramName
            };
            data['name'] = miniProgramName;
            data['logo'] = imageFileId;
            data['landingPageUrl'] = landingPageUrl;
            // if (miniProgramProxySwitch) {
            //     data['withProxy'] = 1;
            // } else {
            //     data['withProxy'] = 0;
            // }
            data['withProxy'] = 0;
            data['usageType'] = miniProgramUsage;
            data['order'] = miniProgramOrder;
            data['loadingType'] = miniProgramDisplay;

            if (miniProgramPermission) {
                data['permissionType'] = miniProgramPermission;//all
            } else {
                data['permissionType'] = 0;//all
            }

            if (miniProgramSecretKey) {
                data['secretKey'] = 1;
            } else {
                data['secretKey'] = 0;
            }
            data['management'] = miniProgramManagement;


            var url = "./index.php?action=manage.miniProgram.add&type=save&lang=" + getLanguage();
            zalyjsCommonAjaxPostJson(url, data, addMiniProgramResponse);

        });

        function addMiniProgramResponse(url, data, result) {
            if (result) {
                var resJson = JSON.parse(result);

                var errCode = resJson['errCode'];

                if ("success" == errCode) {
                    alert(getLanguage() == 1 ? "添加成功" : "Add Success");
                    window.location.reload();
                } else {
                    var errInfo = resJson['errInfo'];
                    alert("error:" + errInfo);
                }
            } else {
                alert("error");
            }
        }

        function selectMiniProgramPermission() {
            var language = getLanguage();
            // PluginPermissionAdminOnly   = 0;
            // PluginPermissionAll     = 1;
            // PluginPermissionGroupMaster = 2;

            $.actions({
                title: language == 0 ? "select mini program permission" : "请选择小程序权限",
                onClose: function () {
                    console.log("close");
                },
                actions: [{
                    text: language == 0 ? "All User Available" : "所有人可用",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#mini-program-permission-text").html(language == 0 ? "All User Available" : "所有人可用");
                        $(".mini-program-permission").attr("data", "1");
                        updateMiniProgramProfile("permissionType", "1");
                    }
                }, {
                    text: language == 0 ? "Group Master Available" : "群管理员可用",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#mini-program-permission-text").html(language == 0 ? "Group Master Available" : "群管理员可用");
                        $(".mini-program-permission").attr("data", "2");
                        updateMiniProgramProfile("permissionType", "2");
                    }
                }, {
                    text: language == 0 ? "Site Managers Available" : "站点管理员可用",
                    className: "select-color-primary",
                    onClick: function () {
                        $("#mini-program-permission-text").html(language == 0 ? "Site Managers Available" : "站点管理员可用");
                        $(".mini-program-permission").attr("data", "0");
                        updateMiniProgramProfile("permissionType", "0");
                    }
                }]
            });
        }

    </script>

</body>
</html>





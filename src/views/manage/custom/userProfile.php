<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>用户资料字段<?php } else { ?> User Profile Column<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>
    <link rel="stylesheet" href="../../public/manage/config.css"/>

    <style>

        .save_button,
        .save_button:hover,
        .save_button:focus,
        .save_button:active {
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

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row" id="custom-key" data="<?php echo $userCustomInfo["customKey"]; ?>">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">自定义字段</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Custom Key</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value"
                                 id="custom-key-value"><?php echo $userCustomInfo["customKey"]; ?></div>
                            <div class="item-body-value-more">
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row" id="custom-key-name" update-key="keyName" onclick="showPopup('custom-key-name');">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">字段名称</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Key Name</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value"><?php echo $userCustomInfo["keyName"]; ?></div>
                            <div class="item-body-value-more">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row" id="custom-key-desc" update-key="keyDesc" onclick="showPopup('custom-key-desc');">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">字段描述</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Custom Desc</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value"><?php echo $userCustomInfo["keyDesc"]; ?></div>
                            <div class="item-body-value-more">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>


            <div class="item-row" id="custom-key-icon">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">字段图标</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Key Icon</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value">
                                <img class="site-image" id="custom-key-image" onclick="uploadImage('custom-key-logo');"
                                     fileId="<?php echo $userCustomInfo["keyIcon"]; ?>"
                                     src="../../public/img/manage/plugin_default.png">

                                <input id="custom-key-logo" type="file" onchange="uploadImageFile(this)"
                                       style="display: none;" accept="image/*">
                            </div>

                            <div class="item-body-value-more">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row" id="custom-key-sort" update-key="keySort" onclick="showPopup('custom-key-sort');">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">排序</div>
                        <?php } else { ?>
                            <div class="item-body-desc">KeySort</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value"><?php echo $userCustomInfo["keySort"]; ?></div>
                            <div class="item-body-value-more">
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
                    <div class="item-body-display custom-key-open">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">是否公开显示</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Public Display</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <?php if ($userCustomInfo["isOpen"] == 1) { ?>
                                <input id="custom-key-open-switch" class="weui_switch" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="custom-key-open-switch" class="weui_switch" type="checkbox">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display custom-key-status">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">是否注册时填写</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Register Need</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <?php if ($userCustomInfo["status"] == 2) { ?>
                                <input id="custom-key-status-switch" class="weui_switch" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="custom-key-status-switch" class="weui_switch" type="checkbox">
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display custom-key-required">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">是否必填</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Required</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <?php if ($userCustomInfo["isRequired"] == 1) { ?>
                                <input id="custom-key-required-switch" class="weui_switch" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="custom-key-required-switch" class="weui_switch" type="checkbox">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display custom-key-constraint">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">是否创建搜索索引</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Create Search Index</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <?php if ($userCustomInfo["keyConstraint"] == 1) { ?>
                                <input id="custom-key-constraint-switch" class="weui_switch" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="custom-key-constraint-switch" class="weui_switch" type="checkbox">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="division-line"></div>

        </div>

    </div>


    <?php if (!in_array($userCustomInfo["customKey"], ["phoneId", "email"])) { ?>

        <div class="layout-all-row">

            <div class="list-item-center">

                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display" onclick="deleteUserCostom();">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">删除</div>
                            <?php } else { ?>
                                <div class="item-body-desc">Delete</div>
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
    <?php } ?>
</div>

<div class="wrapper-mask" id="wrapper-mask" style="visibility: hidden;"></div>

<div class="popup-template" style="display: none;">

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
                <button type="button" id="updatePopupButton" class="create_button" onclick="updateDataValue()"
                        url-value="">保存
                </button>
            <?php } else { ?>
                <button type="button" id="updatePopupButton" class="create_button" onclick="updateDataValue()"
                        url-value="">Save
                </button>
            <?php } ?>
        </div>

    </div>

</div>


<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/jquery/jquery-weui.min.js"></script>
<script type="text/javascript" src="../../public/js/jquery-confirm.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

<script type="text/javascript">

    $(".site-image").each(function () {
        var avatar = $(this).attr("fileId");
        var src = " /_api_file_download_/?fileId=" + avatar;
        if (!isMobile()) {
            src = "./index.php?action=http.file.downloadFile&fileId=" + avatar + "&returnBase64=0";
        }
        $(this).attr("src", src);
    });

    function uploadImage(obj) {
        if (isAndroid()) {
            zalyjsImageUpload(uploadImageResult);
        } else {
            $("#" + obj).val("");
            $("#" + obj).click();
        }
    }

    function uploadImageResult(result) {

        var fileId = result.fileId;

        updateServerImage(fileId);

        var newSrc = "/_api_file_download_/?fileId=" + fileId;

        $(".site-image").attr("src", newSrc);
        $(".site-image").attr("fileId", fileId);
    }

    downloadFileUrl = "./index.php?action=http.file.downloadFile";


    function uploadImageFile(obj) {
        if (obj) {
            if (obj.files) {
                var formData = new FormData();

                formData.append("file", obj.files.item(0));
                formData.append("fileType", 1);
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

        $.ajax({
            url: url,
            type: "post",
            data: formData,
            contentType: false,
            processData: false,
            success: function (imageFileIdResult) {
                if (imageFileIdResult) {
                    var res = JSON.parse(imageFileIdResult);
                    var fileId = res.fileId;
                    updateServerImage(fileId);
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

    function updateServerImage(fileId) {
        var customKey = $("#custom-key").attr("data");
        var url = "index.php?action=manage.custom.userUpdate";
        var data = {
            'customKey': customKey,
            'updateKey': 'keyIcon',
            'updateValue': fileId,
        };

        zalyjsCommonAjaxPostJson(url, data, updateResponse);
    }

    function showLocalImage(fileId) {
        var requestUrl = downloadFileUrl + "&fileId=" + fileId + "&returnBase64=0";
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && (this.status == 200 || this.status == 304)) {
                var blob = this.response;
                var src = window.URL.createObjectURL(blob);
                $("#mini-program-image").attr("src", src);
            }
        };
        xhttp.open("GET", requestUrl, true);
        xhttp.responseType = "blob";
        // xhttp.setRequestHeader('Cache-Control', "max-age=2592000, public");
        xhttp.send();
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
        var customKey = $("#custom-key").attr("data");
        var key = $("#updatePopupButton").attr("key-value");
        var updateValue = $.trim($(".popup-group-input").val());
        var updateKey = $("#" + key).attr("update-key");
        // alert("updateKey=" + updateKey + " updateValue" + updateValue);

        var data = {
            'customKey': customKey,
            'updateKey': updateKey,
            'updateValue': updateValue,
        };

        var url = "index.php?action=manage.custom.userUpdate";

        zalyjsCommonAjaxPostJson(url, data, updateResponse);
        // close
        removeWindow($(".config-hidden"));
    }

    function updateResponse(url, data, result) {
        if (result) {

            var res = JSON.parse(result);

            if ("success" != res.errCode) {
                var errInfo = res.errInfo;
                var errMsg = (getLanguage() == 1 ? "操作失败,原因：" : "update error, cause:") + errInfo;
                alert(errMsg);
            } else {
                window.location.reload();
            }

        } else {
            alert(getLanguage() == 1 ? "操作失败" : "update error");
        }
    }

    function showPopup(showId) {
        var title = $("#" + showId).find(".item-body-desc").html();
        var inputBody = $("#" + showId).find(".item-body-value").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", showId);
    }


    $("#custom-key-status-switch").change(function () {
        var customKey = $("#custom-key").attr("data");
        var isChecked = $(this).is(':checked');
        var url = "index.php?action=manage.custom.userUpdate";
        var data = {
            'customKey': customKey,
            'updateKey': 'status',
            'updateValue': isChecked ? 2 : 1,
        };

        zalyjsCommonAjaxPostJson(url, data, enableSwitchResponse);

    });

    $("#custom-key-required-switch").change(function () {
        var customKey = $("#custom-key").attr("data");
        var isChecked = $(this).is(':checked');
        var url = "index.php?action=manage.custom.userUpdate";
        var data = {
            'customKey': customKey,
            'updateKey': 'isRequired',
            'updateValue': isChecked ? 1 : 0,
        };

        zalyjsCommonAjaxPostJson(url, data, enableSwitchResponse);

    });

    $("#custom-key-open-switch").change(function () {
        var customKey = $("#custom-key").attr("data");
        var isChecked = $(this).is(':checked');
        var url = "index.php?action=manage.custom.userUpdate";
        var data = {
            'customKey': customKey,
            'updateKey': 'isOpen',
            'updateValue': isChecked ? 1 : 0,
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

    function deleteUserCostom() {

        var lang = getLanguage();
        $.modal({
            title: lang == 1 ? '删除字段' : 'Delete Column',
            text: lang == 1 ? '确定删除？' : 'Confirm Delete?',
            buttons: [
                {
                    text: lang == 1 ? "取消" : "cancel", className: "default", onClick: function () {
                        // alert("cancel");
                    }
                },
                {
                    className: "select-color-primary",
                    text: lang == 1 ? "确定" : "confirm", onClick: function () {
                        var customKey = $("#custom-key").attr("data");
                        var url = "index.php?action=manage.custom.userDelete";

                        var data = {
                            'customKey': customKey,
                        };

                        zalyjsCommonAjaxPostJson(url, data, deleteResponse);
                    }
                },

            ]
        });

    }

    function deleteResponse(url, data, result) {
        if (result) {
            var res = JSON.parse(result);

            if ("success" != res.errCode) {
                var errInfo = res.errInfo;
                var errMsg = (getLanguage() == 1 ? "删除失败,原因：" : "update error, cause:") + errInfo;
                alert(errMsg);
            } else {
                var url = "index.php?action=manage.custom.user";
                zalyjsOpenPage(url);
            }

        } else {
            alert(getLanguage() == 1 ? "删除失败" : "delete error");
        }
    }

</script>

</body>
</html>





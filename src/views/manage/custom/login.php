<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>登陆配置<?php } else { ?>Login Settings<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>

    <link rel="stylesheet" href="../../public/manage/config.css"/>

    <style>
        .color-picker {
            width: 100px;
            height: 35px;
            border: 1px solid #10aeff;
            outline: none;
            cursor: pointer;
            font-size: 14px;
        }

        /*填充*/
        .login-background-image-0 {
            margin: 5px 10px 5px 10px;
            height: 200px;
            /*background-color: #0bb20c;*/
            background-size: cover;
            background-attachment: fixed;
        }

        /*拉伸*/
        .login-background-image-1 {
            margin: 5px 10px 5px 10px;
            height: 200px;
            background-position: 0 0;
            background-size: 100% 100%;
        }

        .login-background-image-2 {
            margin: 5px 10px 5px 10px;
            height: 200px;
            background-repeat: repeat;
        }

        .item-row, .create_button {
            cursor: pointer;
            outline: none;
        }

    </style>

</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row" id="site-name" onclick="showLoginWelcomeText()">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">登陆页欢迎文案</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Login Page Introduction</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value"><?php echo $loginWelcomeText; ?></div>
                            <div class="item-body-value">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">登陆页背景颜色</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Login Background Color</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <input class="color-picker" type="color" id="html5colorpicker"
                                   onchange="clickColor(0, -1, -1, 5)" value="<?php echo $loginBackgroundColor; ?>">
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
                    <div class="item-body-display" onclick="uploadFile('upload-background-image')">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">登陆页背景图片</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Login Page Background-image</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="login-background-id"
                 class="login-background-image-<?php echo $loginBackgroundImageDisplay ?> image-bg"
                 style="background-image: url('/_api_file_download_/?fileId=<?php echo $loginBackgroundImage ?>');"
                 bgImgId="<?php echo $loginBackgroundImage ?>">
                <input id="upload-background-image" type="file" onchange="uploadImageFile(this)"
                       accept="image/gif,image/jpeg,image/jpg,image/png,image/svg"
                       style="display: none;">
            </div>

            <div class="item-row">
                <div class="item-body" id="image-display-type" data="<?php echo $loginBackgroundImageDisplay ?>">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">登陆页背景图片模式</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Login Page backgroud display mode</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value" id="image-display-type-text">
                                <?php if ($loginBackgroundImageDisplay == "0") { ?>
                                    <?php if ($lang == "1") { ?> 默认(填充)<?php } else { ?> Default(Cover) <?php } ?>
                                <?php } else if ($loginBackgroundImageDisplay == "1") { ?>
                                    <?php if ($lang == "1") { ?> 拉伸<?php } else { ?>Fill<?php } ?>
                                <?php } else if ($loginBackgroundImageDisplay == "2") { ?>
                                    <?php if ($lang == "1") { ?> 平铺<?php } else { ?> Repeat <?php } ?>
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
                <button id="updatePopupButton" type="button" class="create_button" style="cursor: pointer;outline: none"
                        key-value=""
                        onclick="updateDataValue();">确认
                </button>
            <?php } else { ?>
                <button id="updatePopupButton" type="button" class="create_button" style="cursor: pointer;outline: none"
                        key-value=""
                        onclick="updateDataValue();">Confirm
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

    function uploadFile(obj) {
        if (isMobile()) {
            zalyjsImageUpload(uploadImageResult);
        } else {
            $("#" + obj).val("");
            $("#" + obj).click();
        }
    }

    function uploadImageResult(result) {

        var fileId = result.fileId;

        //update server image
        updateLoginBackgroundImage(fileId);

        var url = "/_api_file_download_/?fileId=" + fileId;
        $("#login-background-id").css("background-image", "url('" + url + "')")
    }


    function uploadImageFile(obj) {

        if (obj) {
            if (obj.files) {
                var formData = new FormData();

                formData.append("file", obj.files.item(0));
                formData.append("fileType", 1);
                formData.append("isMessageAttachment", false);

                var src = window.URL.createObjectURL(obj.files.item(0));

                uploadFileToServer(formData, src);

                //上传以后本地展示的
                $(".site-logo-image").attr("src", src);
            }
            return obj.value;
        }

    }

    $(".image-bg").each(function () {
        if (!isMobile()) {
            var imgId = $(this).attr("bgImgId");
            var src = "./index.php?action=http.file.downloadFile&fileId=" + imgId + "&returnBase64=0";
            $(".image-bg")[0].style.backgroundImage = " url('" + src + "')";
        }
    });


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
                    var res = JSON.parse(imageFileIdResult);
                    var fileId = res.fileId;
                    updateLoginBackgroundImage(fileId);
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

    function updateLoginBackgroundImage(imageFileId) {
        var url = "index.php?action=manage.custom.login&lang=" + getLanguage();
        var data = {
            'key': 'loginBackgroundImage',
            'value': imageFileId,
        };
        zalyjsCommonAjaxPostJson(url, data, updateImageResponse);
    }

    function updateImageResponse(url, data, result) {
        var res = JSON.parse(result);

        if (res.errCode) {
            window.location.reload();
        } else {
            alert("errorInfo:" + res.errInfo);
        }
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

        var url = "index.php?action=manage.custom.login&lang=" + getLanguage();

        var value = $.trim($(".popup-group-input").val());

        var data = {
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

    function showLoginWelcomeText() {
        var title = $("#site-name").find(".item-body-desc").html();
        var inputBody = $("#site-name").find(".item-body-value").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", "loginWelcomeText");
    }


    $("#image-display-type").click(function () {
        var language = getLanguage();

        $.actions({
            title: "",
            onClose: function () {
                console.log("close");
            },
            actions: [{
                text: language == 0 ? "Default(Cover)" : "默认(填充)",
                className: "select-color-primary",
                onClick: function () {
                    $("#image-display-type-text").html(language == 0 ? "Default" : "默认");
                    $("#image-display-type").attr("data", "0");
                    updateImageDisplayType(0);
                }
            }, {
                text: language == 0 ? "Fill" : "拉伸",
                className: "select-color-primary",
                onClick: function () {
                    $("#image-display-type-text").html(language == 0 ? "Cover" : "填充");
                    $("#image-display-type").attr("data", "1");
                    updateImageDisplayType(1);
                }
            }, {
                text: language == 0 ? "Repeat" : "平铺",
                className: "select-color-primary",
                onClick: function () {
                    $("#image-display-type-text").html(language == 0 ? "Repeat" : "平铺");
                    $("#image-display-type").attr("data", "2");

                    updateImageDisplayType(2);
                }
            }]
        });
    });

    //update push notice type
    function updateImageDisplayType(displayValue) {
        var url = "index.php?action=manage.custom.login&lang=" + getLanguage();
        var data = {
            'key': 'loginBackgroundImageDisplay',
            'value': displayValue,
        };

        zalyjsCommonAjaxPostJson(url, data, updateResponse);
    }


    function updateResponse(url, data, result) {
        if (result) {

            var res = JSON.parse(result);

            if (!"success" == res.errCode) {
                alert(getLanguage() == 1 ? "操作失败" : "update error");
            }

        } else {
            alert(getLanguage() == 1 ? "操作失败" : "update error");
        }
        window.location.reload();
    }

    function clickColor(hex, seltop, selleft, html5) {

        var color;
        if (html5 && html5 == 5) {
            color = document.getElementById("html5colorpicker").value;
        } else {
            if (hex == 0) {
                color = document.getElementById("entercolor").value;
            } else {
                color = hex;
            }
        }

        var url = "index.php?action=manage.custom.login&lang=" + getLanguage();
        var data = {
            'key': 'loginBackgroundColor',
            'value': color,
        };

        zalyjsCommonAjaxPostJson(url, data, updateResponse);
    }

</script>


</body>
</html>





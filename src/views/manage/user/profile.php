<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>用户资料<?php } else { ?>User Profile<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>
    <link rel="stylesheet" href="../../public/manage/config.css"/>
    <style>

        .site-user-avatar {
            width: 30px;
            height: 30px;
            margin-top: 12px;
            border-radius: 1px;
            cursor: pointer;
        }

        .item-row, .weui_switch {
            cursor: pointer;
        }
    </style>

</head>

<body>

<!--<div class="wrapper-mask" id="wrapper-mask" style="visibility: hidden;"></div>-->

<div class="wrapper" id="wrapper">

    <!--  site basic config  -->
    <div class="layout-all-row" id="user-id" data="<?php echo $userId; ?>">

        <div class="list-item-center">

            <div class="item-row" onclick="showUserId('<?php echo $userId; ?>')">
                <div class="item-body">
                    <div class="item-body-display user-id-body">
                        <div class="item-body-desc">ID</div>

                        <div class="item-body-tail" id="user-id-value">
                            <div class="item-body-value"><?php
                                if (isset($userId)) {
                                    $subUserId = substr($userId, 0, 4) . " **** ";
                                    $subUserId .= substr($userId, -4);
                                    echo $subUserId;
                                }
                                ?></div>
                            <div class="item-body-value">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row" id="user-nickname">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">用户昵称</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Nickname</div>
                        <?php } ?>

                        <div class="item-body-tail" id="user-nickname-text">
                            <div class="item-body-value font-size-12"><?php echo $nickname; ?></div>
                            <div class="item-body-value">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>


            <!--      part1: site name      -->
            <div class="item-row" id="user-loginName-name">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">用户名</div>
                        <?php } else { ?>
                            <div class="item-body-desc ">LoginName</div>
                        <?php } ?>


                        <div class="item-body-tail" id="user-nickname-text">
                            <div class="item-body-value font-size-12"><?php echo $loginName; ?></div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">用户头像</div>
                        <?php } else { ?>
                            <div class="item-body-desc">User Avatar</div>
                        <?php } ?>


                        <div class="item-body-tail" id="user-avatar-img-id" fileId="<?php echo $avatar ?>">
                            <div class="item-body-value">
                                <img id="user-avatar-img" class="site-user-avatar"
                                     onclick="uploadFile('user-avatar-img-input')"
                                     avatar="<?php echo $avatar ?>"
                                     src=""
                                     onerror="src='../../public/img/msg/default_user.png'">

                                <input id="user-avatar-img-input" type="file" onchange="uploadImageFile(this)"
                                       accept="image/gif,image/jpeg,image/jpg,image/png,image/svg"
                                       style="display: none;">
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


    <!-- part 2  register && login plugin-->
    <div class="layout-all-row">

        <div class="list-item-center">

            <?php if (!$isSiteOwner) { ?>

                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">设为站点管理员</div>
                            <?php } else { ?>
                                <div class="item-body-desc">Add Site Manager</div>
                            <?php } ?>


                            <div class="item-body-tail">
                                <?php if ($isSiteManager == 1) { ?>
                                    <input id="addSiteManagerSwitch" class="weui_switch" type="checkbox" checked>
                                <?php } else { ?>
                                    <input id="addSiteManagerSwitch" class="weui_switch" type="checkbox">
                                <?php } ?>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="division-line"></div>
            <?php } ?>

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">设为站点默认好友</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Add Site Default Friend</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <?php if ($isDefaultFriend == 1) { ?>
                                <input id="addDefaultFriendSwitch" class="weui_switch" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="addDefaultFriendSwitch" class="weui_switch" type="checkbox">
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

        </div>

    </div>


    <!--   part 3  -->
    <div class="layout-all-row">

        <div class="list-item-center">
            <div class="item-row" id="user-group-list">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">用户群组列表</div>
                        <?php } else { ?>
                            <div class="item-body-desc">User Group List</div>
                        <?php } ?>

                        <div class="item-body-tail">
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


    <!--   part 4  -->
    <div class="layout-all-row">
        <div class="list-item-center">

            <div class="item-row" id="change-user-password" onclick="changeUserPassword()">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">修改用户密码</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Change Password</div>
                        <?php } ?>

                        <div class="item-body-tail">
                            <div class="item-body-value"></div>

                            <div class="item-body-value-more">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>


            <div class="item-row" id="remove-user" onclick="deleteUserAccount()">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">删除用户账号</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Remove Account</div>
                        <?php } ?>

                        <div class="item-body-tail">
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
            <div class="header_tip_font popup-group-title">创建群组</div>
        </div>

        <div class="" style="text-align: center">
            <input type="text" class="popup-group-input"
                   data-local-placeholder="enterGroupNamePlaceholder" placeholder="please input">
        </div>

        <div class="line"></div>

        <div class="" style="text-align:center;">
            <?php if ($lang == "1") { ?>
                <button id="update-user-button" type="button" class="create_button" data=""
                        onclick="updateConfirm();"> 修改
                </button>
            <?php } else { ?>
                <button id="update-user-button" type="button" class="create_button" data=""
                        onclick="updateConfirm();">Update
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
            zalyjsImageUpload(uploadAvatarImageResult);
        } else {
            $("#" + obj).val("");
            $("#" + obj).click();
        }
    }


    function uploadAvatarImageResult(result) {

        var fileId = result.fileId;

        //update server image
        updateServerImage(fileId);

        var newSrc = "/_api_file_download_/?fileId=" + fileId;
        if (!isMobile()) {
            newSrc = "./index.php?action=http.file.downloadFile&fileId=" + fileId + "&returnBase64=0";
        }
        $(".site-user-avatar").attr("src", newSrc);
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

                //直接放图片
                $("#user-avatar-img").attr("src", src);
            }
            return obj.value;
        }

    }

    $(".site-user-avatar").each(function () {
        var avatar = $(this).attr("avatar");
        var src = " /_api_file_download_/?fileId=" + avatar;
        if (!isMobile()) {
            src = "./index.php?action=http.file.downloadFile&fileId=" + avatar + "&returnBase64=0";
        }
        $(this).attr("src", src);
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
                    updateServerImage(fileId);
                } else {
                    alert(getLanguage() == 1 ? "上传返回结果空 " : "empty response");
                }
            },
            error: function (err) {
                alert("update image error");
                // return false;
            }
        });
    }

    function updateServerImage(fileId) {
        var userId = $("#user-id").attr("data");
        var url = "index.php?action=manage.user.update&lang=" + getLanguage();

        var data = {
            'userId': userId,
            'key': 'avatar',
            'value': fileId,
        };
        zalyjsCommonAjaxPostJson(url, data, updateAvatarResponse);
    }

    function updateAvatarResponse(url, data, result) {
        var res = JSON.parse(result);
        if (res.errCode != "success") {
            return getLanguage() == 1 ? "更新头像失败" : "update user avatar fail";
        }
    }

</script>


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


    function showUserId(userId) {
        var url = "index.php?action=manage.user.id&userId=" + userId + "&lang=" + getLanguage();
        zalyjsCommonOpenPage(url);
    }


    $("#user-nickname").click(function () {
        var title = $(this).find(".item-body-desc").html();
        var inputBody = $(this).find(".item-body-value").html();

        $("#update-user-button").attr("data", "nickname");
        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);

    });

    // $("#user-loginName").click(function () {
    //     var title = $(this).find(".item-body-desc").html();
    //     var inputBody = $(this).find(".item-body-value").html();
    //
    //     $("#update-user-button").attr("data", "loginName");
    //     showWindow($(".config-hidden"));
    //
    //     $(".popup-group-title").html(title);
    //     $(".popup-group-input").val(inputBody);
    // });


    $("#change-user-password").click(function () {
        var title = $(this).find(".item-body-desc").html();
        var inputBody = $(this).find(".item-body-value").html();

        $("#update-user-button").attr("data", "changePassword");
        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
    });

    function updateConfirm() {
        var userId = $("#user-id").attr("data");
        var value = $(".popup-group-input").val();
        var nameData = $("#update-user-button").attr("data");

        if (nameData == null || nameData == "") {
            alert("update fail");
            return;
        }

        // if(nameData == "loginName" && (value.length > 16  || value.length <1)) {
        //     alert("用户名长度1到16个字符");
        //     return;
        // }

        if(nameData == "nickname" && (value.length > 16  || value.length <1)) {
            alert("用户昵称长度1到16个字符");
            return;
        }

        var data = {
            'userId': userId,
            'key': nameData,
            'value': value
        };

        var url = "index.php?action=manage.user.update&lang=" + getLanguage();

        zalyjsCommonAjaxPostJson(url, data, updateNameResponse);

        removeWindow($(".config-hidden"));
    }

    function updateNameResponse(url, data, result) {
        var res = JSON.parse(result);

        if (res.errCode != "success") {
            alert(getLanguage() == 1 ? "更新失败" : "update name error");
            location.reload();
        } else {
            if (data.key == "changePassword") {
                alert(getLanguage() == 1 ? "修改密码成功" : "change password success");
            } else {
                location.reload();
            }
        }
    }

    //enable realName
    $("#addSiteManagerSwitch").change(function () {

        var userId = $("#user-id").attr("data");
        var isChecked = $(this).is(':checked')
        var url = "index.php?action=manage.user.update&lang=" + getLanguage();


        var data = {
            'userId': userId,
            'key': 'addSiteManager',
            'value': isChecked ? 1 : 0,
        };

        zalyjsCommonAjaxPostJson(url, data, addManagerResponse);

    });

    function addManagerResponse(url, data, result) {
        var res = JSON.parse(result);

        if (res.errCode != "success") {
            alert(getLanguage() == 1 ? "更新失败" : "update error");
        }

    }


    $("#addDefaultFriendSwitch").change(function () {

        var userId = $("#user-id").attr("data");
        var isChecked = $(this).is(':checked')

        var url = "index.php?action=manage.user.update&lang=" + getLanguage();

        var data = {
            'userId': userId,
            'key': 'addDefaultFriend',
            'value': isChecked ? 1 : 0,
        };

        zalyjsCommonAjaxPostJson(url, data, addDefaultFriendResponse);
    });

    function addDefaultFriendResponse(url, data, result) {
        var res = JSON.parse(result);

        if (res.errCode != "success") {
            alert(getLanguage() == 1 ? "更新失败" : "update error");
        }
    }


    $("#user-group-list").click(function () {
        var userId = $("#user-id").attr("data");

        var url = "index.php?action=manage.user.groups&userId=" + userId + "&lang=" + getLanguage();

        zalyjsCommonOpenPage(url);
    });

    function deleteUserAccount() {

        var lang = getLanguage();
        $.modal({
            title: lang == 1 ? '删除用户' : 'Delete User',
            text: lang == 1 ? '确定删除？' : 'Confirm Delete?',
            buttons: [
                {
                    text: lang == 1 ? "取消" : "cancel", className: "default", onClick: function () {
                        // alert("cancel");
                    }
                },
                {
                    className: "select-color-primary",
                    text: lang == 1 ? "确定" : "confirm", className: "main-color", onClick: function () {
                        var userId = $("#user-id").attr("data");

                        var url = "index.php?action=manage.user.delete&lang=" + getLanguage();

                        var data = {
                            'deleteUserId': userId
                        };

                        zalyjsCommonAjaxPostJson(url, data, removeUserResponse);
                    }
                },

            ]
        });

    }

    function removeUserResponse(url, data, result) {
        var res = JSON.parse(result);

        if (res.errCode == "success") {
            var url = "index.php?action=manage.user&lang=" + getLanguage();
            location.href = url;
        } else {
            alert(getLanguage() == 1 ? "删除用户失败" : "update error");
        }

    }


</script>


</body>
</html>





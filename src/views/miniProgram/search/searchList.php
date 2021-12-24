<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>搜索列表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/manage/config.css"/>
    <link rel="stylesheet" href="../../public/manage/search.css"/>

    <style>
        .item-row-title {
            /*width: 100%;*/
            height: 20px;
            font-size: 14px;
            font-family: PingFangSC-Medium;
            font-weight: 500;
            color: rgba(153, 153, 153, 1);
            line-height: 20px;
            margin: 17px 0px 7px 10px;
        }

         .item-row {
            cursor: pointer;
        }
        #search-group-div {
            text-align: center;
        }
        .show_all_tip {
            height:12px;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(127,118,180,1);
            line-height:12px;
        }


        .item-body-display, .item-body-desc, .item-body, .group-list {
            height:56px;
            line-height: 56px;
        }

        .show_all_list_name {
            height: 30px;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(157,155,159,1);
            line-height:30px;
        }
        .height30{
            height: 30px;
            line-height: 30px;
        }
        .height36{
            height: 36px;
            line-height: 36px;
        }
        .user-avatar-image {
            width:40px;
            height:40px;
        }

        .item-header {
            height:56px;
            width: 60px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .applyButton, .chatButton, .addButton  {
            height:28px;
            background:rgba(76,59,177,1);
            border-radius:2px;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(255,255,255,1);
            line-height: 28px;
            cursor: pointer;
            outline: none;
            border:1px solid;
        }
        .disableButton {
            background: #cccccc;
        }
        .group_name, .group_owner {
            font-size:14px;
            height: 18px;
            line-height: 18px;
            text-align: left;
            margin-top: 5px;
        }
        .group_owner {
            font-size:10px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(153,153,153,1);
            line-height:28px;
        }
    </style>

</head>

<body>

<input type="hidden" value="<?php echo $key?>" class="search_key">
<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center" style="margin-top: 20px;">
            <div class="item-row-list">

                <div class="item-row group-list height30" >
                    <div class="item-body height30" >
                        <div class="item-body-display height30" >
                            <div class="item-body-desc show_all_list_name height30" >
                                用户列表
                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>

                <?php if(count($users)):?>
                    <?php foreach ($users as $user):?>
                    <div class="item-row group-list">
                        <div class="item-header">
                            <img class="user-avatar-image" avatar="<?php echo $user['avatar'] ?>"
                                 src=""
                                 onerror="this.src='../../public/img/msg/default_user.png'"/>
                        </div>
                        <div class="item-body" >
                            <div class="item-body-display">
                                <div class="item-body-desc" style="font-size: 10px;">
                                   <?php echo $user['nickname']; ?>
                                </div>
                                <div class="item-body-tail">
                                    <?php if($user['isFollow']):?>
                                        <button class="chatButton" userId="<?php echo $user["userId"] ?>">
                                            发起会话
                                        </button>
                                    <?php elseif(!$user['isFollow'] && ($user['userId'] != $token)): ?>
                                        <button class="addButton applyButton" userId="<?php echo $user["userId"] ?>">
                                            添加好友
                                        </button>

                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="division-line"></div>
                    <?php endforeach;?>

                <div class="division-line"></div>
                <div class="item-row group-list height36 show_all_friend">
                    <div class="item-body height36 " >
                        <div class="item-body-display height36 ">
                            <div class="item-body-desc show_all_tip height36 " >
                                查看更多好友
                            </div>

                            <div class="item-body-tail">
                                <div class="item-body-value">
                                    <img class="more-img" src="../../public/img/manage/more.png"/>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>

    </div>

    <div class="layout-all-row">

        <div class="list-item-center" style="margin-top: 20px;">
            <div class="item-row-list">

                <div class="item-row group-list height30" >
                    <div class="item-body height30" >
                        <div class="item-body-display height30" >
                            <div class="item-body-desc show_all_list_name height30" >
                                群组列表
                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>

                <?php if(count($groups)):?>
                <?php foreach ($groups as $group):?>
                        <div class="item-row group-list">
                            <div class="item-header">
                                <img class="user-avatar-image" avatar="<?php echo $group['avatar'] ?>"
                                     src=""
                                     onerror="this.src='../../public/img/msg/default_user.png'"/>
                            </div>
                            <div class="item-body">
                                <div class="item-body-display">
                                    <div class="item-body-desc">
                                        <div class="group_name">
                                            <?php echo $group['name'];?>
                                        </div>
                                        <div class="group_owner">
                                            群主：<?php echo $group['ownerName'];?>
                                        </div>
                                    </div>

                                    <div class="item-body-tail">
                                        <?php if($group['isMember'] == true):?>
                                            <button class="addButton disableButton <?php echo $group['groupId'];?> " groupId="<?php echo $group['groupId'];?>">
                                                已入群
                                            </button>
                                        <?php else :?>
                                            <?php if($group['permissionJoin'] == 0):?>
                                                <button class="addButton applyJoinButton <?php echo $group['groupId'];?> " groupId="<?php echo $group['groupId'];?>">
                                                    一键入群
                                                </button>
                                            <?php else: ?>
                                                <button class="addButton disableButton <?php echo $group['groupId'];?>" groupId="<?php echo $group['groupId'];?>">
                                                    非公开群
                                                </button>
                                            <?php endif;?>
                                        <?php endif;?>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="division-line"></div>
                    <?php endforeach;?>

                <div class="item-row group-list height36 show_all_group">
                    <div class="item-body height36" >
                        <div class="item-body-display height36">
                            <div class="item-body-desc show_all_tip height36" >
                                查看更多群组
                            </div>

                            <div class="item-body-tail">
                                <div class="item-body-value">
                                    <img class="more-img" src="../../public/img/manage/more.png"/>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endif;?>

            </div>

        </div>

    </div>
</div>

<input type="hidden" value="<?php echo $loginName;?>" id="myUserId">
<input type="hidden" value="<?php echo $token;?>" id="token">

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
                <button id="update-user-button" type="button" class="create_button" data=""
                        onclick="sendRequest();">发送
                </button>

        </div>

    </div>

</div>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>


<script type="text/javascript">


    $(".user-avatar-image").each(function () {
        var avatar = $(this).attr("avatar");
        var src = "./index.php?action=http.file.downloadFile&fileId=" + avatar + "&returnBase64=0";
        if (isMobile()) {
            var path = avatar.split("-");
            src = "../../attachment/"+path[0]+"/"+path[1];
        }

        $(this).attr("src", src);
    });

    $(".show_all_friend").on("click", function () {
        var param = $(".search_key").val();
        var url = "index.php?action=miniProgram.search.index&for=user&key="+param;
        zalyjsCommonOpenPage(url);
    });
    $(".show_all_group").on("click", function () {
        var param = $(".search_key").val();
        var url = "index.php?action=miniProgram.search.index&for=group&key="+param;
        zalyjsCommonOpenPage(url);
    });
    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return 1;
        }
        return 0;
    }


    $(document).on("click",".applyButton", function () {
        var lang = getLanguage();
        var myNickname = $("#myUserId").val();
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


    function sendRequest() {
        var friendUserId = $("#update-user-button").attr("data");
        var applyInfo = $(".popup-group-input").val();

        var data = {
            'friendId': friendUserId,
            'greeting': applyInfo
        };

        var url = "index.php?action=miniProgram.search.apply";
        zalyjsCommonAjaxPostJson(url, data, applyResponse)

        removeWindow($(".config-hidden"));
    }


    function applyResponse(url, data, result) {
        var res = JSON.parse(result);

        if (res.errCode != "success") {
            alert(res.errInfo);
        }
    }

    $(".applyJoinButton").on("click", function () {
        var groupId = $(this).attr("groupId");
        var data = {
            groupId:groupId
        };
        var searchKey = $(".search_key").val();
        var url = "index.php?action=miniProgram.search.index&for=joinGroup&key="+searchKey;
        zalyjsCommonAjaxPostJson(url, data, joinGroupResponse)
    });

    function  joinGroupResponse(url, jsonBody, result){
        try{
            var result = JSON.parse(result);
            if(result['errorCode'] == "error") {
                alert('此群不支持任意加入，请联系群主');
                return;
            }
        }catch (error) {

        }
        var groupId = jsonBody.groupId;
        $("."+groupId).removeClass("applyButton");
        $("."+groupId).addClass("disableButton");
        $("."+groupId).attr("disabled", "disabled");
        $("."+groupId).html("已入群");
    }


</script>

</body>
</html>





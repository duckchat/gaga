<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>用户管理<?php } else { ?>User Management<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>

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
        #search-user-div {
            text-align: center;
        }
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


        .item-body-display, .item-body-desc, .item-body, .item-row {
            height:56px;
            line-height: 56px;
        }

        .item-header {
            width: 50px;
            height: 56px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .user-avatar-image {
            width:40px;
            height:40px;
        }
        .setService {
            background:rgba(76,59,177,1);
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(255,255,255,1);
            line-height: 28px;
            cursor: pointer;
            outline: none;
            border:1px solid;
            width:50px;
            height:28px;
            background:rgba(76,59,177,1);
            border-radius:4px;

        }
        .disableButton {
            width:50px;
            height:28px;
            border-radius:4px;
            border:1px solid;
            line-height: 28px;
            cursor: pointer;
            outline: none;
            border:1px solid;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            background: #cccccc;
            color: white;
        }
    </style>

</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="item-search">
        <img class="search-img" width="19px" height="19px" src="../../public/img/manage/search.png">

        <?php if ($lang == "1") { ?>
            <input class="search-input" placeholder="通过用户昵称搜索用户">
        <?php } else { ?>
            <input class="search-input" placeholder="search nickname">
        <?php } ?>

    </div>

    <div class="layout-all-row" id="search-content" style="display: none">
        <div class="list-item-center">

            <div id="search-title" class="item-row-title">
                <?php if ($lang == "1") { ?>
                    用户搜索结果
                <?php } else { ?>
                    Search Users
                <?php } ?>

            </div>


            <div id="search-user-div">

            </div>
        </div>
    </div>

</div>


<script type="text/javascript" src="../../public/js/template-web.js"></script>
<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/jquery/jquery-weui.min.js"></script>
<script type="text/javascript" src="../../public/js/jquery-confirm.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script id="tpl-search-user" type="text/html">
    <div class="item-row">
        <div class="item-header">
            <img class="user-avatar-image" avatar="{{avatar}}"
                 src="{{avatar}}"
                 onerror="this.src='../../public/img/msg/default_user.png'"/>
        </div>
        <div class="item-body">
            <div class="item-body-display">
                <div class="item-body-desc" style="font-size: 10px;">
                    {{nickname}}
                </div>

                <div class="item-body-tail">
                    {{if isService == 1}}
                    <button class="addButton service_{{userId}} disableButton" userId="{{userId}}">
                        客服
                    </button>
                    {{else}}

                    <button class="addButton setService service_{{userId}}" userId="{{userId}}">
                        设置
                    </button>
                    {{/if}}
                </div>
            </div>
        </div>
    </div>

    <div class="division-line"></div>

</script>

<script type="text/javascript">

    var currentPageNum = 1;
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
            'pageNum': ++currentPageNum,
        };

        var url = "index.php?action=manage.user";
        zalyjsCommonAjaxPostJson(url, data, loadMoreResponse)
    }

    function loadMoreResponse(url, data, result) {

        if (result) {
            var res = JSON.parse(result);

            var isloading = res['loading'];
            loading = isloading;
            var data = res['data'];

            // alert(result);
            if (data && data.length > 0) {
                $.each(data, function (index, user) {
                    var userHtml = ' ' +
                        '<div class="item-row">' +
                        '<div class="item-body" onclick="showUserProfile(\'' + user.userId + '\')" id="user-list-id">' +
                        '<div class="item-body-display" style="align-items: center">' +
                        ' <div class="item-body-desc">' + user.nickname + '</div>' +
                        '   <div class="item-body-tail">' +
                        '   <div class="item-body-value">' +
                        '   <img class="more-img" src="../../public/img/manage/more.png"/>' +
                        '   </div>' +
                        '   </div>' +
                        '   </div>' +
                        '   </div>' +
                        '</div>';

                    userHtml += '<div class="division-line"></div>';

                    $(".item-row-list").append(userHtml);
                });
            }

        }

    }
</script>

<script type="text/javascript">

    $(".search-input").on('input porpertychange', function () {
        var val = $(this).val();
        if (val == "") {
            $("#search-content").hide();
        }
    });
    var lang = getLanguage();

    $(".search-input").on('keypress', function (e) {

        var keycode = e.keyCode;
        var searchName = $(this).val();
        if (keycode == '13') {
            // The Event interface's preventDefault() method tells the user agent that if the event does not get explicitly handled, its default action should not be taken as it normally would be. The event continues to propagate as usual, unless one of its event listeners calls stopPropagation() or stopImmediatePropagation(), either of which terminates propagation at once.
            e.preventDefault();

            var searchValue = $(this).val();
            searchUsers(searchValue)
        }
    });

    function searchUsers(searchValue) {
        $("#search-content").show();

        var url = "./index.php?action=miniProgram.customerService.manage&lang=" + getLanguage();
        var data = {
            'operation':'search',
            "searchValue": searchValue
        };

        zalyjsCommonAjaxPostJson(url, data, searchUsersResponse);
    }


    function isMobile() {
        if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
            return true;
        }
        return false;
    }

    function searchUsersResponse(url, data, result) {

        $("#search-user-div").html("");

        if (result) {

            var res = JSON.parse(result);

            if (res.errCode == "success") {

                var userList = res['users'];
                var isMobileClient = isMobile();

                $.each(userList, function (index, user) {
                    var src = "./index.php?action=http.file.downloadFile&fileId=" +  user['avatar'] + "&returnBase64=0";
                    if (isMobileClient) {
                        var avatar = user['avatar'];
                        var path = avatar.split("-");
                        src = "../../attachment/"+path[0]+"/"+path[1];
                    }
                    var html = template("tpl-search-user", {
                        nickname:user['nickname'],
                        userId:user['userId'],
                        avatar:src,
                        isService:user['isService'],
                    })
                    $("#search-user-div").append(html);
                });

            } else {
                var html = "";
                $("#search-user-div").append("没有找到结果");
            }

        } else {
            $("#search-user-div").append("没有找到结果");
        }
    }

    function showUserProfile(userId) {
        var url = "./index.php?action=manage.user.profile&lang=" + getLanguage() + "&userId=" + userId;
        zalyjsOpenPage(url);
    }


    $(document).on("click",".setService", function () {
        var userId = $(this).attr("userId");
        $.modal({
            title: lang == 1 ? '设置客服' : 'Set Customer Service',
            text: lang == 1 ? '确认设置？' : ' Confirm?',
            buttons: [
                {
                    text: lang == 1 ? "取消" : "cancel", className: "default", onClick: function () {
                    }
                },
                {
                    text: lang == 1 ? "确定" : "confirm", className: "main-color", onClick: function () {
                        setCustomerService(userId);
                    }
                },

            ]
        });
    });

    function setCustomerService(userId)
    {
        var data = {
            'userId': userId,
            "operation":"add",
        };

        console.log(JSON.stringify(data));
        var url = "index.php?action=miniProgram.customerService.manage";
        zalyjsCommonAjaxPostJson(url, data, setCustomerServiceResponse);
    }


    function setCustomerServiceResponse(url, data, result) {
       try{
           var res = JSON.parse(result);

           var userId = data.userId;

           if (res.errCode != "success") {
               alert(res.errInfo);
               return;
           }
           $(".service_"+userId).addClass("disableButton");
           $(".service_"+userId).html('客服');
       }catch (error){

       }

    }

</script>


</body>
</html>





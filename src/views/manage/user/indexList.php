<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>用户管理<?php } else { ?>User Management<?php } ?></title>
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
        #search-user-div {
            text-align: center;
        }
    </style>

</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="item-search">
        <img class="search-img" width="19px" height="19px" src="../../public/img/manage/search.png">

        <?php if ($lang == "1") { ?>
            <input class="search-input" placeholder="通过用户名、昵称、用户ID 搜索用户">
        <?php } else { ?>
            <input class="search-input" placeholder="search loginName,nickname,userId">
        <?php } ?>

    </div>

    <div class="layout-all-row" id="search-content" style="display: none">
        <div class="list-item-center">

            <div id="search-title" class="item-row-title">
                <?php if ($lang == "1") { ?>
                    用户搜索结果
                <?php } else { ?>
                    Search Groups
                <?php } ?>

            </div>


            <div id="search-user-div">

            </div>
        </div>
    </div>

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row-title">
                <div class="">
                    <?php if ($lang == "1") { ?>
                        站点成员列表
                    <?php } else { ?>
                        Site Users
                    <?php } ?>
                    (<?php echo $totalUserCount ?>)
                </div>
            </div>

            <div class="item-row-list">
                <?php foreach ($userList as $key => $profile) { ?>

                    <div class="item-row">
                        <div class="item-body" onclick="showUserProfile('<?php echo($profile["userId"]) ?>')"
                             id="user-list-id">
                            <div class="item-body-display" style="align-items: center">
                                <div class="item-body-desc"><?php
                                    $username = $profile["userId"];
                                    if ($profile["nickname"]) {
                                        $username = $profile["nickname"];
                                    } else if ($profile["loginName"]) {
                                        $username = $profile["loginName"];
                                    }

                                    $length = mb_strlen($username);
                                    if ($length > 20) {
                                        echo mb_substr($username, 0, 20) . "...";
                                    } else {
                                        echo $username;
                                    }
                                    ?></div>

                                <div class="item-body-tail">
                                    <div class="item-body-value">
                                        <img class="more-img" src="../../public/img/manage/more.png"/>
                                    </div>
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
<script type="text/javascript" src="../../public/manage/native.js"></script>
<script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

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

        var url = "./index.php?action=manage.user.search&lang=" + getLanguage();
        var data = {
            "searchValue": searchValue
        };

        zalyjsCommonAjaxPostJson(url, data, searchUsersResponse);
    }

    function searchUsersResponse(url, data, result) {

        $("#search-user-div").html("");

        if (result) {

            var res = JSON.parse(result);

            if (res.errCode == "success") {

                var userList = res['users'];

                $.each(userList, function (index, user) {

                    var html = '<div class="item-row">'
                        + '<div class="item-body" onclick="showUserProfile(\'' + user["userId"] + '\');">'
                        + '<div class="item-body-display">'
                        + '<div class="item-body-desc">' + user["nickname"] + '</div>'

                        + '<div class="item-body-tail">'
                        + '<img class="more-img" src="../../public/img/manage/more.png"/>'
                        + '</div>'
                        + '</div>'

                        + '</div>'
                        + '</div>'
                        + '<div class="division-line"></div>';
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

</script>


</body>
</html>





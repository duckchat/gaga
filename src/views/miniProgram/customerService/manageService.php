<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>用户列表</title>
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
        .cancelService {
            width:50px;
            height:28px;
            border-radius:4px;
            border:1px solid;
            background:rgba(255,255,255,1);
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(76,59,177,1);
            line-height: 28px;
            cursor: pointer;
            outline: none;
            border:1px solid;
        }
    </style>

</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center" style="margin-top: 20px;">
            <div class="item-row-list">

                <?php if(count($services)): ?>
                    <?php foreach ($services as $user):?>
                        <div class="item-row">
                            <div class="item-header">
                                <img class="user-avatar-image" avatar="<?php echo $user['avatar'] ?>"
                                     src=""
                                     onerror="this.src='../../public/img/msg/default_user.png'"/>
                            </div>
                            <div class="item-body">
                                <div class="item-body-display">
                                    <div class="item-body-desc" style="font-size: 10px;" onclick="showUserChat('<?php echo $user["userId"] ?>')">
                                        <?php echo $user['nickname']; ?>
                                    </div>

                                    <div class="item-body-tail">
                                        <button class="addButton cancelService" userId="<?php echo $user["userId"] ?>">
                                            取消
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="division-line"></div>
                    <?php endforeach;?>
                <?php endif;?>

            </div>
        </div>

    </div>

</div>


<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/jquery/jquery-weui.min.js"></script>
<script type="text/javascript" src="../../public/js/jquery-confirm.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript">

    var currentPageNum = 1;
    var loading = true;
    var token = $(".token").val();

    var lang = getLanguage();
    $(window).scroll(function () {
        //判断是否滑动到页面底部

        var pwLeft = $(".item-row-list")[0];
        var ch  = pwLeft.clientHeight;
        var sh = pwLeft.scrollHeight;
        var st = $(".item-row-list").scrollTop();
        if ((ch-sh-st) < 1) {
            if (!loading) {
                return;
            }

            loadMoreUsers();
        }
    });

    function loadMoreUsers() {

        var data = {
            'page': ++currentPageNum,
        };
        var searchKey = $(".search_key").val();
        var url = "index.php?action=miniProgram.search.index&for=user&key="+searchKey;
        zalyjsCommonAjaxPostJson(url, data, loadMoreResponse)
    }

    function loadMoreResponse(url, data, result) {
        if (result) {
            var res = JSON.parse(result);
            var data = res['data'];
            if (data && data.length > 0) {
                var isMobileClient = isMobile();

                $.each(data, function (index, user) {
                    var src = "./index.php?action=http.file.downloadFile&fileId=" +  user['avatar'] + "&returnBase64=0";
                    if (isMobileClient) {
                        var avatar = user['avatar'];
                        var path = avatar.split("-");
                        src = "../../attachment/"+path[0]+"/"+path[1];
                    }

                    var userHtml = template("tpl-search-user", {
                        nickname:user['nickname'],
                        userId:user['userId'],
                        avatar:src,
                        token:token
                    });

                    $(".item-row-list").append(userHtml);
                    $(".applyButton").bind("click");
                });
                loading = true;
                return;
            }
        }
        loading = false;
        currentPageNum = currentPageNum-1;
    }


    $(document).on("click",".cancelService", function () {
        var userId = $(this).attr("userId");
        $.modal({
            title: lang == 1 ? '取消客服' : 'Cancel Customer Service',
            text: lang == 1 ? '确认删除？' : ' Confirm?',
            buttons: [
                {
                    text: lang == 1 ? "取消" : "cancel", className: "default", onClick: function () {
                    }
                },
                {
                    text: lang == 1 ? "确定" : "confirm", className: "main-color", onClick: function () {
                        cancelCustomerService(userId);
                    }
                },

            ]
        });
    });

    function cancelCustomerService(userId)
    {
        var data = {
            'userId': userId,
            "operation":"delete",
        };

        console.log(JSON.stringify(data));
        var url = "index.php?action=miniProgram.customerService.manage";
        zalyjsCommonAjaxPostJson(url, data, cancleCustomerServiceResponse);
    }


    function cancleCustomerServiceResponse(url, data, result) {
        var res = JSON.parse(result);

        if (res.errCode != "success") {
            alert(res.errInfo);
            return;
        }
        window.location.reload();

    }

    $(".user-avatar-image").each(function () {
        var avatar = $(this).attr("avatar");
        var src = "./index.php?action=http.file.downloadFile&fileId=" + avatar + "&returnBase64=0";
        if (isMobile()) {
            var path = avatar.split("-");
            src = "../../attachment/"+path[0]+"/"+path[1];
        }
        $(this).attr("src", src);
    });

    function isMobile() {
        if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
            return true;
        }
        return false;
    }

    $(document).on("click", ".chatButton", function () {
        var friendId = $(this).attr("userId");
        if(isMobile()) {
            try {
                zalyjsGoto(null, "u2Msg", friendId);
            } catch (e) {
                alert("客户端暂不支持，请升级客户端");
            }
        } else {
            alert("web端暂不支持，请使用客户端");
        }

    });



</script>




</body>
</html>





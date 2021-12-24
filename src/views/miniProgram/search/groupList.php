<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>群组列表</title>
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
        .item-row-list {
            height: 100%;
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
        .group-avatar-image {
            width:40px;
            height:40px;
        }
        .disableButton {
            background: #cccccc;
        }

    </style>

</head>

<body>
<?php include (dirname(__DIR__) . '/search/template_search.php');?>

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center" style="margin-top: 20px;">
            <div class="item-row-list">

                <?php if(count($groups)): ?>
                <?php foreach ($groups as $group):?>
                <div class="item-row">
                    <div class="item-header">
                        <img class="group-avatar-image" avatar="<?php echo $group['avatar'] ?>"
                             src=""
                             onerror="this.src='../../public/img/msg/default_user.png'"/>
                    </div>

                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc" >
                               <div class="group_name">
                                   <?php echo $group['name'];?>
                               </div>
                                <div class="group_owner">
                                    群主：<?php echo $group['ownerName'];?>
                                </div>
                            </div>

                            <div class="item-body-tail">
                                <?php if($group['isMember'] == true):?>
                                <button class="addButton disableButton <?php echo $group['groupId'];?>" groupId="<?php echo $group['groupId'];?>">
                                    已入群
                                </button>
                                <?php else :?>
                                    <?php if($group['permissionJoin'] == 0):?>
                                        <button class="addButton applyButton <?php echo $group['groupId'];?>" groupId="<?php echo $group['groupId'];?>">
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
                <?php endif;?>

            </div>
        </div>

    </div>

</div>
<input type="hidden" value="<?php echo $key;?>" class="search_key">
<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript" src="../../public/js/template-web.js"></script>
<script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

<script type="text/javascript">
    var currentPageNum = 1;
    var loading = true;

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
            loadMoreGroups();
        }
    });

    function loadMoreGroups() {

        var data = {
            'page': ++currentPageNum,
        };
        var searchKey = $(".search_key").val();
        var url = "index.php?action=miniProgram.search.index&for=group&key="+searchKey;
        zalyjsCommonAjaxPostJson(url, data, loadMoreResponse)
    }

    function isMobile() {
        if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
            return true;
        }
        return false;
    }
    function loadMoreResponse(url, data, result) {
        if (result) {
            var res = JSON.parse(result);
            var data = res['data'];
            if (data && data.length > 0) {
                var isMobileClient = isMobile();

                $.each(data, function (index, group) {
                    var src = "./index.php?action=http.file.downloadFile&fileId=" +  group['avatar'] + "&returnBase64=0";
                    if (isMobileClient) {
                        var avatar = group['avatar'];
                        var path = avatar.split("-");
                        src = "../../attachment/"+path[0]+"/"+path[1];
                    }

                    var userHtml = template("tpl-search-group", {
                        name:group['name'],
                        groupId:group['groupId'],
                        ownerName:group['ownerName'],
                        avatar:src,
                        isMember:group['isMember'],
                        permissionJoin:group['permissionJoin']
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

    $(".group-avatar-image").each(function () {
        var avatar = $(this).attr("avatar");
        var src = "./index.php?action=http.file.downloadFile&fileId=" + avatar + "&returnBase64=0";
        if (isMobile()) {
            var path = avatar.split("-");
            src = "../../attachment/"+path[0]+"/"+path[1];
        }
        $(this).attr("src", src);
    });

    $(".applyButton").on("click", function () {
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

    $(document).on("click", ".chatButton", function () {
        var groupId = $(this).attr("groupId");
        if(isMobile()) {
            try {
                zalyjsGoto(null, "groupMsg", groupId);
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





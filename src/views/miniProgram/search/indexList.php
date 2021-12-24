<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../../public/manage/config.css"/>

    <style>
        body,html {
            height: 100%;
            -webkit-user-select:none;
            -moz-user-select:none;
            -ms-user-select:none;
            user-select:none;
        }
        div{
            -webkit-tap-highlight-color:rgba(0,0,0,0);
            -webkit-user-select:none;
            -moz-user-select:none;
            -ms-user-select:none;
            user-select:none;
        }
        #wrapper {
            height: 100%;
            text-align: center;
            display: flex;
            justify-content: center;
            background:white;
        }
        .continer {
            width: 100%;
            height:100%;
            background:white;
            overflow-y: scroll;
            border: 1px solid #cccccc;
        }
        .logo_img {
            width: 51px;
        }
        .box {
            text-align: center;
            width: 100%;
            margin-top: 17px;
        }
        .search_input {
            width:80%;
            height:42px;
            font-size:14px;
            border-radius:2px;
            border:1px solid;
            outline: none;
            padding-left: 31px;
            margin-top: 25px;
        }
        .box_relation {
            text-align: center;
            display: flex;
            justify-content: center;
            height: 71px;
            position: relative;
        }
        .search_logo{
            width: 14px;
            height:16px;
        }
        .search_absoulte {
            position: absolute;
            width: 100%;
        }
        .search_logo_div {
            height: 96px;
            display: flex;
            justify-content: left;
            align-items: center;
            width: 16px;
            left: 9.5%;
        }
        .desc {
            width: 100%;
            font-size: 12px;
            font-family: PingFangSC-Regular;
            font-weight: 400;
            color: rgba(52,54,60,1);
            line-height: 17px;
            word-break: break-all;
            text-align: left;

        }
        .desc_div {
            width: 100%;
            text-align: left;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .sub_title {
            width: 100%;
            height:14px;
            font-size:10px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(52,54,60,1);
            line-height:14px;
            margin-top: 21px;
        }

        .sub_title_contact {
            width: 100%;
            height:10px;
            font-size:10px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(52,54,60,1);
            line-height:10px;
            margin-bottom: 7px;
        }
        .v_line {
            width:1px;
            height:45px;
            background:rgba(233,232,237,1);
        }
        .search_tip {
            font-size:16px;
            font-family:PingFangSC-Medium;
            font-weight:500;
            color:rgba(50,136,255,1);
            border: none;
            outline: none;
            cursor: pointer;
            background: white;
            width: 100px;
        }
        .v_line_absoulte {
            height: 92px;
            display: flex;
            justify-content: center;
            align-items: center;
            right: 15%;
            width: 5px;
        }
        .search_tip_div {
            right: 9%;
            top: 25px;
            margin:auto;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            width: 100px;
        }
        .search_tip_div:focus {
            background: none;
        }
        .grap_div {
            height:10px;
            background:rgba(233,232,237,1);
            overflow: hidden;
        }
        .row {
            width: 100%;
            height: 50%;
            background:white;
        }
        .title_recomment_group {
            height:40px;
            font-size:16px;
            font-family:PingFangSC-Medium;
            font-weight:500;
            color:rgba(27,27,27,1);
            line-height:40px;
            text-align: left;
            margin-left: 10px;
        }
        .line {
            width:100%;
            height:1px;
            background:rgba(233,232,237,1);
        }
        .group_row {
            height:56px;
        }

        .group-avatar-image {
            width: 40px;
            height: 40px;
            margin-left: 10px;
        }
        .group_info {
            display:flex;
            height: 100%;
            text-align: center;
            align-items: center;
            justify-content: space-between;
        }
        .group_name, .group_owner {
            text-align: left;
        }
        .group_owner {
            height:12px;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(153,153,153,1);
            line-height:12px;
            margin-top: 8px;
        }

        .add_group_button  button {
            height:28px;
            background:rgba(76,59,177,1);
            border: rgba(76,59,177,1);;
            border-radius:2px;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(255,255,255,1);
            line-height:28px;
            outline: none;
            cursor: pointer;
        }
        .desc_box {
            text-align: center;
            width: 100%;
            justify-content: center;
            display: flex;
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

        .group_name {
            height: 18px;
            line-height: 18px;
            text-align: left;
            margin-top: 10px;
        }
        .group_owner {
            height: 12px;
            line-height: 12px;
            text-align: left;
            margin-bottom: 10px;
        }
        .group_owner {
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(153,153,153,1);
            line-height:12px;
        }
        .disableButton {
            background: #cccccc;
        }
        .desc_box_div {
            width: 80%;
            padding-right: 31px;
        }
        .division-line {
            margin-left: 10px;
        }

        @media (max-width: 500px) {
            .desc_box_div {
                width: 90%;
                padding-right: 0px;
            }
            .search_tip_div {
                right:7%;
            }
            .search_logo_div {
                left:8%;
            }
        }

    </style>

</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="continer">
            <div class="box">
                <img class="logo_img" src="../../public/img/search/logo.png">
            </div>
            <div class="box box_relation">
                <div class="search_absoulte">
                    <input type="text" class="search search_input">
                </div>
                <div class="search_absoulte search_logo_div">
                    <img class="search_logo " src="../../public/img/search/search.png">
                </div>

                <div class="search_absoulte search_tip_div">
                    <div class="v_line"></div><button class="search_tip">搜索一下</button>
                </div>
            </div>
            <div class="box desc_box">
                <div style="width: 100%;display: flex;justify-content: center">
                    <div class="desc_box_div">
                        <div class="desc">
                            <?php echo $about_us_desc; ?>
                        </div>
                        <div class="sub_title">
                            <?php echo $about_us_title; ?>
                        </div>
                        <div class="sub_title_contact">
                            <?php echo $about_us_contact; ?>
                        </div>
                    </div>
                </div>
            </div>
        <div class="grap_div">
        </div>
        <div class="row">
            <div class="title_recomment_group">
                热门推荐
            </div>
            <div class="line"></div>

            <div class="group_rows">
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
                                            <button class="addButton  disableButton <?php echo $group['groupId'];?>" groupId="<?php echo $group['groupId'];?>">
                                                已入群
                                            </button>
                                        <?php else :?>
                                            <?php if($group['permissionJoin'] == 0):?>
                                                <button class="addButton applyButton <?php echo $group['groupId'];?> " groupId="<?php echo $group['groupId'];?>">
                                                    一键入群
                                                </button>
                                            <?php else: ?>
                                                <button class="addButton applyButton disableButton <?php echo $group['groupId'];?> " groupId="<?php echo $group['groupId'];?>">
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


<?php include (dirname(__DIR__) . '/search/template_search.php');?>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>
<script type="text/javascript" src="../../public/js/template-web.js"></script>
<script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>


<script type="text/javascript">


    $(".search_tip_div").on("click", function () {
        var param = $(".search_input").val();
        if(param == undefined || param.length < 1) {
            alert("请输入需要搜索的内容");
            return;
        }
        var url = "index.php?action=miniProgram.search.index&for=search&key="+param;
        zalyjsCommonOpenPage(url);
    });

    $(".join_group").on("click", function () {
        var url = "index.php?action=miniProgram.search.index&for=search";
        zalyjsCommonOpenPage(url);
    });

    $(".group-avatar-image").each(function () {
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
                alert(result['errorInfo']);
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





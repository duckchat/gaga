<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>密码错误日志<?php } else { ?> Password error log<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>

    <link rel="stylesheet" href="../../public/manage/config.css"/>
    <style>

        html, body {
            padding: 0px;
            margin: 0px;
            font-family: PingFangSC-Regular, "MicrosoftYaHei";
            overflow: hidden;
            width: 100%;
            height: 100%;
            background: rgba(245, 245, 245, 1);
            font-size: 14px;

        }

        .wrapper {
            width: 100%;
            height:100%;
        }

        .layout-all-row {
            width: 100%;
            background: white;
            display: flex;
            align-items: stretch;
            overflow: hidden;
            flex-shrink: 0;

        }

        .item-row {
            background: rgba(255, 255, 255, 1);
            display: flex;
            flex-direction: row;
            text-align: center;
            height: 50px;
            cursor: pointer;
            /*margin-bottom: 2rem;*/
        }

        /*.item-row:hover{*/
        /*background: rgba(255, 255, 255, 0.2);*/
        /*}*/

        .item-row:active {
            background: rgba(255, 255, 255, 0.2);
        }

        .item-header {
            width: 50px;
            height: 50px;
        }

        .site-manage-image {
            width: 40px;
            height: 40px;
            margin-top: 5px;
            margin-bottom: 5px;
            margin-left: 16px;
            border-radius: 50%;
        }

        .item-body {
            width: 100%;
            height: 50px;
            margin-left: 1rem;
            margin-top: 7px;
            flex-direction: row;
        }

        .list-item-center {
            width: 100%;
            height: 44px;
            /*height: 11rem;*/
            /*background: rgba(255, 255, 255, 1);*/
            /*padding-left: 1rem;*/

        }

        .item-body-display {
            display: flex;
            justify-content: space-between;
            /*margin-right: 7rem;*/
            /*margin-bottom: 3rem;*/
            line-height: 3rem;
        }

        .item-body-tail {
            margin-right: 10px;
        }

        .item-body-desc {
            width: 100%;
            text-align: left;
        }

        .more-img {
            width: 8px;
            height: 13px;
            /*border-radius: 50%;*/
        }

        .division-line {
            height: 1px;
            background: rgba(243, 243, 243, 1);
            margin-left: 40px;
            overflow: hidden;
        }
        .check_img {
            width: 15px;
            height:15px;
        }
        .tip{
            height:12px;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(102,102,102,1);
            line-height:12px;
            margin-bottom: 20px;
            margin-left: 10px;
        }
        .table {
            border-radius: 3px;
            height:100%;
            width: 100%;
            background:rgba(255,255,255,1);
            margin: 20px 10px 10px 10px;
        }

        .error-log-div {
            width: 100%;
            height:100%;
            overflow-y: scroll;
        }
        .row {
            display: flex;
            width: 100%;
            height:36px;
            justify-content: center;
            border-left: 1px solid #999999;;
        }
        .data {
            width:60%;
            padding-left: 1rem;
        }
        .cell {
            display: flex;
            height:36px;
            width:25%;
            align-items: center;
            border-bottom: 1px solid #999999;
            border-right: 1px solid #999999;
        }
        .row-head {
            width:25%;
            justify-content: center;
        }
        .item-body-tail span {
            width:84px;
            height:16px;
            font-size:14px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(76,59,177,1);
            line-height:16px;
            cursor: pointer;
        }

    </style>
</head>

<body>

<div class="wrapper" id="wrapper">
    <div class="layout-all-row count-row" style="margin-top:10px;">
        <div class="list-item-center">
            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                总记录数 (<?php echo $count;?>)
                            <?php } else { ?>
                                Total (<?php echo $count;?>)
                            <?php }?>
                        </div>

                        <div class="item-body-tail">
                            <span onclick="truncateLogs()"><?php if ($lang == "1") { ?>删除所有记录 <?php } else { ?> Delete logs <?php } ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layout-all-row error-log-div" style="margin-top:10px;">
        <div class="table">
            <div class="row" style="border-top: 1px solid #999999;">
                <div class="row-head cell"><?php if ($lang == "1") { ?>账号 <?php } else { ?>Account <?php }?></div>
                <div class="row-head cell"><?php if ($lang == "1") { ?>操作 <?php } else { ?> Operation     <?php }?></div>
                <div class="row-head cell">IP</div>
                <div class="data cell"><?php if ($lang == "1") { ?>时间 <?php } else { ?> Date <?php }?></div>
            </div>

            <?php foreach ($logs as $log) {?>
                <div class="row log_id" log-id="<?php echo $log['id'];?>">
                    <div class="row-head cell"><?php echo $log['loginName']; ?></div>
                    <div class="row-head cell">
                        <?php if($log['operation'] == 1) {?>

                            <?php if ($lang == "1") { ?>登录 <?php } else { ?>Login <?php }?>
                        <?php } else { ?>
                             <?php if ($lang == "1") { ?>修改密码 <?php } else { ?>Modify Password <?php }?>

                        <?php  } ?>
                    </div>
                    <div class="row-head cell"><?php echo $log['ip']; ?></div>
                    <div class="data cell"><?php echo date('Y-m-d H:i', $log['operateTime']/ 1000); ?></div>
                </div>
            <?php } ?>

        </div>
    </div>

</div>


<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript">

    var lang = getLanguage();
    var wrapperDivHeight = $(".wrapper")[0].clientHeight;
    var countRowHeight = $(".count-row")[0].clientHeight;
    $(".error-log-div")[0].style.height = Number(wrapperDivHeight-countRowHeight-20)+"px";

    $(".check_img").on("click", function () {
        var pwdType = $(this).attr("pwd_type");
        var uncheckSrc = "../../public/img/manage/uncheck.png";
        var checkSrc = "../../public/img/manage/checked.png";
        switch (pwdType) {
            case "pwd_default":
                $(".pwd_convenience_img").attr("src", uncheckSrc);
                $(".pwd_security_img").attr("src", uncheckSrc);
                $(".pwd_default_img").attr("src", checkSrc);
                break;
            case "pwd_convenience":
                $(".pwd_convenience_img").attr("src", checkSrc);
                $(".pwd_security_img").attr("src", uncheckSrc);
                $(".pwd_default_img").attr("src", uncheckSrc);

                break;
            case "pwd_security":
                $(".pwd_convenience_img").attr("src", uncheckSrc);
                $(".pwd_security_img").attr("src", checkSrc);
                $(".pwd_default_img").attr("src", uncheckSrc);
                break;
        }
        var data = {
            "pwd_type" : pwdType
        }
        var url = "index.php?action=manage.security.update&lang="+lang;

        zalyjsCommonAjaxPostJson(url, data, updateResponse);
    });

    function updateResponse(url, data, result) {
        var res = JSON.parse(result);
        if ("success" == res.errCode) {
            window.location.reload();
        } else {
            alert("error : " + res.errInfo);
        }
    }

    var currentPageNum = 1;
    var loading = true;

    $(".error-log-div").scroll(function () {
        //判断是否滑动到页面底部

        var errorLogDiv =  $(".error-log-div")[0];
        var sh = errorLogDiv.scrollHeight;
        var ch  = errorLogDiv.clientHeight;
        var st = $('.error-log-div').scrollTop();

        if((sh - ch - st) <= 1){
            if (!loading) {
                return;
            }
            loadMoreLogs();
        }
    });

    function loadMoreLogs() {

        var data = {
            'page': ++currentPageNum,
        };

        var url = "index.php?action=manage.security.log&lang="+lang;
        zalyjsCommonAjaxPostJson(url, data, loadMoreResponse)
    }
    function truncateLogResponse(url, results)
    {
        var results = JSON.parse(results);
        if(results['errCode'] == true) {
            window.location.reload();
            return;
        }
        var tip = "failed";
        if(lang == 1) {
            tip = "删除失败";
        }
        alert(tip);
    }

    function truncateLogs()
    {
        var url = "index.php?action=manage.security.log&for=truncate&lang="+lang;
        zalyjsCommonAjaxGet(url, truncateLogResponse)
    }

    function getDateByTime(time)
    {
        time = Number(time);
        var date = new Date(time); //获取一个时间对象

        var minutes =  date.getMinutes()>=10 ? date.getMinutes():"0"+date.getMinutes();
        var month = date.getMonth() >= 9 ? (date.getMonth()+1) : "0"+ (date.getMonth()+1);

        return date.getFullYear() + '-' + month + '-' +date.getDate() + " " + date.getHours()+":"+minutes;  // 获取完整的年份(4位,1970)
    }



    function loadMoreResponse(url, data, result) {

        if (result) {
            var res = JSON.parse(result);

            var data = res['data'];

            // alert(result);
            if (data && data.length > 0) {
                $.each(data, function (index, log) {
                    var html = '<div class="row log_id" log-id="'+log.id+'"> <div class="row-head cell">'+log.loginName+'</div> <div class="row-head cell">'
                    if(log.operation == 1) {
                        if(lang == 1) {
                            html += "登录";
                        } else {
                            html += "Login ";
                        }
                    } else {
                        if(lang == 1) {
                            html += "修改密码";
                        } else {
                            html += "Modify Password ";
                        }
                    }
                    html += '</div><div class="row-head cell">'+log.ip+'</div> <div class="data cell">'+getDateByTime(log.operateTime)+'</div> </div>'
                    $(".table").append(html);
                });
                loading = true;
                return;
            }
        }
        loading = false;

    }


</script>

</body>
</html>





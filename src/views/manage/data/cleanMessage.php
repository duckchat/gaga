<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>
    <link rel="stylesheet" href="../../public/manage/config.css"/>

    <style>

        .datetime-row {
            margin: 31pt 10pt 0 10pt;
            text-align: center;
        }

        .datetime-picker {
            width: 100%;
            height: 40px;
            background: rgba(255, 255, 255, 1);
            border-radius: 4px;
            border: 1px solid;
            border-color: #DFDFDF;
        }

        .datetime-label {
            margin-top: 82px;
            margin-bottom: 19px;
            text-align: center;
        }

        .select-label {
            font-size: 12px;
            color: #999999;
        }

        .datetime-select-button {
            width: 60px;
            height: 60px;
            border-radius: 2px;
            border: 1px solid;
            border-color: #979797;
            background: rgba(245, 245, 245, 1);
        }

        .item-body-datetime-select {
            margin: 0 40px 64px 40px;
            display: flex;
            justify-content: space-between;
        }

        .clean-button {
            margin: 0 10px 0 10px;
            margin-right: 50px;
            width: 100%;
            height: 44px;
            background: rgba(76, 59, 177, 1);
            border-radius: 4px;
            font-size: 16px;
            font-family: PingFangSC-Regular;
            font-weight: 400;
            color: rgba(255, 255, 255, 1);
        }

    </style>
</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center">

            <input type="hidden" id="clean-type" value="<?php echo $type; ?>"/>

            <div class="datetime-row">
                <input class="datetime-picker" id="datetime-local" type="datetime-local"/>
            </div>

            <div class="datetime-label">
                <?php if ($type == "u2Message") { ?>
                    <label class="select-label">快速选择删除某一个时间之前的所有“二人”消息</label>
                <?php } elseif ($type == "groupMessage") { ?>
                    <label class="select-label">快速选择删除某一个时间之前的所有“群组”消息</label>
                <?php } ?>
            </div>

            <div class="item-body-datetime-select">

                <div class="">
                    <input class="datetime-select-button" type="button" data="1" value="一天前"/>
                </div>

                <div class="">
                    <input class="datetime-select-button" type="button" data="7" value="一周前"/>
                </div>

                <div class="">
                    <input class="datetime-select-button" type="button" data="30" value="一月前"/>
                </div>

                <div class="">
                    <input class="datetime-select-button" type="button" data="0" value="所有的"/>
                </div>
            </div>

            <div class="" style="margin-right: 20px">
                <button class="clean-button">删除消息</button>
            </div>

        </div>
    </div>

</div>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/jquery/jquery-weui.min.js"></script>
<script type="text/javascript" src="../../public/js/jquery-confirm.js"></script>
<script type="text/javascript" src="../../../public/sdk/zalyjsNative.js"></script>

<script type="text/javascript">

    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return 1;
        }
        return 0;
    }

    $(".datetime-select-button").click(function () {

        var currentTimeMillis = new Date().getTime();

        var day = $(this).attr("data");

        var beforeTimeMillis = currentTimeMillis - day * 24 * 3600 * 1000 + 8 * 3600 * 1000;

        var beforeDate = new Date(beforeTimeMillis);

        var ss5 = beforeDate.toISOString();

        var isoTimes = ss5.split(".");

        $("#datetime-local").val(isoTimes[0]);
    });

    $(".clean-button").click(function () {

        var type = $("#clean-type").val();
        var dateTime = $("#datetime-local").val();

        var lang = getLanguage();

        if (dateTime == "") {
            alert("请选择或者输入删除消息的时间");
            return;

        }

        var date = new Date(dateTime);
        $.modal({
            title: lang == 1 ? '清理消息' : 'Clean Message',
            text: lang == 1 ? '操作无法撤销，确认删除？' : 'Cannot be undo, Confirm?',
            buttons: [
                {
                    text: lang == 1 ? "取消" : "cancel", className: "default", onClick: function () {
                        // alert("cancel");
                    }
                },
                {
                    text: lang == 1 ? "确定" : "confirm", className: "main-color", onClick: function () {
                        cleanMessage(type, date.getTime());
                    }
                },

            ]
        });

    });

    function cleanMessage(type, timeMillis) {

        if (type != "u2Message" && type != "groupMessage") {
            alert("需要删除的类型错误");
            return;
        }

        if (timeMillis == NaN) {
            alert("请选择或者输入删除消息的时间");
            return;
        }

        var url = "index.php?action=manage.data.clean&lang=" + getLanguage();

        var data = {
            "type": type,
            "beforeTime": timeMillis
        };

        $.ajax({
            url: url,
            method: "POST",
            data: data,
            success: function (result) {

                if (result) {
                    var res = JSON.parse(result);

                    if ("success" == res.errCode) {
                        alert("删除消息数据完成");
                    } else {
                        alert("操作失败，原因：" + res.errInfo);
                    }

                } else {
                    alert("操作失败");
                }
            },
            error: function (err) {
                alert("error");
            }
        });

    }

</script>

</body>
</html>





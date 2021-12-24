<!DOCTYPE html>

<html lang="ZH">

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>用户资料配置<?php } else { ?>User Profile Settings<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>

    <link rel="stylesheet" href="../../public/manage/config.css"/>

    <style>

        .item-row-title {
            /*width: 100%;*/
            height: 20px;
            font-size: 14px;
            font-family: PingFangSC-Regular;
            font-weight: 400;
            color: rgba(153, 153, 153, 1);
            line-height: 14px;
            margin: 1px 0px 1px 10px;
        }

    </style>
</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display add-user-column" onclick="addNewColumn()">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">添加字段</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Add Column</div>
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


    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row-title">
                <div class="">
                    <?php if ($lang == "1") { ?>
                        字段列表
                    <?php } else { ?>
                        Column List
                    <?php } ?>
                </div>
            </div>


            <?php foreach ($userCustoms as $userCustom) { ?>
                <div class="item-row" onclick="showUserCustomProfile('<?php echo $userCustom['customKey']; ?>')">
                    <div class="item-body">
                        <div class="item-body-display add-user-column" onclick="addNewColumn()">
                            <div class="item-body-desc"><?php echo $userCustom['keyName']; ?></div>
                            <div class="item-body-tail">
                                <div class="item-body-value">
                                    <?php echo $userCustom['keySort']; ?>
                                </div>
                                <div class="item-body-value-more">
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

<script>

    function addNewColumn() {
        var url = "index.php?action=manage.custom.userAdd&lang=" + getLanguage();
        zalyjsOpenPage(url);
    }

    function updateResponse(url, data, result) {
        var res = JSON.parse(result);
        if ("success" == res.errCode) {
            window.location.reload();
        } else {
            alert("error : " + res.errInfo);
        }
    }

    function showUserCustomProfile(customKey) {
        var url = "index.php?action=manage.custom.userUpdate&lang=" + getLanguage() + "&customKey=" + customKey;
        zalyjsOpenPage(url);
    }

</script>

</body>

</html>
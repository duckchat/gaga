<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>高级<?php } else { ?>Advanced<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

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
            display: flex;
            align-items: stretch;
        }

        .layout-all-row {
            width: 100%;
            /*background: white;*/
            background: rgba(245, 245, 245, 1);;
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
            /*height: 11rem;*/
            /*background: rgba(255, 255, 255, 1);*/
            padding-bottom: 11px;
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
            height: 3rem;
            font-size: 16px;
            font-family: PingFangSC-Regular;
            /*color: rgba(76, 59, 177, 1);*/
            margin-left: 11px;
            line-height: 3rem;
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

    </style>
</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row" id="check_phpinfo">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                查看PHPINFO
                            <?php } else { ?>
                                Check PHPINFO
                            <?php } ?>
                        </div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../public/img/manage/more.png"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

    </div>

</div>


<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript">

$("#check_phpinfo").on("click", function () {
    var url = "index.php?action=manage.advanced&page=phpinfo&lang=" + getLanguage();
    zalyjsCommonOpenNewPage(url);
});

</script>

</body>
</html>





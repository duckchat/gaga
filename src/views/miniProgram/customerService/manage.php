<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>站点管理<?php } else { ?>Site Manage<?php } ?></title>
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

        .wrapper_div {
            height: 100%;
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
            overflow-y: scroll;
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

<div class="wrapper_div">
    <div class="wrapper" id="wrapper">

        <div class="layout-all-row">

            <div class="list-item-center">

                <div class="item-row" id="enable_customer_service">
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                    开启客服功能
                                <?php } else { ?>
                                    Enable Customer Service
                                <?php } ?>
                            </div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>


                <div class="item-row" id="see_customer_service_list">
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                    查看客服
                                <?php } else { ?>
                                    View Customer Service
                                <?php } ?>
                            </div>

                            <div class="item-body-tail">
                                <img class="more-img" src="../../public/img/manage/more.png"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>


                <div class="item-row" id="add_customer_service">
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc">
                                <?php if ($lang == "1") { ?>
                                    添加客服
                                <?php } else { ?>
                                    Add Coustome Service
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

    </div>

</div>
<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

<script type="text/javascript">

    function openPage(url) {
        if (isMobile()) {
            zalyjsOpenNewPage(url);
        } else {
            zalyjsOpenPage(url);
        }
    }

    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return 1;
        }
        return 0;
    }

    $("#see_customer_service_list").click(function () {
        var url = "/index.php?action=miniProgram.customerService.manage&operation=see&lang=" + getLanguage();
        openPage(url);
    });

    $("#add_customer_service").click(function () {
        var url = "index.php?action=miniProgram.customerService.manage&operation=add&lang=" + getLanguage();
        openPage(url);
    });

    $("#enable_customer_service").click(function () {
        var url = "index.php?action=miniProgram.customerService.index&lang=" + getLanguage();
        openPage(url);
    });



</script>

</body>
</html>





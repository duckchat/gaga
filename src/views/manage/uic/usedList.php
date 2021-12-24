<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>已使用邀请码<?php } else { ?>Used Invitation Code<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <style>

        html, body {
            padding: 0px;
            margin: 0px;
            font-family: PingFangSC-Regular, "MicrosoftYaHei";
            /*overflow: hidden;*/
            width: 100%;
            height: 100%;
            background: rgba(245, 245, 245, 1);
            font-size: 14px;
            overflow-x: hidden;
            /*overflow-y: hidden;*/
            display: block;
        }

        /* mask and new window */
        .wrapper-mask {
            background: rgba(0, 0, 0, 0.8);
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            position: fixed;
            z-index: 9999;
            overflow: hidden;
            visibility: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .wrapper {
            width: 100%;
            height: 100%;
            /*display: flex;*/
            align-items: stretch;
        }

        .layout-all-row {
            width: 100%;
            /*background: white;*/
            background: rgba(245, 245, 245, 1);;
            display: flex;
            /*align-items: stretch;*/
            overflow: hidden;
            flex-shrink: 0;
        }

        .item-row {
            background: rgba(255, 255, 255, 1);
            display: flex;
            flex-direction: row;
            text-align: center;
            height: 44px;
        }

        /*.item-row:hover{*/
        /*background: rgba(255, 255, 255, 0.2);*/
        /*}*/

        .item-row:active {
            background: rgba(255, 255, 255, 0.2);
        }

        .item-bottom {
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            flex-direction: row;
            text-align: center;
            height: 25px;
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
            /*border-radius: 50%;*/
        }

        .site-logo-image {
            width: 30px;
            height: 30px;
            /*margin-top: 5px;*/
            margin-bottom: 7px;
            /*border-radius: 50%;*/
        }

        .item-body {
            width: 100%;
            height: 44px;
            /*margin-left: 1rem;*/
            margin-top: 0.5rem;
            flex-direction: row;
            vertical-align: middle;
        }

        .item-body-50 {
            width: 100%;
            height: 44px;
            /*margin-left: 1rem;*/
            margin-top: 0.5rem;
            flex-direction: row;
            vertical-align: middle;
        }

        .list-item-center {
            width: 100%;
            /*height: 11rem;*/
            /*background: rgba(245, 245, 245, 1);*/
            padding-bottom: 11px;
            /*padding-left: 1rem;*/

        }

        .item-body-display {
            display: flex;
            justify-content: space-between;
            /*margin-top: 7px;*/
            height: 100%;
            /*line-height: 3rem;*/
            text-align: center;
        }

        .item-body-tail {
            text-align: center;
            margin-right: 10px;
            font-size: 16px;
            height: 3rem;
            /*color: rgba(76, 59, 177, 1);*/
            line-height: 3rem;
        }

        .item-body-desc {
            height: 3rem;
            font-size: 16px;
            /*color: rgba(76, 59, 177, 1);*/
            margin-left: 10px;
            line-height: 3rem;
        }

        .more-img {
            width: 8px;
            height: 13px;
            /*border-radius: 50%;*/
        }

        .line {
            width: 28.14rem;
            height: 0.01rem;
            border: 0.09rem solid rgba(153, 153, 153, 1);
            overflow: hidden;
            text-align: center;
            margin: 0 auto;
            margin-top: 0.1rem;
        }

        .division-line {
            height: 1px;
            background: rgba(243, 243, 243, 1);
            margin-left: 40px;
            overflow: hidden;
        }

        #popup-group {
            width: 50rem;
            height: 30rem;
            background: rgba(255, 255, 255, 1);
            border-radius: 0.94rem;
        }

        .header_tip_font {
            justify-content: center;
            text-align: center;
            margin-top: 5rem;
            height: 3.75rem;
            font-size: 2.63rem;
            color: rgba(76, 59, 177, 1);
            line-height: 3.75rem;
        }

        .popup-group-input {
            background-color: #ffffff;
            border-style: none;
            outline: none;
            height: 1.88rem;
            font-size: 1.88rem;
            font-family: PingFangSC-Regular;
            /*color: rgba(205, 205, 205, 1);*/
            line-height: 1.88rem;
            /*margin-left: 10rem;*/
            margin-top: 5rem;
            padding: 0.5rem;
            width: 55%;
            overflow: hidden;
        }

        .data_tip {
            height: 1.69rem;
            font-size: 1.31rem;
            font-family: PingFangSC-Regular;
            color: rgba(153, 153, 153, 1);
            line-height: 1.69rem;
            margin-left: 23rem;
            width: 29rem;
            word-break: break-all;
            padding: 0.5rem;
        }

        .create_button,
        .create_button:hover,
        .create_button:focus,
        .create_button:active {
            width: 90px;
            height: 30px;
            background: rgba(76, 59, 177, 1);
            border-radius: 0.44rem;
            border-width: 0;
            font-size: 1.67rem;
            color: rgba(255, 255, 255, 1);
        }

        .weui_switch {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            position: relative;
            width: 52px;
            height: 32px;
            border: 1px solid #DFDFDF;
            outline: 0;
            border-radius: 16px;
            box-sizing: border-box;
            background: #DFDFDF;
        }

        .weui_switch:checked {
            border-color: #4C3BB1;
            /*">#04BE02;*/
            background-color: #4C3BB1;
        }

        .weui_switch:before {
            content: " ";
            position: absolute;
            top: 0;
            left: 0;
            width: 50px;
            height: 30px;
            border-radius: 15px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
            border-bottom-left-radius: 15px;
            background-color: #FDFDFD;
            -webkit-transition: -webkit-transform .3s;
            transition: -webkit-transform .3s;
            transition: transform .3s;
            transition: transform .3s, -webkit-transform .3s;
        }

        .weui_switch:checked:before {
            -webkit-transform: scale(0);
            transform: scale(0);
        }

        .weui_switch:after {
            content: " ";
            position: absolute;
            top: 0;
            left: 0;
            width: 30px;
            height: 30px;
            border-radius: 15px;
            background-color: #FFFFFF;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
            -webkit-transition: -webkit-transform .3s;
            transition: -webkit-transform .3s;
            transition: transform .3s;
            transition: transform .3s, -webkit-transform .3s;
        }

        .weui_switch:checked:after {
            -webkit-transform: translateX(20px);
            transform: translateX(20px);
        }

    </style>

</head>

<body>

<div class="wrapper-mask" id="wrapper-mask" style="visibility: hidden;"></div>

<div class="wrapper" id="wrapper">

    <!--   part 2  -->
    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row-title">
                <div class="" style="flex-direction: row;vertical-align: bottom;width: 100%;">
                    <div class="item-body-display">

                        <div class="" style="margin-left: 10px">
                            <?php if ($lang == "1") { ?>
                                已使用邀请码列表
                            <?php } else { ?>
                                Used Invitation Code
                            <?php } ?>
                        </div>

                        <div class="" style="margin-right: 10px">
                            <!--                            <button id="uic-manage" type="button" class="create_button" url-value=""-->
                            <!--                                    style="margin-top: 7px;font-size: 14px">管理-->
                            <!--                            </button>-->
                        </div>

                    </div>
                </div>
            </div>


            <?php

            if (empty($usedList) || count($usedList) <= 0) {
                echo "暂无数据";
            }


            foreach ($usedList as $key => $value) { ?>
                <div class="item-row" id="" pluginId="<?php echo($value["code"]) ?>">
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc"><?php echo $value["code"] ?></div>

                            <div class="item-body-tail">
                                <?php if ($value['nickname']) {
                                    echo $value['nickname'];
                                } else {
                                    echo $value['loginName'];
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>
            <?php } ?>

            <div class="item-bottom">

            </div>
        </div>

    </div>
</div>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript">

    function isAndroid() {

        var userAgent = window.navigator.userAgent.toLowerCase();
        if (userAgent.indexOf("android") != -1) {
            return true;
        }

        return false;
    }

    function isMobile() {
        if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
            return true;
        }
        return false;
    }

    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return 1;
        }
        return 0;
    }


    function zalyjsAjaxPostJSON(url, body, callback) {
        zalyjsAjaxPost(url, jsonToQueryString(body), function (data) {
            var json = JSON.parse(data)
            callback(json)
        })
    }


    function zalyjsNavOpenPage(url) {
        var messageBody = {}
        messageBody["url"] = url
        messageBody = JSON.stringify(messageBody)

        if (isAndroid()) {
            window.Android.zalyjsNavOpenPage(messageBody)
        } else {
            window.webkit.messageHandlers.zalyjsNavOpenPage.postMessage(messageBody)
        }
    }

    function zalyjsCommonAjaxGet(url, callBack) {
        $.ajax({
            url: url,
            method: "GET",
            success: function (result) {

                callBack(url, result);

            },
            error: function (err) {
                alert("error");
            }
        });

    }


    function zalyjsCommonAjaxPost(url, value, callBack) {
        $.ajax({
            url: url,
            method: "POST",
            data: value,
            success: function (result) {
                callBack(url, value, result);
            },
            error: function (err) {
                alert("error");
            }
        });

    }

    function zalyjsCommonAjaxPostJson(url, jsonBody, callBack) {
        $.ajax({
            url: url,
            method: "POST",
            data: jsonBody,
            success: function (result) {

                callBack(url, jsonBody, result);

            },
            error: function (err) {
                alert("error");
            }
        });

    }

    /**
     * _blank    在新窗口中打开被链接文档。
     * _self    默认。在相同的框架中打开被链接文档。
     * _parent    在父框架集中打开被链接文档。
     * _top    在整个窗口中打开被链接文档。
     * framename    在指定的框架中打开被链接文档。
     *
     * @param url
     * @param target
     */
    function zalyjsCommonOpenPage(url, target = "_blank") {
        // window.open(url, target);
        location.href = url;
    }

</script>

<script type="text/javascript">


</script>


</body>
</html>





<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>邀请码<?php } else { ?>Invitation Code<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>

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
        }
        .item-row,.weui-actionsheet__cell,.create_button {
            cursor: pointer;
            outline: none;
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
            /*height: 100%;*/
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
            height: 20px;
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
            /*margin-top: 5px;*/
            flex-direction: row;
            vertical-align: middle;
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
            /*height: 100%;*/
            /*line-height: 3rem;*/
            margin-top: 7px;
        }

        .item-body-tail {
            text-align: right;
            margin-right: 10px;
            margin-bottom: 10px;
            font-size: 16px;
            display: flex;
            /*height: 3rem;*/
            /*color: rgba(76, 59, 177, 1);*/
            /*line-height: 3rem;*/
        }

        .item-body-desc {
            /*height: 3rem;*/
            font-size: 16px;
            /*color: rgba(76, 59, 177, 1);*/
            margin-left: 10px;
        }

        .more-img {
            width: 8px;
            height: 13px;
            margin-top: 5px;
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

        .plugin-add-input {
            background-color: #ffffff;
            border-style: none;
            outline: none;
            font-size: 14px;
            font-family: PingFangSC-Regular;
            /*color: rgba(205, 205, 205, 1);*/
            /*line-height: 1.88rem;*/
            /*margin-left: 10rem;*/
            padding: 0.5rem;
            width: 200px;
            height: 5px;
            overflow: hidden;
            text-align: right;
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
            /*margin-top: 2rem;*/
            width: 100px;
            height: 23px;
            background: rgba(76, 59, 177, 1);
            border-radius: 0.94rem;
            border-width: 0px;
            font-size: 14px;
            color: rgba(255, 255, 255, 1);
            line-height: 2px;
            cursor: pointer;
            outline: none;
        }
        .item-body {
            cursor: pointer;
        }
        .select-color-primary {
            color: #4C3BB1;
        }

    </style>

</head>

<body>

<div class="wrapper-mask" id="wrapper-mask" style="visibility: hidden;"></div>

<div class="wrapper" id="wrapper">

    <!--  site basic config  -->
    <div class="layout-all-row">

        <div class="list-item-center">

            <!--      part1: site name      -->
            <div class="item-row" id="used-uic">
                <div class="item-body">
                    <div class="item-body-display">

                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">已使用的邀请码</div>

                        <?php } else { ?>
                            <div class="item-body-desc">Used Invitation Code</div>

                        <?php } ?>

                        <div class="item-body-tail">
                            <img class="more-img"
                                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAnCAYAAAAVW4iAAAABfElEQVRIS8WXvU6EQBCAZ5YHsdTmEk3kJ1j4HDbGxMbG5N7EwkIaCy18DxtygMFopZ3vAdkxkMMsB8v+XqQi2ex8ux/D7CyC8NR1fdC27RoRszAMv8Ux23ccJhZFcQoA9wCQAMAbEd0mSbKxDTzM6wF5nq+CIHgGgONhgIi+GGPXURTlLhDstDRN8wQA5zOB3hljFy66sCzLOyJaL6zSSRdWVXVIRI9EdCaDuOgavsEJY+wFEY8WdmKlS5ZFMo6xrj9AF3EfukaAbcp61TUBdJCdn85J1yzApy4pwJeuRYAPXUqAqy4tgIsubYCtLiOAjS5jgKkuK8BW1w0APCgOo8wKMHcCzoA+AeDSGKA4AXsOEf1wzq/SNH01AtjUKG2AiZY4jj9GXYWqazDVIsZT7sBGizbAVosWwEWLEuCqZRHgQ4sU4EvLLMCnlgnAt5YRYB9aRoD/7q77kivWFlVZ2R2XdtdiyTUNqpNFxl20bBGT7ppz3t12MhctIuwXEK5/O55iCBQAAAAASUVORK5CYII="/>
                        </div>

                    </div>

                </div>
            </div>
            <div class="division-line"></div>

        </div>

    </div>

    <!--   part 2  -->

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row-title">
                <div class="" style="flex-direction: row;vertical-align: bottom;width: 100%;">
                    <div class="item-body-display">

                        <div class="" style="margin-left: 10px">
                            <?php if ($lang == "1") { ?>
                                未使用邀请码列表
                            <?php } else { ?>
                                Unused Invitation Code
                            <?php } ?>
                        </div>

                        <div class="" style="margin-right: 10px">
                            <button id="uic-manage" type="button" class="create_button" url-value=""
                                    style="margin-top: 7px;font-size: 14px">管理
                            </button>
                        </div>

                    </div>
                </div>
            </div>


            <?php foreach ($unusedList as $key => $value) { ?>
                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-desc"><?php echo $value['code'] ?></div>

                            <div class="item-body-tail">

                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>
            <?php } ?>

            <div class="item-bottom"></div>
        </div>

    </div>
</div>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/jquery/jquery-weui.min.js"></script>

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

    $("#used-uic").click(function () {

        var url = "index.php?action=manage.uic&page=used&lang=" + getLanguage();
        zalyjsCommonOpenPage(url);

    });


    $('#uic-manage').click(function () {
        var language = getLanguage();
        $.actions({
            title: "",
            onClose: function () {
                console.log("close");
            },
            actions: [{
                text: language == 0 ? "Create New Invitation Code" : "生成新的邀请码",
                className: "select-color-primary",
                onClick: function () {
                    //生成邀请码
                    var url = "index.php?action=manage.uic.create&lang=" + language;
                    zalyjsCommonAjaxGet(url, createUicResponse);

                }
            }, {
                text: language == 0 ? "Delete All Unused Invitation Code" : "删除所有未使用的邀请码",
                className: "select-color-primary",
                onClick: function () {
                    //清除所有邀请码
                    var url = "index.php?action=manage.uic.delete&code=all&lang=" + language;
                    zalyjsCommonAjaxGet(url, deleteUicResponse);
                }
            }]
        });
    });

    function createUicResponse(url, result) {
        if (result) {
            var res = JSON.parse(result);
            if (res.errCode == "success") {
                window.location.reload();
            } else {
                alert(getLanguage() == 1 ? "生成邀请码失败" : "create invitation code error");
            }
        }

    }

    function deleteUicResponse(url, result) {
        if (result) {
            var res = JSON.parse(result);
            if (res.errCode == "success") {
                window.location.reload();
            } else {
                alert(getLanguage() == 1 ? "删除失败" : "delete invitation code error");
            }
        }
    }

</script>


</body>
</html>





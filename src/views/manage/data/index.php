<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Site Manage</title>

    <!--    -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel=stylesheet href="../../../public/css/manage_base.css"/>
    <!--    <link rel="stylesheet" href="../../public/css/hint.min.css">-->
    <script src="../../../public/js/jquery.min.js"></script>
    <script src="../../../public/js/template-web.js"></script>

</head>

<body>

<div class="wrapper" id="wrapper">
    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row">
                <div class="item-header">
                    <img class="site-manage-image" src="../../../public/img/manage/site_config.png"/>
                </div>
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc">站点设置</div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../../public/img/manage/more@3x.png"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>


            <div class="item-row">
                <div class="item-header">
                    <img class="site-manage-image" src="../../../public/img/manage/program_manage.png"/>
                </div>
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc">小程序管理</div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../../public/img/manage/more@3x.png"/>
                        </div>
                    </div>

                </div>
            </div>

            <div class="division-line"></div>


            <div class="item-row">
                <div class="item-header">
                    <img class="site-manage-image" src="../../../public/img/manage/user_manage.png"/>
                </div>
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc">用户管理</div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../../public/img/manage/more@3x.png"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row">
                <div class="item-header">
                    <img class="site-manage-image" src="../../../public/img/manage/group_manage.png"/>
                </div>
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc">群组管理</div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../../public/img/manage/more@3x.png"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row">
                <div class="item-header">
                    <img class="site-manage-image" src="../../../public/img/manage/uic_manage.png"/>
                </div>
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc">邀请码</div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../../public/img/manage/more@3x.png"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>

            <div class="item-row">
                <!--                <div class="item-row-inner">-->
                <div class="item-header">
                    <img class="site-manage-image" src="../../../public/img/manage/data_report.png"/>
                </div>
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-desc">数据报表</div>

                        <div class="item-body-tail">
                            <img class="more-img" src="../../../public/img/manage/more@3x.png"/>
                        </div>
                    </div>

                </div>
                <!--                </div>-->
            </div>
            <div class="division-line"></div>
        </div>

    </div>

</div>

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

</body>
</html>





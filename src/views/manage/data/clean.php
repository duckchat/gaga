<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/manage/config.css"/>

</head>

<body>


<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row" id="clean-u2-message">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">清理二人聊天消息</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Clean U2 Message</div>
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

            <div class="item-row" id="clean-group-message">
                <div class="item-body">
                    <div class="item-body-display">
                        <?php if ($lang == "1") { ?>
                            <div class="item-body-desc">清理群组聊天消息</div>
                        <?php } else { ?>
                            <div class="item-body-desc">Clean Group Message</div>
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

</div>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script src="../../../public/sdk/zalyjsNative.js"></script>

<script type="text/javascript">

    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return 1;
        }
        return 0;
    }

    $("#clean-u2-message").click(function () {
        var url = "index.php?action=manage.data.clean&page=u2Message&lang=" + getLanguage();
        zalyjsOpenPage(url);
    });


    $("#clean-group-message").click(function () {
        var url = "index.php?action=manage.data.clean&page=groupMessage&lang=" + getLanguage()
        zalyjsOpenPage(url);
    });

    $("#site-clean-gif-data").click(function () {
        var url = "index.php?action=manage.data.cleanGif&lang=" + getLanguage();
        zalyjsOpenPage(url);
    });
</script>

</body>
</html>





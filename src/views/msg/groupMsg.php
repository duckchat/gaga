<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $siteName;?></title>
    <link rel="stylesheet" href="./public/css/zaly_home.css?_version=<?php echo $versionCode?>" />
    <link rel="stylesheet" href="./public/css/zaly-action-row.css?_version=<?php echo $versionCode?>" />
    <link rel="stylesheet" href="./public/css/zaly_contact.css?_version=<?php echo $versionCode?>" />
    <link rel="stylesheet" href="./public/css/zaly_apply_friend_list.css?_version=<?php echo $versionCode?>" />
    <link rel="stylesheet" href="./public/css/hint.min.css?_version=<?php echo $versionCode?>">
    <link rel="stylesheet" href="./public/css/zaly_msg.css?_version=<?php echo $versionCode?>" />
    <link rel="stylesheet" media="(max-height: 650px)" href="./public/css/zaly_media.css?_version=<?php echo $versionCode?>" />

    <script src="./public/js/jquery.min.js?_version=<?php echo $versionCode?>"></script>
    <script src="./public/js/template-web.js?_version=<?php echo $versionCode?>"></script>
    <script src="./public/js/jquery.i18n.properties.min.js?_version=<?php echo $versionCode?>"></script>
    <script src="./public/js/im/zalyKey.js?_version=<?php echo $versionCode?>"></script>
    <link class="icon" rel="icon" href="./favicon.ico?_version=<?php echo $versionCode?>" type="image/x-icon" />

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

</head>
<input type="hidden" value='<?php echo $siteAddress;?>' class="siteAddress">

<body>
    <!--左列， 以及会话帧-->
    <div style="background-color: red;height: 5rem;display: none" class="site-warning"></div>
    <?php include(dirname(__DIR__) . '/base/baseView.php'); ?>
    <!-- 右边聊天窗口，包括消息帧 -->
    <div class="layout-right msg-chat-dialog" >
        <div class="chat-dialog" style="display: none">
            <?php include(dirname(__DIR__) . '/base/chatDialog.php'); ?>
        </div>
        <div  class="no-chat-dialog-div" style="display: none">
            <img src="<?php echo $siteAddress?>/public/img/msg/no_chat_dialog.png" style=" " class="no-chat-dialog">
        </div>
    </div>

    <div class="layout-right friend-apply-dialog" style="display: none;">
          <?php include(dirname(__DIR__) . '/base/friendApplyList.php'); ?>
    </div>


    <div class="layout-right plugin-list-dialog" style="display: none;">
        <?php include(dirname(__DIR__) . '/base/pluginList.php'); ?>
    </div>

</div>


    <audio id="msg_sound_tip" style="display: none" muted="muted">
        <source src="<?php echo $siteAddress?>/public/voice/definite.mp3" type="audio/mpeg">
    </audio>


<?php include(dirname(__DIR__) . '/base/template.php'); ?>
<?php include(dirname(__DIR__) . '/base/template_msg.php'); ?>

<script src="./public/js/im/zalyAction.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyClient.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyBaseWs.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyIm.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyMsg.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyGroupMsg.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/home/zalyMiniProgram.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/sdk/zalyjsNative.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/qrcode.js?_version=<?php echo $versionCode?>" ></script>
<script src="./public/js/utf.js?_version=<?php echo $versionCode?>" ></script>
<script src="./public/js/jquery.qrcode.js?_version=<?php echo $versionCode?>"></script>
    <script src="./public/js/zalyjsHelper.js?_version=<?php echo $versionCode?>"></script>
    <script src="./public/js/watermark.js?_version=<?php echo $versionCode?>"></script>

<script type="text/javascript">


    requestSiteConfig(ZalyIm);

    $(window).resize(function () {
        setFontSize();
        try{
            var friendsDivHeight = $(".left-body-friends")[0].clientHeight;
            var friendToolsHeight = $(".friend-tools")[0].clientHeight;
            $(".friend-list-contact-row")[0].style.height = Number(friendsDivHeight-friendToolsHeight)+"px";
        }catch (error) {

        }

        try{
            var groupsDivHeight = $(".left-body-groups")[0].clientHeight;
            var groupToolsHeight = $(".group-tools")[0].clientHeight;
            $(".group-list-contact-row")[0].style.height = Number(groupsDivHeight-groupToolsHeight)+"px";
        }catch (error) {

        }
    });
    setFontSize();
    function setFontSize()
    {
        var rem = getRemPx();
        $('html').css('font-size', rem + "px");
    }

    function getRemPx()
    {
        var whdef = 10.66/1440;// 1440,使用10.66PX的默认值
        var wW = window.innerWidth;// 当前窗口的宽度
        var rem = wW * whdef;// 以默认比例值乘以当前窗口宽度,得到该宽度下的相应FONT-SIZE值
        if(rem < 8) {
            rem = 8;
        }
        if(rem > 10.66) {
            rem = 10.66;
        }
        return rem;
    }

    localStorage.setItem(chatTypeKey, DefaultChat);


    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });


    jQuery.i18n.properties({
        name: "lang",
        path: siteAddress + '/public/js/config/',
        mode: 'map',
        language: languageName,
        callback: function () {
            try {
                //初始化页面元素
                $('[data-local]').each(function () {
                    var changeData = $(this).attr("data-local");
                    var changeDatas = changeData.split(":");
                    var changeDataName = changeDatas[0];
                    var changeDataValue = changeDatas[1];
                    $(this).attr(changeDataName, $.i18n.map[changeDataValue]);
                });
                $('[data-local-value]').each(function () {
                    var changeHtmlValue = $(this).attr("data-local-value");
                    $(this).html($.i18n.map[changeHtmlValue]);
                });
                $('[data-local-placeholder]').each(function () {
                    var placeholderValue = $(this).attr("data-local-placeholder");
                    $(this).attr("placeholder", $.i18n.map[placeholderValue]);
                });
            }
            catch(ex){
                console.log(ex.message);
            }
        }
    });
        $(document).on("click", ".preemptiveVersionDiv", function(){
            var qqUrl = "https://jq.qq.com/?_wv=1027&k=5GBN4lJ";
            window.open(qqUrl);
        });

    if (window.Notification && Notification.permission !== "granted") {
        Notification.requestPermission().then(function(result) {
            if (result === 'denied') {
                console.log('Permission wasn\'t granted. Allow a retry.');
                return;
            }
            if (result === 'default') {
                console.log('The permission request was dismissed.');
                return;
            }
        });
    }

</script>


</body>
</html>





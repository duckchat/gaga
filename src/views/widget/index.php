<html>
<header>
    <meta charset="UTF-8">
    <title>DuckChat聊天室</title>
    <link rel=stylesheet href="../../public/css/zaly_msg.css" />
    <link rel="stylesheet" href="../../public/css/zaly_widget.css">
    <link rel="stylesheet" href="../../public/css/hint.min.css">
    <script src="../../public/js/jquery.min.js"></script>
    <script src="../../public/js/template-web.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
    </style>
</header>
<body>

<div class="widget group-chat">
    <div class="widget-header" style="position: relative; text-align:center">
        <span>Group to introduce</span>
        <img  class="see_group_profile" is_show_profile="yes" src="../../public/img/widget/see_group_profile.png" style="width:2rem;height:2rem;position: absolute;right:1rem; bottom: 1rem;">
    </div>
    <div class="right-body" >
        <div class="right-body-chat" >
            <div class="right-chatbox" >
            </div>
            <div id="emojies" style="">
                <div class="emoji-row" style="margin-top: 1rem;">
                    <item class="emotion-item">🙂</item>
                    <item  class="emotion-item">😂</item>
                    <item  class="emotion-item">😊</item>
                    <item  class="emotion-item">😉</item>
                    <item  class="emotion-item">😋</item>
                    <item  class="emotion-item">😎</item>
                    <item  class="emotion-item">😀</item>
                    <item  class="emotion-item">😍</item>
                    <item  class="emotion-item">🤩</item>
                </div>
                <div class="emoji-row">
                    <item  class="emotion-item">😤</item>
                    <item  class="emotion-item" >😬</item>
                    <item class="emotion-item" >😡</item>
                    <item class="emotion-item">😠</item>
                    <item class="emotion-item">🙁</item>
                    <item class="emotion-item">😞</item>
                    <item class="emotion-item">😰</item>
                    <item class="emotion-item">😭</item>
                    <item class="emotion-item">😱</item>
                </div>

                <div class="emoji-row">
                    <item  class="emotion-item">😘</item>
                    <item  class="emotion-item">👄</item>
                    <item  class="emotion-item">💋</item>
                    <item  class="emotion-item">💘</item>
                    <item  class="emotion-item">❤</item>
                    <item  class="emotion-item">💔</item>
                    <item  class="emotion-item">🌹</item>
                    <item  class="emotion-item">🥀</item>
                    <item  class="emotion-item">😷</item>
                </div>

                <div class="emoji-row">
                    <item  class="emotion-item">🙃</item>
                    <item  class="emotion-item">🙄</item>
                    <item  class="emotion-item">😴</item>
                    <item  class="emotion-item">😓</item>
                    <item  class="emotion-item">😳</item>
                    <item  class="emotion-item">🤔</item>
                    <item  class="emotion-item">😐</item>
                    <item  class="emotion-item">🤫</item>
                    <item  class="emotion-item">🤧</item>
                </div>

                <div class="emoji-row">
                    <item  class="emotion-item">🤮</item>
                    <item  class="emotion-item">🤪</item>
                    <item  class="emotion-item">😇</item>
                    <item  class="emotion-item">😏</item>
                    <item  class="emotion-item">💀</item>
                    <item  class="emotion-item">👻</item>
                    <item  class="emotion-item">💩</item>
                    <item  class="emotion-item">😝</item>
                    <item  class="emotion-item">💪</item>
                </div>

                <div class="emoji-row">
                    <item  class="emotion-item">✌</item>
                    <item  class="emotion-item">👌</item>
                    <item  class="emotion-item">👍</item>
                    <item  class="emotion-item">👎</item>
                    <item  class="emotion-item">✊</item>
                    <item  class="emotion-item">👏</item>
                    <item  class="emotion-item">🙏</item>
                    <item  class="emotion-item">🍻</item>
                    <item  class="emotion-item">👅</item>

                </div>
                <div class="emoji-row">
                    <item  class="emotion-item">💢</item>
                    <item  class="emotion-item">💣</item>
                    <item  class="emotion-item">👙</item>
                    <item  class="emotion-item">👑</item>
                    <item  class="emotion-item">💍</item>
                    <item  class="emotion-item">💎</item>
                    <item  class="emotion-item">🌼</item>
                    <item  class="emotion-item">💩</item>
                    <item  class="emotion-item">💊</item>
                </div>
                <div class="emoji-row">
                    <item  class="emotion-item">💰</item>
                    <item  class="emotion-item">💳</item>
                    <item  class="emotion-item">🐵</item>
                    <item  class="emotion-item">🐶</item>
                    <item  class="emotion-item">🦊</item>
                    <item  class="emotion-item">🐱</item>
                    <item  class="emotion-item">🐷</item>
                </div>
            </div>
        </div>
    </div>
    <div class="widget-bottom">
        <?php if($user_id && $session_id) {?>
            <div class="input-box">
                <div style="position: relative">
                    <textarea id="msg_content" onkeydown="sendMsgByKeyDown(event)" class="input-box-text msg_content" placeholder="Type a message…." id="msg_content" maxlength="1000"></textarea>
                    <div style="position: absolute; top:2rem;right:1rem;">
                        <img src="../../public/img/widget/emotions.png" class="emotions" style="width: 3rem;height:3rem;"/>
                        <img src="../../public/img/widget/msg_send.png" class="send_msg" style="width: 3rem;height:3rem;"/>
                    </div>
                </div>
                <div class="input-line"></div>

            </div>
        <?php } else { ?>
            <?php echo "user_id ==" .$user_id;?>
            <button class="login">Join Group and Login</button>
        <?php } ?>

    </div>
</div>

<div class="widget display-group-profile" style="display: none;">
    <div class="widget-header"  style="position: relative; text-align:center">
        <span> Group to introduce</span>
        <img  class="close_group_profile" is_show_profile="no" src="../../public/img/widget/close_group_profile.png" style="width:2rem;height:2rem;position: absolute;right:1rem; bottom: 1rem;">
    </div>
    <div class="widget-content">
        <div class="name" >
            <img  class="group_avatar_<?php echo $groupId;?>" src="../../public/img/msg/group_default_avatar.png"/>
            <div style="">
                <?php echo $name;?>
            </div>
        </div>
        <div class="line"></div>
        <div class="introduce">
            <div class="tip" >
                Group to introduce
            </div>
            <div class="desc" >
                <?php echo $description;?>
            </div>
        </div>
    </div>
    <div class="widget-bottom">
        <?php if($user_id && $session_id) {?>
            <button class="open_chat">open chat</button>
        <?php } else { ?>
            <button class="login">Join Group and Login</button>
        <?php } ?>
    </div>
</div>


<input type="hidden" class="groupId" value="<?php echo $groupId;?>"/>
<intput type="hidden" data="<?php echo $session_id;?>" class="session_id" />
<intput type="hidden" data="<?php echo $user_id;?>" class="token" />
<intput type="hidden" data="<?php echo $nickname;?>" class="nickname" />
<intput type="hidden" data="<?php echo $avatar;?>" class="self_avatar" />


<iframe class="widget login-platform" style="display: none;" name="login-platform">

</iframe>


<?php include(dirname(__DIR__) . '/widget/template_widget.php'); ?>

<script src="../../public/js/im/zalyKey.js"></script>
<script src="../public/js/im/zalyAction.js"></script>
<script src="../../public/js/im/zalyClient.js"></script>
<script src="../../public/js/im/zalyBaseWs.js"></script>
<script src="../../public/js/im/zalyIm.js"></script>
<script src="../public/js/im/zalyGroupMsg.js"></script>
<script src="../../public/js/im/zalyMsg.js"></script>
<script src="../../public/sdk/zalyjsNative.js"></script>
<script src="../../public/js/im/zalyHelper.js"></script>

<script type="text/javascript">

    localStorage.setItem(chatTypeKey, WidgetChat);
    var groupId = $(".groupId").val();
    localStorage.setItem(chatSessionIdKey, groupId);
    localStorage.setItem(groupId, GROUP_MSG);
    getMsgFromRoom(groupId);

    $(document).on("click", ".emotions", function () {
        document.getElementById("emojies").style.display = "block";
    });

    $(document).mouseup(function(e){
        var targetId = e.target.id;
        var targetClassName = e.target.className;

        if(targetClassName != "emotion-item") {
            document.getElementById("emojies").style.display = "none";
        }
    });

    $(".msg_content").bind('input porpertychange',function() {
        if ($(".msg_content").val().length > 0) {
            $(".msg_content").addClass("color33");
        }
    });

    $(document).on("click", '.emotion-item', function () {
        var content = $(this).html();
        var msgContent = $(".msg_content").val();
        msgContent += content;
        $(".msg_content").val(msgContent);
    });


    $(document).on("click", ".see_group_profile", function () {
        $(".display-group-profile")[0].style.display = 'block';
        $(".group-chat")[0].style.display = 'none';
    });
    $(document).on("click", ".close_group_profile", function () {
        openChatDialog();
    });
    $(document).on("click", ".open_chat", function () {
        openChatDialog();
    });

    function openChatDialog()
    {
        $(".display-group-profile")[0].style.display = 'none';
        $(".group-chat")[0].style.display = 'block';
    }

    $(document).on("click", ".login", function () {
        var platformLoginUrl = $(".platform_login_url").data(".platform_login_url");
        $(".display-group-profile")[0].style.display = 'none';
        $(".group-chat")[0].style.display = 'none';
        $(".login-platform")[0].style.display = 'block';
        var src = platformLoginUrl;
        $(".login-platform").attr("src", src);
    });

</script>


</body>
</html>
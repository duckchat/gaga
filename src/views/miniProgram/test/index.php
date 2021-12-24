
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GIF小程序</title>
    <!-- Latest compiled and minified CSS -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <script type="text/javascript" src="../../../public/js/jquery.min.js"></script>
    <script src="../../../public/sdk/zalyjsNative.js"></script>
    <script src="../../../public/js/template-web.js"></script>
    <style>
        body, html {
            font-size: 10.66px;
            width: 100%;
        }
        .zaly_container {
            height: 18rem;
        }
        .gif {
            width:5rem;
            height:5rem;
        }
        .gif_sub_div{
            display: flex;
        }
        .gif_div_hidden {
            display: none;
        }
        .sliding {
            margin-right: 1rem;
            width:5px;
        }
        .slide_div {
            margin-top: 3rem;
            text-align: center;
        }
        .add_gif{
            height: 5rem;
            cursor: pointer;
        }
        .del_gif{
            width: 2rem;
            height: 2rem;
            margin-top: -1rem;
            position: absolute;
            margin-left: 4.5rem;
            display: none;
        }
        .gif_content_div{
            position: relative;
            width: 5rem;
            height: 5rem;
            display: flex;
            margin-left: 2rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>

<div class="zaly_container" >

    <input type="hidden" class="roomType" value='<?php echo $roomType;?>'>
    <input type="hidden" class="toId" value='<?php echo $toId;?>'>
    <input type="hidden" class="fromUserId" value='<?php echo $fromUserId;?>'>
</div>

<div class="slide_div">
    <button class="text">text</button>
</div>

<script src="../../../public/js/im/zalyKey.js"></script>
<script src="../../../public/js/im/zalyAction.js"></script>
<script src="../../../public/js/im/zalyClient.js"></script>
<script src="../../../public/js/im/zalyBaseWs.js"></script>

<script id="tpl-gif" type="text/html">
    <div class='gif_content_div'>
        <img id="gifId_{{num}}" src='{{gifUrl}}' class='gif' gifId='{{gifId}}' isDefault='{{isDefault}}'>
        <img src='../../public/img/msg/btn-x.png' class='del_gif  {{gifId}}' gifId="{{gifId}}">
    </div>
</script>

<script type="text/javascript">
    var line = 0;
    roomType = $(".roomType").val();
    fromUserId = $(".fromUserId").val();
    toId = $(".toId").val();
    var startX, startY, moveEndX,moveEndY,timeOut;
    var imgObject={};
    var addGifType = "add_gif";
    var delGifType = "del_gif";

    var languageNum = getLanguage();
    $(".text").on("click", function () {
        var reqData = {
            "type":"text",
        };
        sendPostToServer(reqData);
    });
    function sendPostToServer(reqData,)
    {
        $.ajax({
            method: "POST",
            url:"./index.php?action=miniProgram.test.index&lang="+languageNum,
            data: reqData,
            success:function (data) {
                data = JSON.parse(data);
                if(data.errorCode == 'error.alert') {
                    alert(data.errorInfo);
                    return false;
                }
            }
        });
    }


</script>
</body>
</html>
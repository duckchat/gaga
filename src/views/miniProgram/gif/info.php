
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GIF小程序</title>
    <!-- Latest compiled and minified CSS -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <script type="text/javascript" src="../../../public/js/jquery.min.js"></script>
    <script src="../../../public/js/template-web.js"></script>

    <style>
        body, html {
            font-size: 10.66px;
            width: 100%;
            height:100%;
            padding: 0;
            margin: 0;
        }
        .zaly_container {
            width: 100%;
            height:100%;
            display: block;
            position: relative;
        }
        .gif_div{
            text-align: center;
            position: absolute;
            top:30%;
            left:0;
            right:0;
            margin:auto;

        }
        .save_button {
            width:8rem;
            height:3.38rem;
            background:rgba(76,59,177,1);
            border-radius:0.38rem;
            font-size:1.5rem;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(255,255,255,1);
        }
        .gif_div_button {
            display: flex;
            justify-content: center;
            margin-top: 5rem;
            cursor: pointer;
            outline: none;
        }
    </style>
</head>
<body>

<div class="zaly_container" >
    <input type="hidden" class="roomType" value='<?php echo $roomType;?>'>
    <input type="hidden" class="toId" value='<?php echo $toId;?>'>
    <input type="hidden" class="fromUserId" value='<?php echo $fromUserId;?>'>
    <input type="hidden" class="gifId" value='<?php echo $gifId;?>'>
    <input type="hidden" class="gifUrl" value='<?php echo $gifUrl;?>'>
    <input type="hidden" class="gifWidth" value='<?php echo $width;?>'>
    <input type="hidden" class="gifHeight" value='<?php echo $height;?>'>
    <input type="hidden" class="isDefault" value='<?php echo $isDefault;?>'>

</div>

<script id="tpl-gif" type="text/html">
    <div class="gif_div" id="gifDiv">
        <img id="gifInfo" src='{{gifUrl}}' class='gif' gifId='{{gifId}}'>
        {{if isDefault == "0" }}
        <div class="gif_div_button">
            <button class="save_gif save_button " gifId='{{gifId}}'>收藏</button>
        </div>
        {{ else }}
        <div class="gif_div_button">
            <button class="save_button" gifId='{{gifId}}' disabled>已收藏</button>
        </div>
        {{/if}}
    </div>

</script>

<script type="text/javascript">

    UserClientLangZH = "1";
    UserClientLangEN = "0";

    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return UserClientLangZH;
        }
        return UserClientLangEN;
    }

    roomType = $(".roomType").val();
    fromUserId = $(".fromUserId").val();
    toId = $(".toId").val();
    gifId = $(".gifId").val();
    gifUrl = $(".gifUrl").val();
    gifWidth = $(".gifWidth").val();
    gifHeight = $(".gifHeight").val();
    isDefault = $(".isDefault").val();

    var imgObject = {};
    var saveGifType = "save_gif";

    var languageNum = getLanguage();

    var html = template("tpl-gif", {
        gifId:gifId,
        gifUrl:gifUrl,
        isDefault:isDefault
    });

    $(".zaly_container").append(html);

    var src = $("#gifInfo").attr("src");
    autoImgSize(src, 200, 200);

    function autoImgSize(src, h, w)
    {
        var image = new Image();
        image.src = src;
        image.onload = function (ev) {
            var imageNaturalWidth  = image.naturalWidth;
            var imageNaturalHeight = image.naturalHeight;
            if (imageNaturalWidth < w && imageNaturalHeight<h) {
                imgObject.width  = imageNaturalWidth == 0 ? w : imageNaturalWidth;
                imgObject.height = imageNaturalHeight == 0 ? h : imageNaturalHeight;
            } else {
                if (w / h <= imageNaturalWidth/ imageNaturalHeight) {
                    imgObject.width  = w;
                    imgObject.height = w* (imageNaturalHeight / imageNaturalWidth);
                } else {
                    imgObject.width  = h * (imageNaturalWidth / imageNaturalHeight);
                    imgObject.height = h;
                }
            }
            $("#gifInfo")[0].style.width =  imgObject.width+"px";
            $("#gifInfo")[0].style.height =  imgObject.height+"px";
        }

    }

    function isMobile() {
        if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
            return true;
        }
        return false;
    }

    $(".save_gif").on("click", function () {
        var gifId = $(this).attr("gifId");
        var reqData = {
            gifId : gifId,
            type:saveGifType,
        }
        sendPostToServer(reqData);
        return false;
    });

    function sendPostToServer(reqData)
    {
        $.ajax({
            method: "POST",
            url:"./index.php?action=miniProgram.gif.index&type="+saveGifType+"&lang="+languageNum,
            data: reqData,
            success:function (data) {
                try{
                    data = JSON.parse(data);
                    if(data.errorCode == 'error.alert') {
                        alert(data.errorInfo);
                        return false;
                    }
                    $(".save_gif").html("已收藏");
                }catch (error) {

                }
            }
        });
    }

    function updateServerGif(fileId)
    {
        var reqData = {
            gifId : fileId,
            type:addGifType,
            width:imgObject.width,
            height:imgObject.height
        }
        sendPostToServer(reqData, addGifType);
    }

</script>
</body>
</html>
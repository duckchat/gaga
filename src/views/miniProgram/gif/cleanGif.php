<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>GIF</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>
    <link rel="stylesheet" href="../../public/manage/config.css"/>
    <script src="../../../public/js/template-web.js"></script>
    <script type="text/javascript" src="../../public/sdk/zalyjsNative.js"></script>

    <style>

        body,html{
            height:100%;
            width: 100%;
            padding:0;
            margin:0;
        }

        .item-body-select {
            height: 100%;
            width:100%;
            display: flex;
            justify-content: start;
            align-items: center;
        }
        .list-item-center {
            display: flex;
            justify-content: start;
            align-items: center;
            width: 100%;
            padding-bottom: 0px;
        }

        .clean-button {
            width:100%;
            height:100%;
            background:rgba(250,250,250,0.9);
            font-size:1.31rem;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(27,27,27,1);
            line-height:1.88rem;
            cursor: pointer;
            outline: none;
        }


        .gif_div {
            width: 25%;
            height:80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #D8D8D8;
            cursor: pointer;
        }
        .gif_div img {
            width: 70px;
            height:70px;
        }
        #wrapper {
            height:90%;
            width: 100%;
            position: relative;
            margin-bottom: 30px;
        }
        .select_img{
            position:absolute;
            width: 20px;
            height:20px;
            right:10px;
            top:10px;
            margin:auto;
            display:none;
        }
        .layout-all-row {
            display: block;
            height: 100%;
            overflow-y: scroll;
            background-color: white;
        }
        .clean-button-div {
            position: absolute;
            bottom: 10px;
            left: 0;
            right:0;
            margin: 0 auto;
            height: 10%;
            display:flex;
            justify-content: center;
            align-items: center;
        }
    </style>

</head>

<body>


<script id="tpl-gif" type="text/html">
    <div class="gif_div" style="position: relative;"  gifId="{{gifId}}">
        <div style="width:100%;display: flex;justify-content: center;align-items: center">
            <img  style="background-size:contain;" border="0" class="gifImg gifImg_{{gifId}}" gifUrl="{{gifUrl}}" gifId="{{gifId}}"/>
        </div>
        <img style=" width: 20px;height:20px;" class="select_img select_img_{{gifId}}" src="../../public/img/manage/unselect.png" default="0" gifId="{{gifId}}"/>
    </div>
</script>

    <div style="position:relative;height: 100%;width: 100%;">
        <div class="wrapper" id="wrapper">

            <div class="layout-all-row">



            </div>

        </div>
        <div class="clean-button-div" style="">
            <button class="clean-button" operation="clean"><?php if($lang == 1):?>整理 <?php else: ?> Clean <?php endif; ?></button>
        </div>
    </div>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/jquery/jquery-weui.min.js"></script>
<script type="text/javascript" src="../../public/js/jquery-confirm.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript">
    gifs     = '<?php echo $gifs;?>';
    gifArr   = JSON.parse(gifs);
    var line = 0;
    roomType = $(".roomType").val();
    fromUserId = $(".fromUserId").val();
    toId = $(".toId").val();

    function isMobile() {
        if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
            return true;
        }
        return false;
    }

    function displayGif(gifArr)
    {
        var gifLength = gifArr.length ;

        if(gifLength>0) {
            for(var i=1; i<=gifLength ;i ++) {
                var num = i-1;
                var gif = gifArr[num];

                var gifId = "";
                var gifUrl="";
                var isDefault=0;
                try{
                    gifId=gif.gifId;
                    gifUrl=gif.gifUrl;
                    isDefault=gif.isDefault;
                }catch (error) {
                    gifId="";
                }
                if(i == 1 || (i%4)== 1) {
                    var html = '';
                    line = line+1;
                    html += " <div class=\"list-item-center\"><div class='item-body-select'  gif-div='"+(line-1)+"'>";
                }
                html +=template("tpl-gif", {
                    num:i,
                    gifUrl:gifUrl,
                    gifId:gifId,
                    isDefault:isDefault
                });
                if(i>1 && (i-4)%4 == 0) {
                    line = line+1;
                    html += "</div></div>";
                    $(".layout-all-row").append(html);
                } else if(i==gifLength) {
                    html +="</div></div>";
                    $(".layout-all-row").append(html);
                }
            }
            $(".gifImg").each(function (index, gifImg) {
                var gifUrl = $(gifImg).attr("gifUrl");
                var  src = "./index.php?action=http.file.downloadFile&fileId="+gifUrl;
                if(isMobile()) {
                    src =  " /_api_file_download_/?fileId="+gifUrl;
                }
                $(gifImg)[0].style.backgroundImage="url("+src+") ";
                $(gifImg)[0].style.backgroundRepeat = "no-repeat";
            });

            var height =  $(".gif_div")[0].clientWidth;
            $(".gif_div").each(function (index, gif) {
                $(gif)[0].style.height =  height+"px";
                $(gif)[0].style.width  =  height+"px";
            });
        }
    }
    displayGif(gifArr);


    function handleClientGetGif(url, result)
    {
        console.log(JSON.stringify(result));
        try{
            var data = JSON.parse(result);
            console.log(data.length);
            if(data.length>0) {
                displayGif(data);
                return;
            }
        }catch (error) {

        }
        ending = true;
    }

    currentPageNum = 1;
    ending = false;

    $(".layout-all-row").scroll(function () {

        var pwLeft = $(".layout-all-row")[0];
        var ch  = pwLeft.clientHeight;
        var sh = pwLeft.scrollHeight;
        var st = $(".layout-all-row").scrollTop();

        if((sh - ch - st) < 10 && !ending){
            currentPageNum = ++currentPageNum;
            var url = "index.php?action=miniProgram.gif.cleanGif&page="+currentPageNum;
            zalyjsCommonAjaxGet(url, handleClientGetGif);
        }
    });



    function getLanguage() {
        var nl = navigator.language;
        if ("zh-cn" == nl || "zh-CN" == nl) {
            return 1;
        }
        return 0;
    }
    var lang = getLanguage();

    function isMobile() {
        if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
            return true;
        }
        return false;
    }

    $(".clean-button").click(function () {
        var operation = $(this).attr('operation');
        if(operation == 'clean') {
            $(".select_img").each(function (index, selectImg) {
                $(selectImg)[0].style.display = "block"
            });
            var tip = getLanguage() == 1 ? "删除" : "Delete";
            $(this).attr("operation", "delete");
            $(".clean-button").html(tip);
            return;
        }
        var delGifId = new Array();
        $(".select_img[default='1']").each( function (index, selectImg) {
            var gifId = $(selectImg).attr("gifId");
            delGifId.push(gifId);
        });
        var length = delGifId.length;
        if(length < 1) {
            var tip='请选择需要删除的GIF';
            if(lang != 1) {
                tip='Please Select GIF';
            }
            alert(tip);
            return;
        }

        $.modal({
            title: lang == 1 ? '清理Gif' : 'Clean Gif',
            text: lang == 1 ? '操作无法撤销，确认删除？' : 'Cannot be undo, Confirm?',
            buttons: [
                {
                    text: lang == 1 ? "取消" : "cancel", className: "default", onClick: function () {
                        // alert("cancel");
                    }
                },
                {
                    text: lang == 1 ? "确定" : "confirm", className: "main-color", onClick: function () {
                        cleanGif(delGifId);
                    }
                },

            ]
        });

    });

    $(".gif_div").on("click", function () {
        var gifId = $(this).attr("gifId");
        var defaultValue = $(".select_img_"+gifId).attr("default");
        defaultValue = Number(defaultValue);
        var selectSrc = "../../public/img/manage/selected.png";
        var unselectSrc = "../../public/img/manage/unselect.png";

        if(defaultValue == 1) {
            $(".select_img_"+gifId).attr("src", unselectSrc);
            $(".select_img_"+gifId).attr("default", 0);
        } else {
            $(".select_img_"+gifId).attr("src", selectSrc);
            $(".select_img_"+gifId).attr("default", 1);
        }
    });

    function cleanGif(delGifId) {

        var url = "index.php?action=miniProgram.gif.cleanGif&lang=" + getLanguage();

        var data = {
            "delGifIds": delGifId,
        };

        $.ajax({
            url: url,
            method: "POST",
            data: data,
            success: function (result) {

                if (result) {
                    var res = JSON.parse(result);

                    if ("success" == res.errCode) {
                        window.location.reload();
                    } else {
                        alert("操作失败，原因：" + res.errInfo);
                    }

                } else {
                    alert("操作失败");
                }
            },
            error: function (err) {
                alert("error");
            }
        });

    }

</script>

</body>
</html>





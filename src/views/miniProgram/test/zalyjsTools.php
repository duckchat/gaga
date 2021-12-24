<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>zalyjs测试工具</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <script type="text/javascript" src="../../../public/js/jquery.min.js"></script>
    <script src="../../../public/sdk/zalyjsNative.js"></script>


    <style>

        .test-tools {
            width: 100%;
            text-align: center;
        }

        .test-button {
            margin-top: 10px;
            width: 80%;
            height: 40px;
            border-width: 0;
            border-radius: 3px;
        }

        #clostButton {
            margin-top: 20px;
            margin-bottom: 20px;
            background-color: red;
            color: white;
        }

        .img-button {
            width: 100%;
            height: 100px;
        }

        .test-img {
            width: 100px;
            height: 100px;
            cursor: pointer;
        }

        .my-friend {

            margin-top: 30px;
            margin-bottom: 30px;
        }

    </style>
</head>

<body>

<div class="test-tools">

    <button class="test-button" onclick="gotoPage();">跳转url</button>

    <button class="test-button" onclick="gotoNewPage()">打开新页面</button>


    <button class="test-button" onclick="backPage()">返回</button>

    <button class="test-button" id="clostButton" onclick="closePage()">关闭页面</button>

    <!--  上传图片  -->

    <div class="img-button" onclick="uploadImage();">
        <img class="test-img"
             src="http://img.zcool.cn/community/01b6be58fd7f7da8012160f750ebae.JPG@900w_1l_2o_100sh.jpg">
    </div>


    <div style="margin-top: 20px">测试GotoClient工具 Goto：</div>

    <div>
        <button class="test-button" id="gotoHome" onclick="gotoTest('home','')">Goto首页</button>
    </div>


    <button class="test-button" id="gotoChats" onclick="gotoTest('chats','')">聊天列表</button>

    <div class="my-friend">
        <?php

        if ($myFriendProfile) {
            echo $myFriendProfile["userId"];
        } else {
            echo "测试【用户资料】【二人聊天页面】前，请添加你的好友";
        }

        ?>

        <button class="test-button" id="gotoU2Profile"
                onclick="gotoTest('u2Profile','<?php echo $myFriendProfile["userId"] ?>')">
            好友用户资料
        </button>

        <button class="test-button" id="gotoU2Msg"
                onclick="gotoTest('u2Msg','<?php echo $myFriendProfile["userId"] ?>')">好友二人聊天页面
        </button>
    </div>


    <div class="my-friend">
        <?php

        if ($notMyFriendProfile) {
            echo $notMyFriendProfile["userId"];
        } else {
            echo "请添加注册一个非好友的新用户";
        }

        ?>

        <button class="test-button" id="gotoU2Profile"
                onclick="gotoTest('u2Profile','<?php echo $notMyFriendProfile["userId"] ?>')">
            非好友用户资料
        </button>

        <button class="test-button" id="gotoU2Msg"
                onclick="gotoTest('u2Msg','<?php echo $notMyFriendProfile["userId"] ?>')">
            非好友二人聊天页面
        </button>
    </div>


    <div class="my-friend">

        <?php

        if ($myGroupProfile) {
            echo $myGroupProfile["groupId"];
        } else {
            echo "请创建一个我的群组";
        }

        ?>

        <button class="test-button" id="gotoGroupProfile"
                onclick="gotoTest('groupProfile',' <?php echo $myGroupProfile["groupId"] ?>')">
            我的群组资料页
        </button>

        <button class="test-button" id="gotoGroupMsg"
                onclick="gotoTest('groupMsg','<?php echo $myGroupProfile["groupId"] ?>')">
            我的群组聊天界面
        </button>
    </div>

    <div class="my-friend">

        <?php

        if ($notMyGroupProfile) {
            echo $notMyGroupProfile["groupId"];
        } else {
            echo "请创建一个非我的群组";
        }

        ?>

        <button class="test-button" id="gotoGroupProfile"
                onclick="gotoTest('groupProfile',' <?php echo $notMyGroupProfile["groupId"] ?>')">
            非我的群组资料页
        </button>

        <button class="test-button" id="gotoGroupMsg"
                onclick="gotoTest('groupMsg','<?php echo $notMyGroupProfile["groupId"] ?>')">
            非群组聊天界面
        </button>

    </div>

    <button class="test-button" id="gotoContracts" onclick="gotoTest('contracts','')">通讯录列表</button>

    <button class="test-button" id="gotoNewFriend" onclick="gotoTest('newFriend','')">新朋友</button>

    <button class="test-button" id="gotoGroups" onclick="gotoTest('groups','')">群组列表</button>


    <div class="my-friend">

        <?php

        echo "userId=";

        ?>

        <br>
        <button class="test-button" id="gotoAddFriend"
                onclick="gotoTest('addFriend','')">
            申请添加好友(userId为空)
        </button>
        <br>

        <?php

        echo $myFriendProfile["userId"];

        ?>

        <br>
        <button class="test-button" id="gotoAddFriend"
                onclick="gotoTest('addFriend','<?php echo $myFriendProfile["userId"] ?>')">
            申请添加好友(已经是好友)
        </button>
        <br>

        <?php

        if ($notMyFriendProfile["userId"]) {
            echo $notMyFriendProfile["userId"];
        } else {
            echo "请创建一个非好友的用户";
        }

        ?>

        <br>
        <button class="test-button" id="gotoAddFriend"
                onclick="gotoTest('addFriend','<?php echo $notMyFriendProfile["userId"] ?>')">
            申请添加好友（userId不为空）
        </button>
    </div>

    <button class="test-button" id="gotoMe" onclick="gotoTest('me','')">个人帧</button>

    <button class="test-button" id="gotoMiniProgram" onclick="gotoTest('miniProgram','<?php echo $miniProgramId ?>')">
        跳转到小程序首页
    </button>

    <button class="test-button" id="gotoMiniProgramAdmin"
            onclick="gotoTest('miniProgramAdmin','<?php echo $miniProgramId ?>')">跳转到小程序管理页
    </button>


</div>

<script>

    function gotoPage() {
        var url = "https://duckchat.akaxin.com/";
        zalyjsOpenPage(url);
    }

    function gotoNewPage() {

        var url = "./index.php?action=miniProgram.test.tools";
        zalyjsOpenNewPage(url);
    }

    function backPage() {
        zalyjsBackPage();
    }

    function closePage() {
        zalyjsClosePage();
    }


    function uploadImage() {

        alert("上传图片");
        zalyjsImageUpload(uploadImageResult);

    }

    function uploadImageResult(result) {
        alert(result.fileId);
        var fileId = result;

        var imageSrc = "./_api_file_download_/test?fileId=" + fileId;
        $(".test-img").attr("src", imageSrc);
    }

    function gotoTest(page, xarg) {
        zalyjsGoto(null, page, xarg);
    }

</script>

</body>

</html>

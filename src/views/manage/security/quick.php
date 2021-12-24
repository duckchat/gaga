<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>安全配置<?php } else { ?>Security configuration<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../../public/jquery/weui.min.css"/>
    <link rel="stylesheet" href="../../public/jquery/jquery-weui.min.css"/>

    <link rel="stylesheet" href="../../public/manage/config.css"/>
    <style>

        html, body {
            padding: 0px;
            margin: 0px;
            font-family: PingFangSC-Regular, "MicrosoftYaHei";
            overflow: hidden;
            width: 100%;
            height: 100%;
            background: rgba(245, 245, 245, 1);
            font-size: 14px;

        }

        .wrapper {
            width: 100%;
        }

        .layout-all-row {
            width: 100%;
            /*background: white;*/
            background: rgba(245, 245, 245, 1);;
            display: flex;
            align-items: stretch;
            overflow: hidden;
            flex-shrink: 0;

        }

        .item-row {
            background: rgba(255, 255, 255, 1);
            display: flex;
            flex-direction: row;
            text-align: center;
            height: 100%;
            cursor: pointer;
            /*margin-bottom: 2rem;*/
        }

        /*.item-row:hover{*/
        /*background: rgba(255, 255, 255, 0.2);*/
        /*}*/

        .item-row:active {
            background: rgba(255, 255, 255, 0.2);
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
            border-radius: 50%;
        }

        .item-body {
            width: 100%;
            height: 100%;
            margin-left: 1rem;
            margin-top: 7px;
            flex-direction: row;
        }

        .list-item-center {
            width: 100%;
            height: 110px;
            /*height: 11rem;*/
            /*background: rgba(255, 255, 255, 1);*/
            /*padding-left: 1rem;*/

        }

        .item-body-display {
            display: flex;
            height: 100%;
            justify-content: space-between;
            line-height: 110px;
            margin-top: 14px;
        }

        .item-body-tail {
            margin-right: 10px;
        }

        .item-body-desc {
            width: 100%;
            height: 100%;
            text-align: left;
            line-height: 18px;
        }

        .more-img {
            width: 8px;
            height: 13px;
            /*border-radius: 50%;*/
        }

        .division-line {
            height: 1px;
            background: rgba(243, 243, 243, 1);
            margin-left: 40px;
            overflow: hidden;
        }
        .check_img {
            width: 15px;
            height:15px;
        }
        .tip{
            height:12px;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(102,102,102,1);
            line-height:12px;
            margin-left: 10px;
        }
        .tip_div{
            margin-left: 5px;
            margin-top: 12px;
        }
        .item-body-tail {
            width: 20px;
            max-width: 25px;
            height:110px;
            display: flex;
            justify-content: start;
            align-items: start;
        }


    </style>
</head>

<body>


<div class="wrapper" id="wrapper">
    <div class="layout-all-row" style="margin-top:10px;">

        <div class="list-item-center pwd_item" onclick="updatePwdType('pwd_convenience')">
            <div class="item-row">
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-tail">
                            <?php if($pwdContainCharacterType == "pwd_convenience") { ?>
                                <img class="check_img pwd_convenience_img"  pwd_type="pwd_convenience" src="../../public/img/manage/checked.png" >
                            <?php } else { ?>
                                <img class="check_img pwd_convenience_img"  pwd_type="pwd_convenience" src="../../public/img/manage/uncheck.png" >
                            <?php } ?>
                        </div>
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                方便
                            <?php } else { ?>
                                Convenience
                            <?php } ?>
                            <div class="tip_div">
                                <span class="tip" >密码长度6-32位</span>
                                <span class="tip"><br/>不限制密码组成</span>
                                <span class="tip"><br/>每日密码错误次数为10</span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="division-line"></div>

        </div>
    </div>


    <div class="layout-all-row" style="margin-top: 20px">
        <div class="list-item-center pwd_item"  onclick="updatePwdType('pwd_default')">
            <div class="item-row" >
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-tail">
                            <?php if($pwdContainCharacterType == "pwd_default") { ?>
                                <img class="check_img pwd_default_img"  pwd_type="pwd_default" src="../../public/img/manage/checked.png" >
                            <?php } else { ?>
                                <img class="check_img pwd_default_img"  pwd_type="pwd_default" src="../../public/img/manage/uncheck.png" >

                            <?php } ?>

                        </div>
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                默认
                            <?php } else { ?>
                                Default
                            <?php } ?>

                            <div class="tip_div">
                                <span class="tip" >密码长度6-32位</span>
                                <span class="tip"><br/>必须有字母、数字</span>
                                <span class="tip"><br/>每日密码错误次数为5</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="division-line"></div>
        </div>
    </div>

    <div class="layout-all-row" style="margin-top: 20px">
        <div class="list-item-center pwd_item" onclick="updatePwdType('pwd_security')">
            <div class="item-row" >
                <div class="item-body">
                    <div class="item-body-display">
                        <div class="item-body-tail">
                            <?php if($pwdContainCharacterType == "pwd_security") { ?>
                                <img class="check_img pwd_security_img"  pwd_type="pwd_security" src="../../public/img/manage/checked.png" >
                            <?php } else { ?>
                                <img class="check_img pwd_security_img"  pwd_type="pwd_security" src="../../public/img/manage/uncheck.png" >

                            <?php } ?>
                        </div>
                        <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                安全
                            <?php } else { ?>
                                Security
                            <?php } ?>
                            <div class="tip_div">
                                <span class="tip" >密码长度8-32位</span>
                                <span class="tip"><br/>必须有字母、特殊符号、数字</span>
                                <span class="tip"><br/>每日密码错误次数为3</span>
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
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript">

    function updatePwdType(pwdType)
    {
        var uncheckSrc = "../../public/img/manage/uncheck.png";
        var checkSrc = "../../public/img/manage/checked.png";
        switch (pwdType) {
            case "pwd_default":
                $(".pwd_convenience_img").attr("src", uncheckSrc);
                $(".pwd_security_img").attr("src", uncheckSrc);
                $(".pwd_default_img").attr("src", checkSrc);
                break;
            case "pwd_convenience":
                $(".pwd_convenience_img").attr("src", checkSrc);
                $(".pwd_security_img").attr("src", uncheckSrc);
                $(".pwd_default_img").attr("src", uncheckSrc);

                break;
            case "pwd_security":
                $(".pwd_convenience_img").attr("src", uncheckSrc);
                $(".pwd_security_img").attr("src", checkSrc);
                $(".pwd_default_img").attr("src", uncheckSrc);
                break;
        }
        var data = {
            "pwd_type" : pwdType
        }
        var url = "index.php?action=manage.security.update";

        zalyjsCommonAjaxPostJson(url, data, updateResponse);
    }

    function updateResponse(url, data, result) {
        var res = JSON.parse(result);
        if ("success" == res.errCode) {
            window.location.reload();
        } else {
            alert("error : " + res.errInfo);
        }
    }

</script>

</body>
</html>





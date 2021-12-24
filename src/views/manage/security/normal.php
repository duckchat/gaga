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
            height: 50px;
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
            height: 50px;
            margin-left: 1rem;
            margin-top: 7px;
            flex-direction: row;
        }

        .list-item-center {
            width: 100%;
            height: 40px;
        }

        .item-body-display {
            display: flex;
            justify-content: space-between;
            line-height: 3rem;
        }

        .item-body-tail {
            margin-right: 10px;
        }

        .item-body-desc {
            width: 100%;
            text-align: left;
        }

        .more-img {
            width: 8px;
            height: 13px;
            /*border-radius: 50%;*/
        }

        .division-line {
            height: 1px;
            background: rgba(243, 243, 243, 1);
            margin-left: 10px;
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

        .header_tip_font {
            font-size: 18px;
        }
        .tip{
            height:17px;
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(157,155,159,1);
            line-height:17px;
            margin-left: 10px;
        }
    </style>
</head>

<body>


<div class="wrapper" id="wrapper">
    <div class="layout-all-row" style="margin-top:10px;">
       <div style="width: 100%;">
           <span class="tip">
               <?php if ($lang == "1") { ?>
                   用户名配置
               <?php } else { ?>
                   Username configuration
               <?php } ?>
           </span>
           <div class="list-item-center">
               <div class="item-row">
                   <div class="item-body">
                       <div class="item-body-display loginNameMinLength" onclick="showLoginNameMinLength()">
                           <?php if ($lang == "1") { ?>
                               <div class="item-body-desc">最小长度</div>
                           <?php } else { ?>
                               <div class="item-body-desc"> Min length</div>
                           <?php } ?>
                           <div class="item-body-tail">
                               <div class="item-body-value" id="loginNameMinLength"> <?php echo $loginNameMinLength; ?></div>
                               <div class="item-body-value">
                                   <img class="more-img" src="../../public/img/manage/more.png"/>
                               </div>
                           </div>

                       </div>

                   </div>
               </div>
               <div class="division-line"></div>
           </div>
           <div class="list-item-center">
               <div class="item-row">
                   <div class="item-body">
                       <div class="item-body-display loginNameMaxLength" onclick="showLoginNameMaxLength()">
                           <?php if ($lang == "1") { ?>
                               <div class="item-body-desc ">最大长度</div>
                           <?php } else { ?>
                               <div class="item-body-desc"> Max length</div>
                           <?php } ?>
                           <div class="item-body-tail">
                               <div class="item-body-value" id="loginNameMaxLength"> <?php echo $loginNameMaxLength; ?></div>
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

    <div class="layout-all-row" style="margin-top: 20px">
        <div style="width: 100%;">
            <span class="tip">
                <?php if ($lang == "1") { ?>
                    密码配置
                <?php } else { ?>
                    Password configuration
                <?php } ?>
            </span>
            <div class="list-item-center">
                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display passwordMinLength" onclick="showPwdMinLength()">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">最小长度 (不能小于6)</div>
                            <?php } else { ?>
                                <div class="item-body-desc"> Min length (Cannot be less than 6)</div>
                            <?php } ?>
                            <div class="item-body-tail">
                                <div class="item-body-value" id="passwordMinLength"> <?php echo $passwordMinLength; ?></div>
                                <div class="item-body-value">
                                    <img class="more-img" src="../../public/img/manage/more.png"/>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="division-line"></div>
            </div>
            <div class="list-item-center">
                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display passwordMaxLength" onclick="showPwdMaxLength()">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc">最大长度 (不能大于32)</div>
                            <?php } else { ?>
                                <div class="item-body-desc"> Max length (Cannot be greater than 32)</div>
                            <?php } ?>
                            <div class="item-body-tail">
                                <div class="item-body-value" id="passwordMaxLength"> <?php echo $passwordMaxLength; ?></div>
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

    <div class="layout-all-row" style="margin-top: 10px">
        <div style="width: 100%;">
            <div class="list-item-center">
                <div class="item-row">
                    <div class="item-body">
                        <div class="item-body-display passwordMinLength">
                            <?php if ($lang == "1") { ?>
                                <div class="item-body-desc tip">必须包含字符</div>
                            <?php } else { ?>
                                <div class="item-body-desc tip">Must contain characters</div>
                            <?php } ?>

                        </div>

                    </div>
                </div>
                <div class="division-line"></div>
            </div>


            <div class="list-item-center" onclick="updatePwdType('letter')">
                <div class="item-row" >
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-tail">
                                <?php if(strpos($passwordContainCharacters, "letter")!== false ) { ?>
                                    <img class="check_img pwd_letter_img"  pwd_type="letter" src="../../public/img/manage/selected.png"  default="1">
                                <?php } else { ?>
                                    <img class="check_img pwd_letter_img"  pwd_type="letter" src="../../public/img/manage/unselect.png" default="0" >
                                <?php } ?>
                            </div>
                            <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                    字母
                                <?php } else { ?>
                                    Letter
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>
            </div>
            <div class="list-item-center" onclick="updatePwdType('number')">
                <div class="item-row" >
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-tail">
                                <?php if(strpos($passwordContainCharacters, "number")!== false ) { ?>
                                    <img class="check_img pwd_number_img"  pwd_type="number" src="../../public/img/manage/selected.png"  default="1">
                                <?php } else { ?>
                                    <img class="check_img pwd_number_img"  pwd_type="number" src="../../public/img/manage/unselect.png"  default="0">
                                <?php } ?>
                            </div>
                            <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                    数字
                                <?php } else { ?>
                                   Number
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>
            </div>
            <div class="list-item-center" onclick="updatePwdType('special_characters')">
                <div class="item-row" >
                    <div class="item-body">
                        <div class="item-body-display">
                            <div class="item-body-tail">
                                <?php if(strpos($passwordContainCharacters, "special_characters")!== false ) { ?>
                                    <img class="check_img pwd_special_characters_img"  pwd_type="special_characters" src="../../public/img/manage/selected.png" default="1">
                                <?php } else { ?>
                                    <img class="check_img pwd_special_characters_img"  pwd_type="special_characters" src="../../public/img/manage/unselect.png" default="0">
                                <?php } ?>
                            </div>
                            <div class="item-body-desc"><?php if ($lang == "1") { ?>
                                    特殊符号
                                <?php } else { ?>
                                    Special characters
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="division-line"></div>
            </div>
        </div>
    </div>
    <span class="tip">
        <?php if ($lang == "1") { ?>
            备注：为空代表不限制
        <?php } else { ?>
            Remarks: Empty is not limited
        <?php } ?>
    </span>


</div>

<div class="wrapper-mask" id="wrapper-mask" style="visibility: hidden;"></div>

<div class="popup-template" style="display:none;">

    <div class="config-hidden" id="popup-group">

        <div class="flex-container">
            <div class="header_tip_font popup-group-title"></div>
        </div>

        <div class="" style="text-align: center">
            <input type="text" class="popup-group-input" placeholder="please input">
        </div>

        <div class="line"></div>

        <div class="" style="text-align:center;">
            <?php if ($lang == "1") { ?>
                <button id="updatePopupButton" type="button" class="create_button" key-value=""
                        onclick="updateDataValue();">确认
                </button>
            <?php } else { ?>
                <button id="updatePopupButton" type="button" class="create_button" key-value=""
                        onclick="updateDataValue();">Confirm
                </button>
            <?php } ?>
        </div>

    </div>

</div>

<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript">


    function updatePwdType(pwdType) {
        var unselectSrc = "../../public/img/manage/unselect.png";
        var selectSrc = "../../public/img/manage/selected.png";

        switch (pwdType) {
            case "letter":
                var defaultNum = $(".pwd_letter_img").attr("default");

                if(Number(defaultNum) == 0) {
                    $(".pwd_letter_img").attr("src", selectSrc);
                    $(".pwd_letter_img").attr("default", 1);
                } else {
                    $(".pwd_letter_img").attr("src", unselectSrc);
                    $(".pwd_letter_img").attr("default", 0);
                }
                break;
            case "number":
                var defaultNum = $(".pwd_number_img").attr("default");

                if(Number(defaultNum) == 0) {
                    $(".pwd_number_img").attr("src", selectSrc);
                    $(".pwd_number_img").attr("default", 1);
                } else {
                    $(".pwd_number_img").attr("src", unselectSrc);
                    $(".pwd_number_img").attr("default", 0);
                }
                break;
            case "special_characters":
                var defaultNum = $(".pwd_special_characters_img").attr("default");

                if(Number(defaultNum) == 0) {
                    $(".pwd_special_characters_img").attr("src", selectSrc);
                    $(".pwd_special_characters_img").attr("default", 1);
                } else {
                    $(".pwd_special_characters_img").attr("src", unselectSrc);
                    $(".pwd_special_characters_img").attr("default", 0);
                }
                break;
        }
        var containCharaters = $(".check_img[default=1]");
        var length = containCharaters.length;
        var pwdContainCharater = "";
        for(var i=0; i<length;i++) {
            var containCharater = containCharaters[i];
            pwdContainCharater += $(containCharater).attr("pwd_type") +  "," ;
        }
        var data = {
            "key" : "passwordContainCharacters",
            "value" : pwdContainCharater
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

    function showPwdMinLength(){
        var title = $(".passwordMinLength").find(".item-body-desc").html();
        var inputBody = $("#passwordMinLength").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", "passwordMinLength");
    }

    function showPwdMaxLength(){
        var title = $(".passwordMaxLength").find(".item-body-desc").html();
        var inputBody = $("#passwordMaxLength").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", "passwordMaxLength");
    }

    function showLoginNameMaxLength(){
        var title = $(".loginNameMaxLength").find(".item-body-desc").html();
        var inputBody = $("#loginNameMaxLength").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", "loginNameMaxLength");
    }

    function showLoginNameMinLength(){
        var title = $(".loginNameMinLength").find(".item-body-desc").html();
        var inputBody = $("#loginNameMinLength").html();

        showWindow($(".config-hidden"));

        $(".popup-group-title").html(title);
        $(".popup-group-input").val(inputBody);
        $("#updatePopupButton").attr("key-value", "loginNameMinLength");
    }

    function showWindow(jqElement) {
        jqElement.css("visibility", "visible");
        $(".wrapper-mask").css("visibility", "visible").append(jqElement);
    }


    function removeWindow(jqElement) {
        jqElement.remove();
        $(".popup-template").append(jqElement);
        $(".wrapper-mask").css("visibility", "hidden");
    }


    $(".wrapper-mask").mouseup(function (e) {
        var targetId = e.target.id;
        var targetClassName = e.target.className;

        if (targetId == "wrapper-mask") {
            var wrapperMask = document.getElementById("wrapper-mask");
            var length = wrapperMask.children.length;
            var i;
            for (i = 0; i < length; i++) {
                var node = wrapperMask.children[i];
                node.remove();
                // addTemplate(node);
                $(".popup-template").append(node);
                $(".popup-template").hide();
            }
            $(".popup-group-input").val("");
            $("#updatePopupButton").attr("data", "");
            wrapperMask.style.visibility = "hidden";
        }
    });

    function updateDataValue() {

        var key = $("#updatePopupButton").attr("key-value");

        var url = "index.php?action=manage.security.update";

        var value = $.trim($(".popup-group-input").val());

        var data = {
            'key': key,
            'value': value,
        };

        zalyjsCommonAjaxPostJson(url, data, updateResponse);

        // close
        removeWindow($(".config-hidden"));
    }

</script>

</body>
</html>

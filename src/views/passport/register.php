<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title;?></title>
    <!-- Latest compiled and minified CSS -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="./public/css/login.css?_version=<?php echo $versionCode?>">
    <script type="text/javascript" src="./public/js/jquery.min.js"></script>
    <script src="./public/js/jquery.i18n.properties.min.js"></script>
    <script src="./public/sdk/zalyjsNative.js?_version=<?php echo $versionCode?>"></script>
    <script src="./public/js/template-web.js"></script>

</head>

<body>
<input type="hidden" value="<?php echo $loginWelcomeText;?>" class="loginWelcomeText">
<input type="hidden" value="<?php echo $loginBackgroundColor;?>" class="loginBackgroundColor">
<input type="hidden" value="<?php echo $loginBackgroundImage;?>" class="loginBackgroundImage">
<input type="hidden" value="<?php echo $loginBackgroundImageDisplay;?>" class="loginBackgroundImageDisplay">

<input type="hidden" value="<?php echo $pwdContainCharacters;?>" class="pwdContainCharacters">
<input type="hidden" value="<?php echo $loginNameMaxLength;?>" class="loginNameMaxLength">
<input type="hidden" value="<?php echo $loginNameMinLength;?>" class="loginNameMinLength">
<input type="hidden" value="<?php echo $pwdMaxLength;?>" class="pwdMaxLength">
<input type="hidden" value="<?php echo $pwdMinLength;?>" class="pwdMinLength">

<input type="hidden" value="<?php echo $nicknameRequired;?>" class="nicknameRequired">



<div class="site-warning"></div>

<div style="position: relative; width:100%;height:100%;" class="site_login_div">
    <?php if($loginBackgroundColor) { ?>
        <div style="position: absolute;background-color: <?php echo $loginBackgroundColor; ?>;opacity:0.4;filter:alpha(opacity=40);top:0rem; height: 100%;width: 100%;">
        </div>
    <?php } else { ?>
        <div style="position: absolute;background: RGBA(0, 0, 0, 0.6);top:0rem; height: 100%;width: 100%;">
        </div>
    <?php } ?>

    <div class="zaly_container" style="display: none"></div>

    <div style="" class="login_div_container">
        <div class="login_container">
            <div class="container">
                <div  class="login_custom_made">
                    <div class="company_custom_made">
                        <div>
                            <?php if($siteLogo) { ?>
                                <img src="<?php echo $siteLogo; ?>" class="company_logo">
                            <?php } else { ?>
                                <img src="../../public/img/login/logo.png" class="company_logo">
                            <?php } ?>
                        </div>
                        <div>
                            <?php if($siteName) { ?>
                                <span class="company_name"><?php echo $siteName; ?></span>
                            <?php } else { ?>
                                <span class="company_name">Duckchat</span>
                            <?php } ?>
                        </div>
                        <div class="company_slogan">
                            <?php if($loginWelcomeText) { ?>
                                <?php echo $loginWelcomeText; ?>
                            <?php } else { ?>
                                这是一个使用DuckChat系统搭建的聊天站点，此处的描述内容可以在管理后台进行修改配置。<br/>官网：<a target="_blank" href="https://duckchat.akaxin.com">https://duckchat.akaxin.com</a>
                            <?php } ?>
                        </div>
                        <div class="site_version">
                            V<?php echo $siteVersionName; ?>
                        </div>
                    </div>

                </div>
                <div  class="login_div">
                    <div class="zaly_register zaly_site_register zaly_site_register-name" >
                        <div class="login_input_div" >
                            <div class="d-flex flex-row justify-content-center login-header"style="text-align: center;margin-top: 2rem;margin-bottom: 1rem;">
                                <span class="login_phone_tip_font" data-local-value="registerTip" >注册</span>
                            </div>

                            <div class="d-flex flex-row justify-content-left login_name_div margin-top2 login_name_div_mobile" >
                                <img src="./public/img/login/loginName.png" class="img"/>

                                <?php if ($loginNameAlias):?>
                                    <input type="text" id="register_input_loginName"  datatype="s"  class="input_login_site  register_input_loginName"   autocapitalize="off"   placeholder=" <?php echo $loginNameAlias; ?>" >
                                <?php else:?>
                                    <input type="text" id="register_input_loginName"  datatype="s"  class="input_login_site  register_input_loginName" data-local-placeholder="registerLoginNamePlaceholder"  autocapitalize="off"   placeholder="用户名" >
                                <?php endif;?>
                                <span class="img-required register_input_loginName_required" >*</span>
                                <img src="./public/img/msg/msg_failed.png" class="img-failed register_input_loginName_failed">
                            </div>
                            <div class="register_line"></div>
                            <div style="font-size:1.31rem;font-family:PingFangSC-Regular;font-weight:400;color:rgba(153,153,153,1);" ><?php echo $loginNameTip;?></div>

                            <?php if($nicknameRequired) :?>
                                <div class="login_name_div" style="margin-top: 1rem;">
                                    <img src="./public/img/login/nickname.png" class="img"/>
                                    <input type="text" class="input_login_site register_input_nickname" autocapitalize="off"   id="register_input_nickname" data-local-placeholder="nicknamePlaceholder"  placeholder="输入昵称"  >
                                    <span class="img-required register_input_nickname_required" >*</span>
                                    <img src="./public/img/msg/msg_failed.png" class="img-failed register_input_nickname_failed">
                                </div>
                                <div class="register_line"></div>
                            <?php endif; ?>



                            <div class="login_name_div" style="margin-top: 1rem;">
                                <img src="./public/img/login/pwd.png" class="img"/>
                                <input type="password" class="input_login_site register_input_pwd" autocapitalize="off"   id="register_input_pwd" data-local-placeholder="enterPasswordPlaceholder"  placeholder="输入密码"  >
                                <div class="pwd_div" onclick="changeImgByClickPwd()"><img src="../../public/img/login/hide_pwd.png" class="pwd" img_type="hide"/></div>
                                <span class="img-required register_input_pwd_required" >*</span>
                                <img src="./public/img/msg/msg_failed.png" class="img-failed register_input_pwd_failed">
                            </div>
                            <div class="register_line"></div>
                            <div style="font-size:1.31rem;font-family:PingFangSC-Regular;font-weight:400;color:rgba(153,153,153,1);" ><?php echo $pwdTip;?></div>



                            <div class="login_name_div" style="margin-top: 1rem;">
                                <img src="./public/img/login/pwd.png" class="img"/>
                                <input type="password" class="input_login_site register_input_repwd" autocapitalize="off"   id="register_input_repwd" data-local-placeholder="enterRepasswordPlaceholder"  placeholder="再次输入密码"  >
                                <div class="repwd_div" onclick="changeImgByClickRepwd()"><img src="../../public/img/login/hide_pwd.png" class="repwd" img_type="hide"/></div>
                                <span class="img-required register_input_repwd_required" >*</span>
                                <img src="./public/img/msg/msg_failed.png" class="img-failed register_input_repwd_failed">
                            </div>
                            <div class="register_line" ></div>

                            <?php if(count($registerCustoms)) : ?>
                                <?php foreach ($registerCustoms as $registerCustom): ?>
                                    <div class="login_name_div" style="margin-top: 1rem;">
                                            <img src="<?php echo  $registerCustom['keyIcon'];?>" class="img" onerror="src='./public/img/login/custom_default.png'"/>
                                            <input type="text" class="input_login_site register_input_<?php echo $registerCustom['customKey'];?> register_custom" autocapitalize="off" isRequired="<?php echo $registerCustom['isRequired'];?>" customKey = "<?php echo $registerCustom['customKey'];?>" id="register_input_<?php echo $registerCustom['customKey'];?>" customName="<?php echo $registerCustom['keyName'];?>" placeholder="<?php echo $registerCustom['keyName'];?>" >
                                        <?php if($registerCustom['isRequired']):?>
                                            <span class="img-required register_input_<?php echo $registerCustom['customKey'];?>_required" >*</span>
                                        <?php endif;?>

                                        <img src="./public/img/msg/msg_failed.png" class="img-failed register_input_<?php echo $registerCustom['customKey'];?>_failed">
                                    </div>
                                    <div class="register_line"></div>
                                    <div style="font-size:1.31rem;font-family:PingFangSC-Regular;font-weight:400;color:rgba(153,153,153,1);"data-local-value="findPasswordTip" ><?php echo $registerCustom['keyDesc'];?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>


                            <div class="d-flex flex-row justify-content-center ">
                                <?php if ($enableInvitationCode):?>
                                    <button type="button" class="btn register_code_button"><span class="span_btn_tip" data-local-value="registerBtnCodeTip">下一步</span></button>
                                <?php else:?>
                                    <button type="button" class="btn register_button"><span class="span_btn_tip" data-local-value="registerBtnTip">注册并登录</span></button>
                                <?php endif;?>
                            </div>


                            <div class="d-flex flex-row register_span_div " >
                                <span style="color:rgba(153,153,153,1);" data-local-value="hasAccountTip">已有账号？</span>
                                <span onclick="registerForLogin()" data-local-value="loginBtnTip">登录</span>
                            </div>
                        </div>
                    </div>


                    <div class="zaly_login zaly_site_register zaly_site_register-invitecode" style="display: none;">
                        <div class="back">
                            <img src="./public/img/back.png" style="margin-left: 2rem; width: 3rem;height:3rem; margin-top: 2rem;cursor: pointer;" onclick="returnRegisterDiv(); return false;"/>
                        </div>
                        <div class="login_input_div" >
                            <div class="d-flex flex-row justify-content-center login-header"style="text-align: center;margin-top: 8rem;margin-bottom: 1rem;">
                                <span class="login_phone_tip_font" data-local-value="registerInvitationCodeTip" >输入邀请码</span>
                            </div>

                            <div class="code_div login_name_div_mobile" style="margin-top: 8rem;">
                                <input type="text" class="input_login_site register_input_code" style="margin-left: 0rem;" data-local-placeholder="enterCodePlaceholder" autocapitalize="off"   placeholder="输入邀请码"  >
                                <div class="line" ></div>
                            </div>

                            <div class="d-flex flex-row justify-content-center " >
                                <button type="button" class="btn register_button"  style="margin-top: 7rem;"><span class="span_btn_tip" data-local-value="registerBtnTip">注册并登录</span></button>
                            </div>
                        </div>
                    </div>

                    <div class="zaly_login zaly_site_register zaly_site_update-invitecode" style="display: none;">
                        <div class="back">
                            <img src="./public/img/back.png" style="margin-left: 2rem; width: 3rem;height:3rem; margin-top: 2rem;cursor: pointer;" onclick="returnLoginDiv(); return false;"/>
                        </div>
                        <div class="login_input_div" >
                            <div class="d-flex flex-row justify-content-center login-header "style="text-align: center;margin-top: 8rem;margin-bottom: 1rem;">
                                <span class="login_phone_tip_font" data-local-value="registerInvitationCodeTip" >输入邀请码</span>
                            </div>

                            <div class="code_div login_name_div_mobile" style="margin-top: 8rem;">
                                <input type="text" class="input_login_site update_input_code" autocapitalize="off" style="margin-left: 0rem;" data-local-placeholder="enterCodePlaceholder" onkeydown="registerAndLoginByKeyDown(event)" placeholder="输入邀请码"  >
                                <div class="line" ></div>
                            </div>

                            <div class="d-flex flex-row justify-content-center " >
                                <button type="button" class="btn update_code_btn"  style="margin-top: 7rem;"><span class="span_btn_tip" data-local-value="registerBtnTip">注册并登录</span></button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div id="powered_by_duckchat" class="powered_by_duckchat">
                Powered by &nbsp; <a class="duckchat_website" target="_blank" href="https://duckchat.akaxin.com" style="cursor: pointer;"> Duckchat</a>
            </div>
        </div>
    </div>
</div>


<input type="hidden" value="<?php echo $jumpRoomId;?>" class="jumpRoomId">
<input type="hidden" value="<?php echo $jumpRoomType;?>" class="jumpRoomType">
<input type="hidden" value="<?php echo $isDuckchat; ?>" class="isDuckchat">
<input type="hidden" value="<?php echo $enableInvitationCode; ?>" class="enableInvitationCode">
<input type="hidden" value="<?php echo $enableRealName; ?>" class="enableRealName">

<?php include(dirname(__DIR__) . '/passport/template_login.php'); ?>
<script src="./public/js/zalyjsHelper.js?_version=<?php echo $versionCode?>"></script>

<script src="./public/js/im/zalyKey.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyAction.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyClient.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/im/zalyBaseWs.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/login/base.js?_version=<?php echo $versionCode?>"></script>
<script src="./public/js/login/register.js?_version=<?php echo $versionCode?>"></script>


</body>
</html>

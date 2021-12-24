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
                            <div class="zaly_login zaly_login_by_pwd" >
                                <div class="login_input_div login_for_size_div" >
                                    <div style="position: relative; height: 100%;">
                                        <div>
                                            <div class="mobile_logo_div">
                                                <?php if ($siteLogo):?>
                                                    <img class="mobile_logo" src="<?php echo $siteLogo;?>">
                                                <?php else:?>
                                                    <img class="mobile_logo" src="./public/img/login/mobile_logo.png">
                                                <?php endif;?>
                                            </div>
                                            <div class="d-flex flex-row justify-content-center login-header" style="text-align: center;">
                                                <span class="login_phone_tip_font" data-local-value="loginTip">登录</span>
                                            </div>

                                            <div class=" d-flex flex-row justify-content-left login_name_div" >
                                                <img src="./public/img/login/loginName.png" class="img"/>
                                                <?php if ($loginNameAlias):?>
                                                    <input type="text" class="input_login_site  login_input_loginName" datatype="s" autocapitalize="off"  placeholder=" <?php echo  $loginNameAlias;?>" >
                                                <?php else:?>
                                                    <input type="text" class="input_login_site  login_input_loginName" datatype="s" autocapitalize="off"  data-local-placeholder="loginNamePlaceholder" placeholder="输入用户名" >
                                                <?php endif;?>

                                                <div class="clearLoginName" onclick="clearLoginName()"><img src="./public/img/msg/btn-x.png" class="clearLoginName clear_img" /></div>
                                                <img src="./public/img/msg/msg_failed.png" class="img-failed login_input_loginName_failed">
                                            </div>
                                            <div class="line"></div>

                                            <div class="login_name_div margin-top2">
                                                <img src="./public/img/login/pwd.png" class="img"/>
                                                <input type="password" class="input_login_site phone_num  login_input_pwd" autocapitalize="off"  data-local-placeholder="enterPasswordPlaceholder"  onkeydown="loginPassportByKeyPress(event)"  placeholder="输入密码, 长度5到20个字符(无中文)" >
                                                <div class="pwd_div" onclick="changeImgByClickPwd()"><img src="./public/img/login/hide_pwd.png" class="pwd" img_type="hide" /></div>
                                                <img src="./public/img/msg/msg_failed.png" class="img-failed login_input_pwd_failed">
                                            </div>
                                            <div class="line"></div>

                                            <div class="d-flex flex-row justify-content-center ">
                                                <button type="button" class="btn login_button" ><span class="span_btn_tip" data-local-value="loginBtnTip">登 录</span></button>
                                            </div>

                                            <div class="d-flex flex-row register_span_div" >
                                                <span onclick="registerForPassportPassword()" style="color: RGBA(0, 0, 0, 0.2);" data-local-value="noAccountTip">还没有账户？</span> <span onclick="registerForPassportPassword()" data-local-value="registerContentTip">去注册</span>
                                            </div>

                                        </div>

                                        <!--   third party landing      -->
                                        <?php if(count($thirdPartyLoginOptions)) : ?>
                                        <div class="login-others">
                                            <div class="landing-third-title">使用以下账号直接登录</div>
                                            <div class="landing-third-content">
                                                <?php foreach($thirdPartyLoginOptions as $thirdLogin) : ?>
                                                <img class="third_login_logo"  landingUrl="<?php echo $thirdLogin['landingUrl'];?>" src="<?php echo $thirdLogin['logo'];?>" name="<?php echo $thirdLogin['name'];?>" onerror="src='./public/img/plugin/default.png'">
                                                <?php endforeach; ?>

                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <!--  for   -->
                                        <div class="mobile_slogn_div" style="position: absolute;bottom:3rem;">
                                            <?php if($loginWelcomeText) : ?>
                                                <?php echo $loginWelcomeText; ?>
                                            <?php else:?>
                                            这是一个使用DuckChat系统搭建的聊天站点，此处的描述内容可以在管理后台进行修改配置。<br/>官网：<a target="_blank" href="https://duckchat.akaxin.com">https://duckchat.akaxin.com</a>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="zaly_login zaly_site_register zaly_site_register-name" style="display: none;">

                            </div>

                            <div class="zaly_login zaly_site_register zaly_site_register-invitecode" style="display: none;">
                                <div class="back">
                                    <img src="../../public/img/back.png" style="margin-left: 2rem; width: 3rem;height:3rem; margin-top: 2rem;cursor: pointer;" onclick="returnRegisterDiv(); return false;"/>
                                </div>
                                <div class="login_input_div" >
                                    <div class="d-flex flex-row justify-content-center login-header" style="text-align: center;margin-top: 8rem;margin-bottom: 1rem;">
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
                                    <img src="../../public/img/back.png" style="margin-left: 2rem; width: 3rem;height:3rem; margin-top: 2rem;cursor: pointer;" onclick="returnLoginDiv(); return false;"/>
                                </div>
                                <div class="login_input_div" >
                                    <div class="d-flex flex-row justify-content-center login-header" style="text-align: center;margin-top: 8rem;margin-bottom: 1rem;">
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
<script src="./public/js/login/login.js?_version=<?php echo $versionCode?>"></script>


</body>
</html>



$(document).on("click", ".update_code_btn", function () {
    invitationCode = $(".update_input_code").val();
    showLoading($(".site_login_div"));
    cancelLoadingBySelf();
    apiPassportPasswordLogin(updatePassportPasswordInvitationCode);
});



///更新邀请码，并且登录site
function failedCallBack(result) {
    try{
        hideLoading();
        if(result.hasOwnProperty("errorInfo")) {
            alert(result.errorInfo);
        }else {
            if(result != undefined && result !='') {
                alert(result);
            }
        }
    }catch (error){
    }
}

function handlePassportPasswordUpdateInvationCode(results)
{
    isRegister = true;
    preSessionId = results.preSessionId;
    cancelLoadingBySelf();
    zalyjsLoginSuccess(loginName, preSessionId, isRegister, "", failedCallBack);
}


$(document).on("click", ".reset_pwd_button", function () {
    var action = "api.passport.passwordResetPassword";
    var isFocus = false;
    var token = $(".forget_input_code").val();
    var repassword = $(".forget_input_repwd").val();
    var password = $(".forget_input_pwd").val();
    var loginName = $(".forget_input_loginName").val();

    if(loginName == "" || loginName == undefined || loginName.length<0) {
        $(".forget_input_loginName").focus();
        $(".forget_input_loginName_failed")[0].style.display = "block";
        isFocus = true;
    }
    if(token ==  "" || token.length<1) {
        $(".forget_input_code_failed")[0].style.display = "block";
        if(isFocus == false) {
            $(".forget_input_code").focus();
            $(".forget_input_loginName_failed")[0].style.display = "none";
            isFocus = true;
        }
    }

    if(password ==  "" || password.length<1) {
        $(".forget_input_pwd_failed")[0].style.display = "block";
        if(isFocus == false) {
            $(".forget_input_pwd").focus();
            $(".forget_input_code_failed")[0].style.display = "none";
            isFocus = true;
        }
    }

    if(repassword ==  "" || repassword.length<1) {
        $(".forget_input_repwd_failed")[0].style.display = "block";
        if(isFocus == false) {
            $(".forget_input_repwd").focus();
            $(".forget_input_pwd_failed")[0].style.display = "none";
            isFocus = true;
        }
    }

    if(isFocus == true) {
        return;
    }

    if(repassword != password) {
        alert($.i18n.map["passwordIsNotSameJsTip"]);
        return;
    }

    var reqData = {
        "loginName" : loginName,
        "token" :token,
        "password" :password
    };

    handleClientSendRequest(action, reqData, handleResetPwd);
});

function handleResetPwd()
{
    $(".zaly_login_by_pwd")[0].style.display = "block";
    $(".zaly_site_register-repwd")[0].style.display = "none";
}

function clearLoginName()
{
    $(".login_input_loginName").val("");
    $(".clear_img")[0].style.display = "none";
    $(".clearLoginName")[0].style.display = "none";
}




//--------------------------------------------------------------login---------------------------------------------------

function registerForPassportPassword() {
    window.location.href = "./index.php?action=page.passport.register&lang="+getLanguage();
}

$(document).on("click", ".third_login_logo", function () {
    var name = $(this).attr("name");
    var landingUrl = $(this).attr("landingUrl");
    var siteAddressUrl = encodeURIComponent(location.href);
    if(landingUrl.indexOf("?") != -1) {
        landingUrl +="&duckchat_third_login_name="+name+"&from=duckchat&redirect_url="+siteAddressUrl;
    } else {
        landingUrl +="?duckchat_third_login_name="+name+"&from=duckchat&redirect_url="+siteAddressUrl;
    }
    var html = template("tpl_third_login", {
        landingUrl:landingUrl
    });
    $(".login_div_container").html(html);
});

$(document).on("click",".third_login_close",function () {
    window.location.reload();
});

$(document).on("input porpertychange", ".login_input_loginName", function () {
    var length = $(".login_input_loginName").val().length;
    if(Number(length)>0) {
        $(".clear_img")[0].style.display = "block";
        $(".clearLoginName")[0].style.display = "block";
    } else {
        $(".clear_img")[0].style.display = "none";
        $(".clearLoginName")[0].style.display = "none";
    }
});

function loginPassportByKeyPress(event) {
    if(checkIsEnterBack(event) == false) {
        return false;
    }
    loginPassport();
}


function loginPassport()
{
    loginName = $(".login_input_loginName").val();
    loginPassword  = $(".login_input_pwd").val();
    var isFocus = false;
    if(loginName == "" || loginName == undefined || loginName.length<0) {
        $(".login_input_loginName").focus();
        $(".login_input_loginName_failed")[0].style.display = "block";
        isFocus = true;
    }

    if(loginPassword == "" || loginPassword == undefined || loginPassword.length<0) {
        $(".login_input_pwd_failed")[0].style.display = "block";
        if (isFocus == false) {
            $(".login_input_pwd").focus();
            $(".login_input_loginName_failed")[0].style.display = "none";
            isFocus = true;
        }
    }

    if(isFocus == true ) {
        return false;
    }
    $(".login_input_pwd_failed")[0].style.display = "none";


    showLoading($(".site_login_div"));
    cancelLoadingBySelf();
    apiPassportPasswordLogin(handleApiPassportPasswordLogin);
}


function apiPassportPasswordLogin(callback)
{
    var action = "api.passport.passwordLogin";
    var name =  loginName ;
    var password =  loginPassword;

    var reqData = {
        loginName:name,
        password:password,
    };
    handleClientSendRequest(action, reqData, callback);
}


function loginFailed(result)
{
    hideLoading();
    if(result.hasOwnProperty('errorInfo')) {
        alert(result.errorInfo);
    } else {
        if(result != undefined && result !='') {
            alert(result);
        }
    }
    if(isRegister == true && enableInvitationCode == 1) {
        $(".register_button").attr("is_type", updateInvitationCodeType);
    }
}




function displayInvitationCode()
{
    hideLoading();
    if(enableInvitationCode != "1") {
        if(isRegister == true) {
            return false;
        }
        isRegister = true;
        zalyjsLoginSuccess(loginName, preSessionId, isRegister, "", loginFailed);
    } else {
        $(".zaly_login_by_pwd")[0].style.display = "none";
        $(".zaly_site_update-invitecode")[0].style.display = "block";
    }
}

function loginFailNeedRegister()
{
    displayInvitationCode();
}


function handleApiPassportPasswordLogin(results)
{
    preSessionId = results.preSessionId;
    cancelLoadingBySelf();
    zalyjsLoginSuccess(loginName, preSessionId, isRegister, "", loginFailNeedRegister);
}

$(document).on("click", ".login_button", function () {
    loginPassport();
});


$(".input_login_site").bind('input porpertychange',function(){
    if($(this).val().length>0) {
        $(this).addClass("black");
        $(this).removeClass("outline");
    }
});

function  returnLoginDiv() {
    $(".zaly_site_register-invitecode")[0].style.display = "none";
    $(".zaly_site_update-invitecode")[0].style.display="none";
    $(".zaly_login_by_pwd")[0].style.display = "block";
}






jQuery.i18n.properties({
    name: "lang",
    path: '../../public/js/config/',
    mode: 'map',
    language: languageName,
    callback: function () {
        try {
            //初始化页面元素
            $('[data-local]').each(function () {
                var changeData = $(this).attr("data-local");
                var changeDatas = changeData.split(":");
                var changeDataName = changeDatas[0];
                var changeDataValue = changeDatas[1];
                $(this).attr(changeDataName, $.i18n.map[changeDataValue]);
            });
            $('[data-local-value]').each(function () {
                var changeHtmlValue = $(this).attr("data-local-value");
                $(this).html($.i18n.map[changeHtmlValue]);
            });
            $('[data-local-placeholder]').each(function () {
                var placeholderValue = $(this).attr("data-local-placeholder");
                $(this).attr("placeholder", $.i18n.map[placeholderValue]);
            });
        }
        catch(ex){
            console.log(ex.message);
        }
    }
});


siteConfig = {};
enableInvitationCode=0;
enableRealName=0;
invitationCode='';
allowShareRealname=0;
preSessionId="";
secondNum  = 120;
isSending  = false;
updateInvitationCodeType = "update_invitation_code";
registerLoginName=undefined
registerPassword=undefined

var protocol = window.location.protocol;
var host = window.location.host;
var pathname = window.location.pathname;
var originDomain = protocol+"//"+host+pathname;
var isRegister=false;
var siteAddress = $(".siteAddressPath").val();

var siteName = $(".siteName").val();

//------------------------------------------public---------------------------------------

$(":input").attr("autocapitalize", "off");

function isWeixinBrowser(){
    return /micromessenger/.test(navigator.userAgent.toLowerCase())
}

function isPhone(){
    if((/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) || isWeixinBrowser()) {
        return true;
    }
    return false;
}

function isAvailableBrowser(){
    var userAgent = navigator.userAgent;
    var isFirefox = userAgent.indexOf("Firefox") != -1;
    var isChrome = userAgent.indexOf("Chrome") && window.chrome;
    var isSafari = userAgent.indexOf("Safari") != -1 && userAgent.indexOf("Version") != -1;
    if(isFirefox || isChrome || isSafari) {
        return true;
    }
    return false;
}
var isDuckchatFlag = $(".isDuckchat").val();
var isPhoneFlag = isPhone();

if(isPhoneFlag && isDuckchatFlag == 0) {
    $(".site-warning")[0].style.display='flex';
    var tip = "暂不支持手机浏览器，请使用手机客户端或者PC访问站点！";
    if(languageName == "en") {
        tip = "Mobile browser is not supported at this time, please use the mobile client or PC to access the site!";
    }
    $(".site-warning").html(tip);
}

if(isPhoneFlag == false) {
    var isAvailabelBrowserFlag = isAvailableBrowser();
    if(!isAvailabelBrowserFlag) {
        $(".site-warning")[0].style.display='flex';
        var tip = "暂时不支持此浏览器, 请使用火狐,chrome,safari访问站点";
        if(languageName == "en") {
            tip = "This browser is not supported at this time. Please use the mobile client or Firefox, Chrome, Safari browser to visit the site!";
        }
        $(".site-warning").html(tip);
    }
}

enableInvitationCode = $(".enableInvitationCode").val();
enableRealName=$(".enableRealName").val();



getOsType();

var loginWelcomeText = $(".loginWelcomeText").val();
var loginBackgroundColor = $(".loginBackgroundColor").val();
var loginBackgroundImage = $(".loginBackgroundImage").val();
var loginBackgroundImageDisplay = $(".loginBackgroundImageDisplay").val();
var passwordResetRequired = $(".passwordResetRequired").val();
var x= $(".jumpRoomId").val();
var page= $(".jumpRoomType").val();
var refererUrlKey = "documentReferer";

if(loginWelcomeText) {
    var text = template("tpl-string", {
        string:loginWelcomeText
    })
    var text = handleLinkContentText(text);
    $(".company_slogan").html(text);
}

var redirectUrl = location.href;
if(page) {
    if(redirectUrl.indexOf("?")) {
        redirectUrl +="&page="+page+"&x="+x;
    }else{
        redirectUrl +="?page="+page+"&x="+x;
    }
}

localStorage.setItem(refererUrlKey, redirectUrl);


function isMobile() {
    if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
        return true;
    }
    return false;
}
if(!isMobile()) {
    if(loginBackgroundImage) {
        loginBackgroundImageDisplay = Number(loginBackgroundImageDisplay);
        switch (loginBackgroundImageDisplay) {
            case 1:
                $(".zaly_container")[0].style.background = "url('"+loginBackgroundImage+"') no-repeat";
                $(".zaly_container")[0].style.backgroundSize = "100% 100%";
                break;
            case 2:
                $(".zaly_container")[0].style.background = "url('"+loginBackgroundImage+"') repeat";
                break;
            default:

                $(".zaly_container")[0].style.background = "url('"+loginBackgroundImage+"')";
                $(".zaly_container")[0].style.backgroundSize = "cover";
        }
    } else {
        switch (loginBackgroundImageDisplay) {
            case 1:
                $(".zaly_container")[0].style.background = "url('../../public/img/login/login_bg.jpg') no-repeat";
                $(".zaly_container")[0].style.backgroundSize = "100% 100%";
                break;
            case 2:
                $(".zaly_container")[0].style.background = "url('../../public/img/login/login_bg.jpg') repeat";
                break;
            default:
                $(".zaly_container")[0].style.background = "url('../../public/img/login/login_bg.jpg')";
                $(".zaly_container")[0].style.backgroundSize = "cover";
        }
    }
    $(".zaly_container")[0].style.display="block";

}
//replace \n from html
function trimHtmlContentBr(str)
{
    html = str.replace(/\\n/g,"<br/>");
    return html;
}

function handleLinkContentText(str)
{
    str = trimHtmlContentBr(str);

    var reg=/(blob:)?((http|ftp|https|duckchat|zaly):\/\/)?[\w\-_]+(\:[0-9]+)?(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/g;
    var arr = str.match(reg);
    if(arr == null) {
        return str;
    }

    var length = arr.length;
    for(var i=0; i<length;i++) {
        var urlLink = arr[i];
        if(urlLink.indexOf("blob:") == -1 &&
            ( IsURL (urlLink)
                || urlLink.indexOf("http://") != -1
                || urlLink.indexOf("https://") != -1
                || urlLink.indexOf("ftp://") != -1
                || urlLink.indexOf("zaly://") != -1
                || urlLink.indexOf("duckchat://") != -1
            )
        ) {
            var newUrlLink = urlLink;
            if(urlLink.indexOf("://") == -1) {
                newUrlLink = "http://"+urlLink;
            }
            var urlLinkHtml = "<a href='"+newUrlLink+"'target='_blank'>"+urlLink+"</a>";
            str = str.replace(urlLink, urlLinkHtml);
        }
    }

    return str;
}

function IsURL (url) {
    var urls = url.split("?");
    var urlAndSchemAndPort = urls.shift();
    var urlAndPort = urlAndSchemAndPort.split("://").pop();
    url = urlAndPort.split(":").shift();
    var ipRegex = '^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$';
    var ipReg=new RegExp(ipRegex);
    if(!ipReg.test(url)) {
        var domainSuffix = url.split(".").pop();
        var urlDomain = "com,cn,net,xyz,top,tech,org,gov,edu,ink,red,int,mil,pub,biz,CC,name,TV,mobi,travel,info,tv,pro,coop,aero,me,app,onlone,shop" +
            ",club,store,life,global,live,museum,jobs,cat,tel,bid,pub,foo,site,";
        if(urlDomain.indexOf(domainSuffix) != -1) {
            return true;
        }
        return false;
    }
    return true;
}

try{
    hideLoading();
}catch (error) {

}
$(document).on("mouseover", "#powered_by_duckchat", function () {
    $(".duckchat_website")[0].style.textDecoration = "underline";
});

$(document).on("mouseout", "#powered_by_duckchat", function () {
    $(".duckchat_website")[0].style.textDecoration = "none";
});

function changeImgByClickPwd() {
    var imgType = $(".pwd").attr("img_type");
    if(imgType == "hide") {
        $(".pwd").attr("img_type", "display");
        $(".pwd").attr("src", "../../public/img/login/display_pwd.png");
        $(".login_input_pwd").attr("type", "text");
        $(".register_input_pwd").attr("type", "text");
        $(".forget_input_pwd").attr("type", "text");
    } else {
        $(".pwd").attr("img_type", "hide");
        $(".pwd").attr("src", "../../public/img/login/hide_pwd.png");
        $(".login_input_pwd").attr("type", "password");
        $(".register_input_pwd").attr("type", "password");
        $(".forget_input_pwd").attr("type", "password");
    }
}

function changeImgByClickRepwd() {
    var imgType = $(".repwd").attr("img_type");
    if(imgType == "hide") {
        $(".repwd").attr("img_type", "display");
        $(".repwd").attr("src", "../../public/img/login/display_pwd.png");
        $(".register_input_repwd").attr("type", "text");
        $(".forget_input_repwd").attr("type", "text");
    } else {
        $(".repwd").attr("img_type", "hide");
        $(".repwd").attr("src", "../../public/img/login/hide_pwd.png");
        $(".register_input_repwd").attr("type", "password");
        $(".forget_input_repwd").attr("type", "password");
    }
}

function checkIsEnterBack(event)
{
    var event = event || window.event;
    var isIE = (document.all) ? true : false;
    var key;

    if(isIE) {
        key = event.keyCode;
    } else {
        key = event.which;
    }

    if(key != 13) {
        return false;
    }
    return true;
}


function updatePassportPasswordInvitationCode(results)
{
    if(results != "" && results != undefined && results.hasOwnProperty("preSessionId")) {
        preSessionId = results.preSessionId;
    }
    var action = "api.passport.passwordUpdateInvitationCode";
    var reqData = {
        invitationCode:invitationCode,
        preSessionId:preSessionId,
    }
    handleClientSendRequest(action, reqData, handlePassportPasswordUpdateInvationCode);
}


function registerAndLoginByKeyDown(event)
{
    if(!checkIsEnterBack(event)){
        return false;
    }
    registerAndLogin();
}
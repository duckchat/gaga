
//多语言序号

UserClientLangZH = "1";
UserClientLangEN = "0";

siteAddress = $(".siteAddress").val();

//语言国际化，替换文案
function handleHtmlLanguage(html)
{
    try{
        $(html).find("[data-local-value]").each(function () {
            var changeHtmlValue = $(this).attr("data-local-value");
            var valueHtml = $(this).html();
            var newValueHtml = $.i18n.map[changeHtmlValue];
            if(newValueHtml != undefined && newValueHtml != "" && newValueHtml != false) {
                html = html.replace(valueHtml, newValueHtml);
            }
        });

        $(html).find("[data-local-placeholder]").each(function () {
            var placeholderValue = $(this).attr("data-local-placeholder");
            var placeholder = $(this).attr("placeholder");
            var newPlaceholder = $.i18n.map[placeholderValue];
            if(newPlaceholder != undefined && newPlaceholder != false && newPlaceholder != "") {
                html = html.replace(placeholder, newPlaceholder);
            }
        });
    }catch (error) {

    }
    return html;
}

//获取语言序号
function getLanguage() {
    var nl = navigator.language;
    if ("zh-cn" == nl || "zh-CN" == nl) {
        return UserClientLangZH;
    }
    return UserClientLangEN;
}

//加载语言包
function getLanguageName() {
    var nl = navigator.language;
    if ("zh-cn" == nl || "zh-CN" == nl) {
        return "zh";
    }
    return "en";
}

languageName = getLanguageName();
languageNum = getLanguage();



function isMobile() {
    if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
        return true;
    }
    return false;
}


function showWindow(jqElement) {
    jqElement.css("visibility", "visible");
    $(".wrapper-mask").css("visibility", "visible").append(jqElement);
}

function removeWindow(jqElement) {
    jqElement.remove();
    $(".wrapper-mask").css("visibility", "hidden");
    $("#all-templates").append(jqElement);
}



function addTemplate(jqElement) {
    $("#all-templates").append(jqElement);
}


function showLoading(jeElement) {
    try{
        var html = "<div class=\"helper_loader\" > <div class=\"helper_circular_div\"> <svg class=\"helper_mini_circular\" viewBox=\"25 25 50 50\"> <circle class=\"helper_path\" cx=\"50\" cy=\"50\" r=\"20\" fill=\"none\" stroke-width=\"2\" stroke-miterlimit=\"10\"/> </svg> </div> </div>";
        jeElement.append(html);
        $(".helper_loader")[0].style.display = "flex";
    }catch (error) {
        console.log(error)
    }
}
getLoadingCss();

function showMiniLoading(jeElement) {
    try{
        var html = "<div class=\"helper_loader\" > <div class=\"helper_mini_circular_div\"> <svg class=\"helper_mini_circular\" viewBox=\"25 25 50 50\"> <circle class=\"helper_path\" cx=\"50\" cy=\"50\" r=\"20\" fill=\"none\" stroke-width=\"2\" stroke-miterlimit=\"10\"/> </svg> </div> </div>";
        jeElement.append(html);
        $(".helper_loader")[0].style.display = "flex";
    }catch (error) {
        console.log(error)
    }
}
function hideLoading() {
   try{
       $(".helper_loader").remove();
   }catch (error) {
   }
}

function getLoadingCss()
{
    var cssId = 'loadingCss';  // you could encode the css path itself to generate id..
    if (!document.getElementById(cssId)) {
        var head  = document.getElementsByTagName('head')[0];
        var link  = document.createElement('link');
        link.id   = cssId;
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = './public/css/loading.css';
        link.media = 'all';
        head.appendChild(link);
    }
}



function cancelLoadingBySelf()
{
    setTimeout(function () {
        hideLoading();
    }, 5000);
}

function checkIsEntities(str){
    var entitiesReg = /(&nbsp;|&#160;|&lt;|&#60;|&gt;|&#62;|&amp;|&#38;|&quot;|&#34;|&apos;|&#39;|&cent;|&#162;|&pound;|&#163;|&yen;|&#165;|&euro;|&#8364;|&sect;|&#167;|&copy;|&#169;|&reg;|&#174;|&times;|&#215;|&divide;|&#247;|&)/g;
    var arrEntities = str.match(entitiesReg);
    if(arrEntities != null) {
        return true;
    }
    return false;
}

/**
 * 数字 字母下划线
 * @param password
 */
function verifyChars(containCharaters, password) {
    if(containCharaters == "" || !containCharaters) {
        return true;
    }
    var flagLetter = true;
    var flagNumber = true;
    var flagSpecialCharacters = true;
    if(containCharaters.indexOf("letter") != -1) {
        var reg = /[a-zA-Z]/g;
        flagLetter = reg.test(password);
    }

    if(containCharaters.indexOf("number") != -1) {
        var reg = /\d/g;
        flagNumber = reg.test(password);
    }

    if(containCharaters.indexOf("special_characters") != -1) {
        var reg = /[@&*$\(\){}!\.~:,\<\>]/g;
        flagSpecialCharacters = reg.test(password);
    }
    if(flagLetter && flagNumber && flagSpecialCharacters) {
        return  true;
    }
    return false;
}


function trimString(str){
    return  str.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
}


function isWeixinBrowser(){
    return /micromessenger/.test(navigator.userAgent.toLowerCase())
}

function getOsType() {
    var clientType;
    var u = navigator.userAgent;
    if (u.indexOf('Android') > -1) {
        clientType =  'Android';
    } else if (u.indexOf('iPhone') > -1) {
        clientType = 'IOS';
    } else {
        clientType = "PC";
    }
    return clientType;
}

function setCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toUTCString();
    }else{
        var expires = "";
    }
    document.cookie = name+"="+value+expires+"; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function deleteCookie(name) {
    setCookie(name,"",-1);
}


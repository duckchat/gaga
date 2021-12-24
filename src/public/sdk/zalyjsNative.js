var clientType = "iOS";
// var callbackIdParamName = "_zalyjsCallbackId"
var callbackIdParamName = "zalyjsCallbackId";

//set pc web referrer
//localStorage, prevent page flush, and the referrer is lost
var refererUrl = document.referrer;
var refererUrlKey = "documentReferer";
var thirdLoginNameKey = "thirdLoginName";

HeaderInvalid    = "_0";
HeaderErrorCode  = "_1";
HeaderErrorInfo  = "_2";
HeaderSessionid  = "_3";
HeaderHostUrl    = "_4";
HeaderReferer    = "_5";
HeaderUserAgent  = "_6";
HeaderAllowCache = "_7";
HeaderUserClientLang = "_8";
HeaderApplicationVersion = "_10";

function getUrlParam(key) {
    var pathParams = window.location.search.substring(1).split('&');
    var paramsLength = pathParams.length;
    for (var i = 0; i < paramsLength; i++) {
        var param = pathParams[i].split('=');
        if (param[0] == key) {
            var url = decodeURIComponent(param[1]);
            return url;
        }
    }
    return false;
}

try{
    //IE not defined
    if ( 'name' in Function.prototype === false ) {
        Object.defineProperty(Function.prototype, 'name', {
            get: function() {
                var name = (this.toString().match(/^function\s*([^\s(]+)/) || [])[1];
                // For better performance only parse once, and then cache the
                // result through a new accessor for repeated access.
                Object.defineProperty(this, 'name', { value: name });
                return name;
            }
        });
    }
}catch (error) {

}


var redirectUrl = getUrlParam("redirect_url");
if (redirectUrl) {
    localStorage.setItem(refererUrlKey, redirectUrl);
}

var thirdLoginName = getUrlParam("duckchat_third_login_name");
if (thirdLoginName) {
    localStorage.setItem(thirdLoginNameKey, thirdLoginName);
}

var zalyjsSiteLoginMessageBody = {};


function zalyjsCallbackHelperConstruct() {

    var thiz = this
    this.dict = {}

    //
    // var id = helper.register(callback)
    //
    this.register = function (callbackFunc) {
        var id = Math.random().toString()
        thiz.dict[id] = callbackFunc
        return id
    }

    //
    // helper.call({"_zalyjsCallbackId", "args": ["", "", "", ....]  })
    //
    this.callback = function (param) {
        try {
            // alert("enter =====" + param);
            var paramBase64Decode;
            try {
                paramBase64Decode = decodeURIComponent(escape(window.atob(param)));
            } catch (error) {
                paramBase64Decode = window.atob(param);
            }
            // js json for \n
            param = paramBase64Decode.replace(/\n/g, "\\\\n");

            var paramObj = JSON.parse(param)
            var id = paramObj[callbackIdParamName]

            var args = paramObj["args"]
            var callbackFunc = thiz.dict["" + id]
            if (callbackFunc != undefined) {
                // callback.apply(undefined, args)
                callbackFunc(args);
                delete(thiz.dict[id])
            } else {
                // do log
                console.log("callback", "" + id + "is undefined")
            }
        } catch (error) {
            console.log("callback", error)
            // do log
        }
    }
    return this
};
var zalyjsCallbackHelper = new zalyjsCallbackHelperConstruct();

getOsType();

function getOsType() {
    var u = navigator.userAgent;
    if (u.indexOf('Android') > -1) {
        clientType = 'Android';
    } else if (u.indexOf('iPhone') > -1) {
        clientType = 'IOS';
    } else {
        clientType = "PC";
    }
}

function isMobile() {
    if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
        return true;
    }
    return false;
}

//是否是android客户端
function isAndroid() {
    return clientType.toLowerCase() == "android"
}

//是否为iOS客户端
function isIOS() {
    return clientType.toLowerCase() == "ios"
}

function jsonToQueryString(json) {
    url = Object.keys(json).map(function (k) {
        return encodeURIComponent(k) + '=' + encodeURIComponent(json[k])
    }).join('&')
    return url
}


function addJsByDynamic(url) {
    var script = document.createElement("script")
    script.type = "text/javascript";
    //Firefox, Opera, Chrome, Safari 3+
    script.src = url;

    document.getElementsByTagName('head')[0].appendChild(script);
}

//
//
// Native Javascript
//
//

//-private
function zalyjsSetClientType(t) {
    clientType = t
}


//-private
function zalyjsNavOpenPage(url) {
    var messageBody = {}
    messageBody["url"] = url
    messageBody = JSON.stringify(messageBody)

    if (isAndroid()) {
        window.Android.zalyjsNavOpenPage(messageBody)
    } else if (isIOS()) {
        window.webkit.messageHandlers.zalyjsNavOpenPage.postMessage(messageBody)
    }
}

//-public
function zalyjsOpenPage(url) {
    location.href = url;
}

//-public
function zalyjsOpenNewPage(url) {
    if (isMobile()) {
        zalyjsNavOpenPage(url);
    } else {
        //new page
        window.open(url, "_blank");
    }
}

//-public
function zalyjsLoginSuccess(loginName, sessionid, isRegister, userCustoms, callback) {
    var callbackId = zalyjsCallbackHelper.register(callback)
    var thirdLoginName = localStorage.getItem(thirdLoginNameKey);
    if (thirdLoginName == null || thirdLoginName == undefined) {
        thirdLoginName = ""
    }
    var messageBody = {}
    messageBody["loginName"] = loginName;
    messageBody["sessionid"] = sessionid;
    messageBody["isRegister"] = (isRegister == true ? true : false);
    messageBody['thirdPartyKey'] = thirdLoginName;
    messageBody[callbackIdParamName] = callbackId;
    if(userCustoms) {
        messageBody['userCustoms'] = userCustoms;
    }
    messageBody = JSON.stringify(messageBody);

    if (isAndroid()) {
        window.Android.zalyjsLoginSuccess(messageBody)
    } else if (isIOS()) {
        window.webkit.messageHandlers.zalyjsLoginSuccess.postMessage(messageBody)
    } else {
        loginPcClient(messageBody, callback);
    }
}

// -private  登录pc, 暂时没有使用callbackId,
function loginPcClient(messageBody, callback) {
    messageBody = JSON.parse(messageBody);
    var refererUrl = localStorage.getItem(refererUrlKey);
    zalyjsSiteLoginMessageBody = messageBody;
    zalyjsSiteLoginMessageBody.callback = callback;
    if (!refererUrl) {
        refererUrl = "./index.php";
    }

    if (messageBody.isRegister == false) {
        if (refererUrl.indexOf("?") > -1) {
            var jsUrl = refererUrl + "&action=page.js&loginName=" + messageBody.loginName + "&success_callback=zalyjsApiSiteLogin&fail_callback=" + callback.name;
        } else {
            var jsUrl = refererUrl + "?action=page.js&loginName=" + messageBody.loginName + "&success_callback=zalyjsApiSiteLogin&fail_callback=" + callback.name;
        }
        addJsByDynamic(jsUrl);
        return;
    }
    zalyjsApiSiteLogin();
}


function getLanguage() {
    var nl = navigator.language;
    if ("zh-cn" == nl || "zh-CN" == nl) {
        return "1";
    }
    return "0";
}
// -private 登录成功后，web回调
function zalyjsApiSiteLogin() {
    var refererUrl = localStorage.getItem(refererUrlKey);
    if (!refererUrl) {
        refererUrl = "./index.php";
    }

    var body = {
        "@type":  "type.googleapis.com/site.ApiSiteLoginRequest",
        "preSessionId":zalyjsSiteLoginMessageBody['sessionid'],
        "loginName":zalyjsSiteLoginMessageBody['loginName'],
        "isRegister":zalyjsSiteLoginMessageBody['isRegister'],
        "thirdPartyKey":zalyjsSiteLoginMessageBody['thirdPartyKey']
    };
    if(zalyjsSiteLoginMessageBody.hasOwnProperty("userCustoms")) {
        body["userCustoms"] =  zalyjsSiteLoginMessageBody['userCustoms'];
    }
    var header = {};
    header[HeaderHostUrl] = refererUrl;
    header[HeaderUserClientLang] = getLanguage();
    header[HeaderUserAgent] = navigator.userAgent;
    var packageId = localStorage.getItem("packageId");

    var transportData = {
        "action" : "api.site.login",
        "body": body,
        "header" : header,
        "packageId" : Number(packageId),
    };

    var transportDataJson = JSON.stringify(transportData);
    if (refererUrl.indexOf("?") > -1) {
        var url = refererUrl + "&action=api.site.login&body_format=json";
    } else {
        var url = refererUrl + "?action=api.site.login&body_format=json";
    }

    var http = new XMLHttpRequest();
    http.open('POST', url, true);

    //Send the proper header information along with the request
    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
            var results = JSON.parse(http.responseText);

            if(results.hasOwnProperty("header") && results.header[HeaderErrorCode] == "success") {
                var sessionId = results.body['sessionId'];

                if(refererUrl.indexOf("?") !=-1 ) {
                    refererUrl = refererUrl+"&token="+sessionId
                } else {
                    refererUrl = refererUrl+"?token="+sessionId
                }
                localStorage.clear();
                try {
                    window.parent.location.href = refererUrl
                } catch (error) {
                    window.location.href = refererUrl;
                }
            } else {
                var result = {
                    "errorInfo" : results.header[HeaderErrorInfo]
                }
                var callbackName = zalyjsSiteLoginMessageBody.callback(result)
            }
        }
    }
    http.send(transportDataJson);
}

//// -private web 检查用户是否已经被注册
function zalyjsWebCheckUserExists(failedCallback, successCallback) {

    var refererUrl = localStorage.getItem(refererUrlKey);
    if (!refererUrl) {
        refererUrl = "./index.php";
    }
    if (refererUrl.indexOf("?") > -1) {
        var jsUrl = refererUrl + "&action=page.js&loginName=" + registerLoginName + "&success_callback=" + successCallback.name + "&fail_callback=" + failedCallback.name;
    } else {
        var jsUrl = refererUrl + "?action=page.js&loginName=" + registerLoginName + "&success_callback=" + successCallback.name + "&fail_callback=" + failedCallback.name;
    }
    addJsByDynamic(jsUrl);
}

// -public
function zalyjsLoginConfig(callback) {
    var callbackId = zalyjsCallbackHelper.register(callback)

    var messageBody = {}
    messageBody[callbackIdParamName] = callbackId
    messageBody = JSON.stringify(messageBody)

    if (isAndroid()) {
        window.Android.zalyjsLoginConfig(messageBody)
    } else if (isIOS()) {
        window.webkit.messageHandlers.zalyjsLoginConfig.postMessage(messageBody)
    } else {
        var siteConfigJsUrl = "./index.php?action=page.siteConfig&callback=" + callback.name;
        addJsByDynamic(siteConfigJsUrl);
    }
}

//-public
function zalyjsClosePage() {
    if (isAndroid()) {
        window.Android.zalyjsNavClose()
    } else if (isIOS()) {
        window.webkit.messageHandlers.zalyjsNavClose.postMessage("");
    } else {
        window.close();
    }
}

//-public
//siteAddress => 127.0.0.1:8888
function zalyjsGoto(siteAddress, page, xarg) {

    if (siteAddress == null || siteAddress == undefined || siteAddress == "") {
        siteAddress = "0.0.0.0";
    }

    var gotoUrl = "duckchat://" + siteAddress + "/goto?page=" + page + "&x=" + xarg;

    if (isAndroid()) {
        window.Android.zalyjsGoto(gotoUrl);
    } else if (isIOS()) {
        window.webkit.messageHandlers.zalyjsGoto.postMessage(gotoUrl);
    } else {
        if(siteAddress =="0.0.0.0") {
            try{
                siteAddress = parent.location.href;
            }catch (error) {
                siteAddress = location.href;
            }
        }
        if(siteAddress.indexOf("?") != -1) {
            var gotoUrl = siteAddress + "&page=" + page + "&x=" + xarg;
        } else {
            var gotoUrl = siteAddress + "?page=" + page + "&x=" + xarg;
        }
        var host = location.host;
        if(location.port) {
            host = host+":"+location.port;
        }
        if(siteAddress.indexOf(host) != -1) {
            window.open(gotoUrl, "_top");
            return;
        }
        window.open(gotoUrl, "_blank");
    }
}

//-public
function zalyjsBackPage() {
    if (isAndroid()) {
        window.Android.zalyjsNavBack();
    } else if (isIOS()) {
        var messageBody = {};
        window.webkit.messageHandlers.zalyjsNavBack.postMessage("");
    }
}


//-public
function zalyjsImageUpload(callback) {
    var callbackId = zalyjsCallbackHelper.register(callback);
    var messageBody = {};
    messageBody[callbackIdParamName] = callbackId;
    messageBody = JSON.stringify(messageBody);

    if (isAndroid()) {
        window.Android.zalyjsImageUpload(messageBody);
    } else if (isIOS()) {
        window.webkit.messageHandlers.zalyjsImageUpload.postMessage(messageBody);
    }
}

wsObj = "";
landingPageUrl="";
var config  = localStorage.getItem(siteConfigKey);
var enableWebsocketGw = localStorage.getItem(websocketGW);


wsUrlSuffix = "?body_format=json";

var packageId = localStorage.getItem(PACKAGE_ID);

if(packageId == null) {
    localStorage.setItem(PACKAGE_ID, 1);
}

var protocol = window.location.protocol;
var host = window.location.host;
var pathname = window.location.pathname;
originDomain = protocol+"//"+host+pathname;

$(":input").attr("autocapitalize", "off");

function ZalyIm(params)
{
    var config = params.config;
    localStorage.setItem(siteConfigKey, JSON.stringify(config));

    DefaultTitle = config.name;
    document.title = config.name;
    var serverAddressForApi = config.serverAddressForApi;
    localStorage.setItem(apiUrl, serverAddressForApi);
    var loginPluginProfile = params.loginPluginProfile;
    var webSocketGwDomain = config[siteConfigKeys.serverAddressForIM];
    if(webSocketGwDomain == undefined || webSocketGwDomain == null || webSocketGwDomain.length<1 || webSocketGwDomain.indexOf("http://") > -1 ||  webSocketGwDomain.indexOf("https://") > -1) {
        localStorage.setItem(websocketGW, "false");////是否开启    console.log("webSocketGwDomain ==" + webSocketGwDomain);
            setInterval(function (args) {
                try{
                    syncMsgForRoom();
                }catch (error) {
                }
            }, 1000);
    } else {
        var webSocketGw = webSocketGwDomain + wsUrlSuffix;
        if(webSocketGwDomain.length > 1) {
            localStorage.setItem(websocketGW, "true");////是否开启
            localStorage.setItem(websocketGWUrl, webSocketGw);
            try{
                auth();
            }catch(error) {

            }
        }
    }
    localStorage.setItem(siteLoginPluginKey, JSON.stringify(loginPluginProfile))
    landingPageUrl = loginPluginProfile.landingPageUrl;

    try{
        displayFrontPage();
    }catch (error) {

    }

}


function requestSiteConfig(callback)
{
    var action  = "api.site.config";
    var reqData = {};
    handleClientSendRequest(action, reqData, callback);
}

requestSiteConfig(ZalyIm);


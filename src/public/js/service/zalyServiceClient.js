

function handleClientSendRequest(action, reqData, callback)
{
    try {
        var requestName = ZalyAction.getReqeustName(action);
        var requestUrl  = ZalyAction.getRequestUrl(action);

        var body = {};
        body["@type"] = "type.googleapis.com/"+requestName;
        for(var key in reqData) {
            body[key] = reqData[key];
        }

        var sessionId = $(".service_session_id").attr("data");

        var header = {};
        header[HeaderSessionid] = sessionId;
        header[HeaderHostUrl] = originDomain;
        header[HeaderUserClientLang] = getLanguage();
        header[HeaderUserAgent] = navigator.userAgent;
        header[HeaderUserAgent] = navigator.userAgent;
        var packageId = localStorage.getItem(PACKAGE_ID);

        var transportData = {
            "action" : action,
            "body": body,
            "header" : header,
            "packageId" : Number(packageId),
        };

        localStorage.setItem(PACKAGE_ID, (Number(packageId)+1));

        var transportDataJson = JSON.stringify(transportData);
        $.ajax({
            method: "POST",
            url:requestUrl,
            data: transportDataJson,
            success:function (resp, status, request) {
                var debugInfo = request.getResponseHeader('duckchat-debugInfo');
                if(debugInfo != null) {
                    console.log("debug-info ==" + debugInfo);
                }
                handleClientReceivedMessage(resp, callback);
            }
        });
    } catch(e) {
        console.log(e.message);
        isSyncingMsg = false;
        return false;
    }
}

function handleClientReceivedMessage(resp, callback)
{
    try{
        var result = JSON.parse(resp);
        if(result.header != undefined && result.header.hasOwnProperty(HeaderErrorCode)) {
            if(result.header[HeaderErrorCode] != "success") {

                try{
                    hideLoading();
                }catch (error){
                    console.log(error)
                }
                if(result.header[HeaderErrorCode] == ErrorSessionCode || result.header[HeaderErrorCode] == ErrorSiteInit) {
                    closeChatDialog();
                    return ;
                }

                switch(result.action)
                {
                    case "api.friend.profile":
                        callback(result.body);
                        return;
                    case "api.friend.apply":
                        if(result.header[HeaderErrorCode] == errorFriendIsKey) {
                            callback("error.friend.is");
                            return;
                        }
                    case "api.group.members":
                    case "api.group.profile":
                        callback(result);
                        return;
                    default:
                        alert(result.header[HeaderErrorInfo]);
                        return;
                }

            }
        }
        if(callback instanceof Function && callback != undefined) {
            callback(result.body);
        }
    }catch (error) {
        console.log(error);
        isSyncingMsg = false;
    }
}


wsImObj = "";
wsUrl = localStorage.getItem(websocketGWUrl);

enableWebsocketGw = localStorage.getItem(websocketGW);

function websocketIm(transportDataJson, callback)
{
    ////TODO gateway 地址需要传入
    if(!wsImObj || wsImObj == '' || wsImObj.readyState == WS_CLOSED || wsImObj.readyState == WS_CLOSING) {
        wsImObj = new WebSocket(wsUrl);
        if(wsImObj.readyState == WS_OPEN) {
            auth();
        }
    }

    if(wsImObj.readyState == WS_OPEN) {
        wsImObj.send(transportDataJson);
    }

    wsImObj.onopen = function(evt) {
        //TODO auth
        wsImObj.send(transportDataJson);
    };

    wsImObj.onmessage = function(evt) {
        var resp = evt.data;
        handleReceivedImMessage(resp, callback);
    };

    wsImObj.onclose = function(evt) {
        reConnectWs()
    };
    wsImObj.onerror = function (evt) {
        reConnectWs()
    };
}

function createWsConnect()
{
    if(!wsImObj || wsImObj == '' || wsImObj.readyState == WS_CLOSED || wsImObj.readyState == WS_CLOSING) {
        wsImObj = new WebSocket(wsUrl);
        if(wsImObj.readyState == WS_OPEN) {
            auth();
        }
    }
}

function reConnectWs()
{
    if(lockReconnect == true) return;
    lockReconnect = true;
    setTimeout(function () {
        createWsConnect();
        lockReconnect = false;
    }, 1000);
}


function handleImSendRequest(action, reqData, callback)
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
        var packageId = localStorage.getItem(PACKAGE_ID);

        var transportData = {
            "action" : action,
            "body": body,
            "header" : header,
            "packageId" : Number(packageId),
        };

        var packageId = localStorage.setItem(PACKAGE_ID, (Number(packageId)+1));

        var transportDataJson = JSON.stringify(transportData);

        var enableWebsocketGw = localStorage.getItem(websocketGW);
        wsUrl = localStorage.getItem(websocketGWUrl);

        if(enableWebsocketGw == "true" && wsUrl != null && wsUrl) {
            websocketIm(transportDataJson, callback);
        } else {
            $.ajax({
                method: "POST",
                url:requestUrl,
                // dataType:"json",
                data: transportDataJson,
                success:function (resp, status, request) {
                    // console.log("status ==" + status);
                    var debugInfo = request.getResponseHeader('duckchat-debugInfo');
                    if(debugInfo != null) {
                        console.log("debug-info ==" + debugInfo);
                    }
                    if(resp) {
                        handleReceivedImMessage(resp, callback);
                    }
                },
                fail: function () {
                    isSyncingMsg = false;
                }
            });
        }
    } catch(e) {
        isSyncingMsg = false;
        return false;
    }
}


function handleReceivedImMessage(resp, callback)
{

    try{
        var result = JSON.parse(resp);
        if(result.action == ZalyAction.im_cts_auth_key) {
            try{
                handleAuth();
            }catch (error) {
            }
            return;
        }

        if(result.action == ZalyAction.im_stc_news_key) {
            syncMsgForRoom();
            return;
        }

        if(result.header != undefined && result.header.hasOwnProperty(HeaderErrorCode)) {
            if(result.header[HeaderErrorCode] != "success") {
                if(result.header[HeaderErrorCode] == ErrorSessionCode || result.header[HeaderErrorCode] == ErrorSiteInit) {
                    if(wsImObj != "" && wsImObj != undefined) {
                        wsImObj.close();
                    }
                    closeChatDialog();
                    return;
                }
                alert(result.header[HeaderErrorInfo]);
                return;
            }
        }

        if(result.action == ZalyAction.im_stc_message_key) {
            handleSyncServiceMsgForRoom(result.body);
            return;
        }

        if(callback instanceof Function && callback != undefined) {
            callback(result.body);
            return;
        }
    }catch (error) {
        console.log(error.message);
        isSyncingMsg = false;
    }

}


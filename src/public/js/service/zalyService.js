
roomKey = "service_room_";
isSyncingMsg = false;
chatSessionIdKey = "service_chat_sessionid";



var isPingSendNum = 0;
var pingIntervalId = false;

function handleAuth()
{
    try{
        pingFunc();
        if(pingIntervalId != false) {
            clearInterval(pingIntervalId);
            pingIntervalId = false;
        }
        pingIntervalId = setInterval(function () {
            if(isPingSendNum > 1) {
                isPingSendNum = 0;
                auth();
                return;
            }
            pingFunc();
        }, 10000);

    }catch (error) {
        console.log(error)
    }
    syncMsgForRoom();
}

function auth()
{
    var action = "im.cts.auth";
    handleImSendRequest(action, "", handleAuth);
}

function pingFunc() {
    ++isPingSendNum;
    var action = 'im.cts.ping';
    handleImSendRequest(action, "", handlePingFunc);
}

function handlePingFunc() {
    isPingSendNum = 0;
}

isPreSyncingMsgTime = "";
function syncMsgForRoom()
{
    if((Date.parse(new Date()) - isPreSyncingMsgTime) > 10000) {
        isSyncingMsg = false;
    }

    if(isSyncingMsg == true) {
        return ;
    }

    isPreSyncingMsgTime = Date.parse(new Date());
    isSyncingMsg = true;
    var action = "im.cts.sync";

    var reqData = {
        "u2Count" : defaultCountKey,
        "groupCount" : defaultCountKey,
    };
    handleImSendRequest(action, reqData, handleSyncServiceMsgForRoom);
}

function handleSyncServiceMsgForRoom(results)
{
    try{
        var list = results.list;
        if(list){
            var length = list.length;
            ////从小到大排序
            list.sort(compare);
            var groupUpdatePointer = {};
            var u2UpdatePointer = 0;
            var i;
            var isNeewUpdatePointer = false;
            for(i=0; i<length; i++) {
                var msg = list[i];
                if(msg.hasOwnProperty("toGroupId") && msg.toGroupId.length>0 &&((msg.hasOwnProperty("treatPointerAsU2Pointer") && msg.treatPointerAsU2Pointer == false) || !msg.hasOwnProperty("treatPointerAsU2Pointer"))) {
                    isNeewUpdatePointer = true;
                    var groupId = msg.toGroupId;
                    if(groupUpdatePointer.hasOwnProperty(groupId)) {
                        var pointer = groupUpdatePointer[groupId];
                        if(Number(pointer) < Number(msg.pointer)) {
                            groupUpdatePointer[groupId] = msg.pointer;
                        }
                    } else {
                        groupUpdatePointer[groupId] = msg.pointer;
                    }
                } else {
                    if(msg.pointer != undefined) {
                        isNeewUpdatePointer = true;
                        if(msg.pointer > u2UpdatePointer) {
                            u2UpdatePointer = msg.pointer;
                        }
                    }
                }
                handleSyncMsg(msg);
            }

            isSyncingMsg = false;

            if(isNeewUpdatePointer == true) {
                var updateMsgPointerData = {
                    u2Pointer:u2UpdatePointer,
                    groupsPointer : groupUpdatePointer,
                };
                updateMsgPointer(updateMsgPointerData);
            }
        }
    }catch (error) {
        console.log(error);
        isSyncingMsg = false;
    }
}

function handleSyncMsg(msg)
{
    if(msg.type == MessageType.MessageEventSyncEnd) {
        return ;
    }
    if(msg.type == MessageType.MessageEventStatus) {
        var msgId     = msg.msgId;
        var msgStatus = msg.status["status"];
        handleMsgStatusResult(msgId, msgStatus);
        return;
    }
    if(msg.type == MessageType.MessageEventFriendRequest) {
        showOtherWebNotification();
        getFriendApplyList();
        return;
    };

    var msg = handleMsgInfo(msg);
    var currentChatSessionId = localStorage.getItem(chatSessionIdKey);
    var isNewMsg = handleMsgForMsgRoom(msg.chatSessionId, msg);

    ///是自己群的消息，并且是新消息
    if(msg.chatSessionId  == currentChatSessionId && isNewMsg) {
        var isEndMsgDialog = isCheckEndMsgDialog();
        appendMsgHtmlToChatDialog(msg);
        if(isEndMsgDialog == true) {
            msgBoxScrollToBottom();
        }
        localStorage.setItem(newSiteTipKey, "new_msg");
    } else if(msg.chatSessionId  == currentChatSessionId && !isNewMsg) {
        if(msg.type == MessageType.MessageRecall) {
            try{
                var msgId = msg['recall'].msgId;
                var msgContent = msg["recall"].msgText !== undefined && msg["recall"].msgText != null ? msg["recall"].msgText : "此消息被撤回";
                var html = template("tpl-receive-msg-notice", {
                    msgContent:msgContent,
                    timeServer:msg.timeServer
                });
                var tagId = "msg-row-"+msgId;
                var oldNode = document.getElementById(tagId);
                oldNode.parentNode.replaceChild($(html)[0],oldNode);
            }catch (error) {
            }
        }
    } else if(msg.chatSessionId != currentChatSessionId && isNewMsg) {
        if(msg.chatSessionId != serviceToken) {
            localStorage.setItem(newSiteTipKey, "new_msg");
        }
    }
}


function isCheckEndMsgDialog()
{
    var rightchatBox = $(".service_right-chatbox")[0];
    var sh = rightchatBox.scrollHeight;
    var ch = rightchatBox.clientHeight;
    var st = $(".service_right-chatbox").scrollTop();
    ///差值小于等于Math.ceil(ch*2) px末， 默认底部
    if(sh - ch - st <= Math.ceil(ch*2)) {
        return true
    }
    return false;
}


function updateMsgPointer(reqData)
{
    var action = "im.cts.updatePointer";
    handleClientSendRequest(action, reqData, "");
}

function handleMsgStatusResult(msgId, msgStatus)
{
   try{
       var msgIdInChatSession = msgIdInChatSessionKey + msgId;
       var chatSessionId = sessionStorage.getItem(msgIdInChatSession);
       if(msgStatus == MessageStatus.MessageStatusFailed) {
           $(".msg_status_failed_"+msgId)[0].style.display = "flex";
           $(".msg_status_loading_"+msgId)[0].style.display = "none";
           $(".msg_status_loading_"+msgId).attr("is-display", "none");
           updateMsgStatus(msgId, chatSessionId, MessageStatus.MessageStatusFailed);
       } else {
           $(".msg_status_loading_"+msgId)[0].style.display = "none";
           $(".msg_status_loading_"+msgId).attr("is-display", "none");
       }
       sessionStorage.removeItem(msgIdInChatSession);
   }catch (error) {

   }
}

/**
 *
 * @param chatSessionId
 * @param pushMsg
 * @returns msgList or isNewMsg
 */
function handleMsgForMsgRoom(chatSessionId, pushMsg)
{
    var roomChatSessionKey = roomKey + chatSessionId;

    var msgListJsonStr = localStorage.getItem(roomChatSessionKey);
    var isFlag = moreThanMaxStorageSore(roomChatSessionKey);
    if(isFlag) {
        msgListJsonStr = false;
    }
    var msgList;
    try{
        if(!msgListJsonStr) {
            msgList = new Array();
        } else {
            msgList = JSON.parse(msgListJsonStr);
        }

        while(msgList.length>=300) {
            msgList.shift();
        }
        try{
            if(pushMsg != undefined) {
                if(pushMsg.type == MessageType.MessageRecall) {
                    var msgListLength = msgList.length;
                    for(var i=0;i<msgListLength; i++) {
                        var msg = msgList[i];
                        if(msg.msgId == pushMsg['recall'].msgId) {
                            msg.type = MessageType.MessageRecall;
                            msg.recall = pushMsg.recall;
                            msgList[i] = msg;
                        }
                    }
                    var isNewMsg = false;
                    localStorage.setItem(roomChatSessionKey, JSON.stringify(msgList));
                } else {
                    msgList.push(pushMsg);
                    var isNewMsg = uniqueMsgAndCheckMsgId(msgList, pushMsg.msgId, roomChatSessionKey);
                }
                return isNewMsg;
            }
        }catch(error) {
            console.log(error)
        }
        msgList.sort(compare);
        return msgList;
    }catch (error){
        console.lgo(error);
        if(error.name == "QuotaExceededError" || error.name == "ReferenceError") {
            msgList = new Array();
            if(pushMsg != undefined) {
                msgList.push(pushMsg);
                var isNewMsg = uniqueMsgAndCheckMsgId(msgList, pushMsg.msgId, roomChatSessionKey);
                return isNewMsg;
            }
            msgList.sort(compare);
            return msgList;
        }
    }
}



function compare(msg1, msg2) {
    if (msg1.timeServer < msg2.timeServer)
        return -1;
    if (msg1.timeServer > msg2.timeServer)
        return 1;
    return 0;
}



function msgBoxScrollToBottom()
{
    var rightchatBox = $(".service_right-chatbox")[0];
    var sh = rightchatBox.scrollHeight;
    var ch  = rightchatBox.clientHeight;
    var scrollTop = sh-ch;
    $(".service_right-chatbox").scrollTop(scrollTop);

}

function addMsgToChatDialog(chatSessionId, msg)
{
    msg.status = MessageStatus.MessageStatusSending;

    setTimeout(function () {
        var msgLoadings = $("[is-display='yes']");
        var length = msgLoadings.length;
        var i;
        for(i=0;i<length;i++) {
            var msgLoading = msgLoadings[i];
            var msgId = $(msgLoading).attr("msgId");
            var sendTime = $(msgLoading).attr("sendTime");
            var nowTime = Date.now();
            if(nowTime - sendTime >= 10000) {
                handleMsgStatusResult(msgId, MessageStatus.MessageStatusFailed);
            }
        }
    }, 10000);///10秒执行
    appendMsgHtmlToChatDialog(msg);

    ///在上部分查看消息的时候不滚动
    msgBoxScrollToBottom();
}

expendTime=0;
function appendMsgHtmlToChatDialog(msg)
{
    if(msg == undefined) {
        return;
    }
    var html = "";
    var msgType = msg.type;
    var msgId = msg.msgId;

    var sendBySelf;

    if( msg.fromUserId != serviceToken) {
        sendBySelf = false;
    } else if(msg.fromUserId == serviceToken) {
        sendBySelf = true;
        msg.userAvatar = serviceAvatar;
    }

    var msgTime = getMsgTimeByMsg(msg.timeServer);
    var groupUserImageClassName = msg.roomType == GROUP_MSG ? "group-user-img group-user-img-"+msg.msgId : "";
    var msgStatus = msg.status ? msg.status : "";
    var userAvatar =  getNotMsgImgUrl(msg.userAvatar);
    if(sendBySelf) {
        switch(msgType) {
            case MessageType.MessageText :
                var msgContent = msg['text'].body;
                html = template("tpl-send-msg-text", {
                    roomType: msg.roomType,
                    nickname:serviceNickname,
                    msgId : msgId,
                    msgTime : msgTime,
                    msgContent:msgContent,
                    msgStatus:msgStatus,
                    avatar:userAvatar,
                    userId:msg.fromUserId,
                    timeServer:msg.timeServer,
                    msgType:msgType,
                });
                break;
            case MessageType.MessageWebNotice:
                var hrefUrl = getWebMsgHref(msg.msgId, msg.roomType);
                html = template("tpl-receive-msg-web-notice", {
                    hrefUrl:hrefUrl
                });
                break;
            case MessageType.MessageNotice:
                var msgContent = msg["notice"].body;
                html = template("tpl-receive-msg-notice", {
                    msgContent:msgContent,
                    timeServer:msg.timeServer
                });
                break;
            default:
                var msgContent = "[当前版本不支持此信息，请尝试升级客户端版本] ";
                html = template("tpl-send-msg-default", {
                    roomType: msg.roomType,
                    nickname:serviceNickname,
                    msgId : msgId,
                    msgTime : msgTime,
                    msgStatus:msgStatus,
                    msgContent:msgContent,
                    avatar:userAvatar,
                    userId:msg.fromUserId,
                    msgType:msgType,
                    timeServer:msg.timeServer
                });
                break;
        }
    } else {
        var isMaster = false;
        switch(msgType) {
            case MessageType.MessageText:
                var msgContent = msg['text'].body;
                html = template("tpl-receive-msg-text", {
                    roomType: msg.roomType,
                    nickname: msg.nickname,
                    msgId : msgId,
                    userId :msg.fromUserId,
                    msgTime : msgTime,
                    msgContent:msgContent,
                    groupUserImg : groupUserImageClassName,
                    avatar:userAvatar,
                    msgType:msgType,
                    isMaster:isMaster
                });
                break;
            case MessageType.MessageNotice:
                var msgContent = msg["notice"].body;
                html = template("tpl-receive-msg-notice", {
                    msgContent:msgContent,
                });
                break;
            default:
                var msgContent = "[当前版本不支持此信息，请尝试升级客户端版本] ";
                html = template("tpl-receive-msg-default", {
                    roomType: msg.roomType,
                    nickname:msg.nickname,
                    msgId : msgId,
                    msgTime : msgTime,
                    msgStatus:msgStatus,
                    msgContent:msgContent,
                    avatar:userAvatar,
                    userId :msg.fromUserId,
                    timeServer:msg.timeServer
                });
        }
    }

    if(msgType == MessageType.MessageText) {
        html = handleMsgContentText(html);
    }
    var currentChatsessionId = localStorage.getItem(chatSessionIdKey);

    if(currentChatsessionId == msg.chatSessionId) {
        $(".service_right-chatbox[chat-session-id="+msg.chatSessionId+"]").append(html);
    }
}

//check is enter back
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


$(document).on("click", ".send_msg" , function(){
    sendMsgBySend();
});


//发送消息
function sendMsgBySend()
{
    var chatSessionId   = localStorage.getItem(chatSessionIdKey);
    var chatSessionType = localStorage.getItem(chatSessionId);
    var msgContent = $(".msg_content").val();

    msgContent = trimString(msgContent);

    if(msgContent.length < 1) {
        return false;
    }

    if(msgContent.length > 1000) {
        alert("文本过长");
        return false;
    }
    $(".msg_content").val('');

    sendMsg(chatSessionId, chatSessionType, msgContent, MessageType.MessageText);
}


function sendMsgByKeyDown(event)
{
    if(!checkIsEnterBack(event)) {
        return ;
    }
    sendMsgBySend();
}

function sendMsg( chatSessionId, chatSessionType, msgContent, msgType, params)
{
    var action = "im.cts.message";
    var msgId  = Date.now();

    var message = {};
    message['fromUserId'] = serviceToken;
    var msgIdSuffix = "";
    if(chatSessionType == U2_MSG) {
        message['roomType'] = U2_MSG;
        message['toUserId'] = chatSessionId
        msgIdSuffix = "U2-";
    } else {
        message['roomType'] = GROUP_MSG;
        message['toGroupId'] = chatSessionId;
        msgIdSuffix = "GROUP-";
    }
    var msgId = msgIdSuffix + msgId+"";
    message['msgId'] = msgId;

    message['timeServer'] = Date.parse(new Date());

    switch (msgType) {
        case MessageType.MessageText:
            message['text'] = {body:msgContent};
            message['type'] = MessageType.MessageText;
            displayContent = msgContent;
            break;
        case MessageType.MessageImage:
            message['type'] = MessageType.MessageImage;
            message['image'] = {url:msgContent, width:msgImageSize.width, height:msgImageSize.height};
            displayContent = "[图片消息]";
            break;
        case MessageType.MessageDocument:
            message['type'] = MessageType.MessageDocument;
            message['document'] = {url:msgContent, size:params.size, name:params.name};
            displayContent = "[文件]";
            break;
    }
    var reqData = {
        "message" : message
    };
    var msgIdInChatSession = msgIdInChatSessionKey + msgId;
    sessionStorage.setItem(msgIdInChatSession, chatSessionId);

    handleImSendRequest(action, reqData, "");
    message['chatSessionId'] = chatSessionId;
    handleMsgForMsgRoom(chatSessionId, message);
    addMsgToChatDialog(chatSessionId, message);
};



//replace \n from html
function trimMsgContentBr(html)
{
    html = html.replace(new RegExp('\n','g'),"<br>");
    html = html.replace(new RegExp('^\\<br>+', 'g'), '');
    html = html.replace(new RegExp('\\<br>+$', 'g'), '');
    html = html.replace(new RegExp('&#38;','g'),"&");
    return html;
}


//replace \n from html
function trimMsgContentNewLine(html)
{
    html = html.replace(new RegExp('<br>','g'),"\n");
    html = html.replace(new RegExp('&amp;','g'),"&");
    return html;
}

function handleMsgContentText(str)
{
    html = trimMsgContentBr(str);
    $(html).find("[msg_content_for_handle]").each(function () {
        var str = $(this).html();
        if(str == undefined) {
            return html;
        }
        str = str.replace(new RegExp('&amp;','g'),"&");

        var reg=/(blob:)?((http|https|ftp|zaly|duckchat):\/\/)?[@\w\-_]+(\:[0-9]+)?(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/g;
        var arr = str.match(reg);
        if(arr == null) {
            return str;
        }
        var length = arr.length;
        for(var i=0; i<length;i++) {
            var urlLink = arr[i];
            if(urlLink.indexOf("blob:") == -1 &&
                (urlLink.indexOf("http://") != -1
                    || urlLink.indexOf("https://") != -1
                    || urlLink.indexOf("ftp://") != -1
                    || urlLink.indexOf("zaly://") != -1
                    || urlLink.indexOf("duckchat://") != -1
                    ||  IsURL (urlLink)
                )
            ) {
                var newUrlLink = urlLink;
                if(urlLink.indexOf("://") == -1) {
                    newUrlLink = "http://"+urlLink;
                }
                var urlLinkHtml = "<a href='"+newUrlLink+"'target='_blank'>"+urlLink+"</a>";
                html = html.replace(urlLink, urlLinkHtml);
            }
        }
    });

    return html;
}

function IsURL (url) {
    var urls = url.split("?");
    var urlAndSchemAndPort = urls.shift();
    var urlAndPort = urlAndSchemAndPort.split("://").pop();
    url = urlAndPort.split(":").shift();
    var ipRegex = '^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$';
    var ipReg=new RegExp(ipRegex);

    if(!ipReg.test(url)) {
        var domainSuffix = url.split(".").pop();
        domainSuffix = domainSuffix.toLowerCase();
        var urlDomain = "com,cn,net,xyz,top,tech,org,gov,edu,ink,red,int,mil,pub,biz,cc,name,mobi,travel,info,tv,pro,coop,aero,me,app,onlone,shop" +
            ",club,store,life,global,live,museum,jobs,cat,tel,bid,pub,foo,site";
        if(urlDomain.indexOf(domainSuffix) != -1) {
            return true;
        }
        return false;
    }
    return true;
}


function getMsgFromRoom(chatSessionId)
{
    var msgList = handleMsgForMsgRoom(chatSessionId, undefined);

    $(".service_right-chatbox").html("");
    if(msgList == null) {
        return;
    }

    if(msgList != null) {
        var length = msgList.length;
        var i;
        for(i=0; i<length; i++) {
            var msg = msgList[i];
            msg = handleMsgInfo(msg);
            appendMsgHtmlToChatDialog(msg);
        }
    }

    var jqElement = $(".chat_session_id_"+chatSessionId);
    msgBoxScrollToBottom();
}

function moreThanMaxStorageSore(item)
{
    var sizeStore = 0;
    var itemData=  window.localStorage.getItem(item);
    if(itemData == false || itemData == undefined || itemData == "") {
        return false;
    }
    sizeStore += itemData.length;
    sizeStore = (sizeStore / 1024 / 1024).toFixed(2)
    if(sizeStore>MaxStorageStore) {
        return true;
    }
    return false;
}



function uniqueMsgAndCheckMsgId(msgList, msgId, roomChatSessionKey)
{
    try{
        var hash = {};
        var repeatMsgId = {};
        msgList = msgList.reduce(function(item, next) {
            hash[next.msgId] ? repeatMsgId[msgId] = true : hash[next.msgId] = true && item.push(next);
            return item
        }, []);
        localStorage.setItem(roomChatSessionKey, JSON.stringify(msgList));

        return repeatMsgId[msgId] ? false : true;
    }catch (error) {
        handleSetItemError(error);
    }
    return false;
}


function getMsgTimeByMsg(time)
{
    time = Number(time);
    var date = new Date(time); //获取一个时间对象

    var minutes =  date.getMinutes()>=10 ? date.getMinutes():"0"+date.getMinutes();
    var month = date.getMonth() >= 9 ? (date.getMonth()+1) : "0"+ (date.getMonth()+1);

    return date.getFullYear() + '-' + month + '-' +date.getDate() + " " + date.getHours()+":"+minutes;  // 获取完整的年份(4位,1970)
}


function  updateMsgStatus(msgId, chatSessionId, msgStatus)
{
    var msgList = handleMsgForMsgRoom(chatSessionId, undefined);
    var i;
    var length = msgList.length;

    for(i=0; i<length;i++) {
        var msg = msgList[i];
        if(msg.msgId == msgId) {
            msg.status = msgStatus;
            msgList[i] = msg ;
        }
    }
    var roomChatSessionKey = roomKey + chatSessionId;
    localStorage.setItem(roomChatSessionKey, JSON.stringify(msgList));
}


function handleMsgInfo(msg)
{
    var toGroupId = msg.toGroupId;
    var userId;

    if(toGroupId != undefined) {
        msg.className = "group-profile";
        msg.chatSessionId = toGroupId;
        msg.roomType = GROUP_MSG;
        var groupProfile = getGroupProfile(msg.chatSessionId);
        if(groupProfile) {
            msg.name = groupProfile['name'];
            msg.avatar = groupProfile['avatar'];
        }
        userId = msg.fromUserId;
    } else {
        msg.className = "u2-profile";
        if(msg.fromUserId == serviceToken) {
            msg.chatSessionId = msg.toUserId;
        } else {
            msg.chatSessionId = msg.fromUserId;
        }
        msg.roomType = U2_MSG;
        userId = msg.chatSessionId;
    }
    var muteKey = msgMuteKey + msg.chatSessionId;
    msg.isMute = localStorage.getItem(muteKey);

    var unreadMuteKey = msgUnReadMuteKey+msg.chatSessionId;
    msg.isMuteMsgNum = localStorage.getItem(unreadMuteKey) == 1 ? 1 : 0;

    var userProfile = getFriendProfile(userId, false, handleGetFriendProfile);
    if(userProfile) {
        msg.nickname   = userProfile['nickname'];
        msg.userAvatar = userProfile['avatar'];
    } else {
        msg.nickname = "";
        msg.userAvatar = "";
    }

    return msg;
}



function getGroupProfile(groupId)
{
    var groupInfoKey = profileKey + groupId;
    var groupProfileStr = localStorage.getItem(groupInfoKey);

    var groupInfoReqKey = reqProfile+groupId;
    var nowTimestamp = Date.parse(new Date());
    var reqProfileTime = sessionStorage.getItem(groupInfoReqKey);
    var groupProfile = false;

    if(groupProfileStr != false && groupProfileStr != null) {
        try{
            groupProfile = JSON.parse(groupProfileStr);
            if(groupProfile && (nowTimestamp - groupProfile['updateTime'])<ProfileTimeout) {
                return groupProfile;
            }
        }catch (error) {

        }
    }

    setTimeout(function (groupId) {
        sessionStorage.removeItem(profileKey + groupId);
        getGroupProfile(groupId);
    }, 3000);

    if(reqProfileTime != false && reqProfileTime != null && reqProfileTime !=undefined  && ((nowTimestamp-reqProfileTime)<reqTimeout) ) {
        return false;
    }
    sessionStorage.setItem(groupInfoReqKey, nowTimestamp);
    sendGroupProfileReq(groupId, handleGetGroupProfile);
    return groupProfile;
}

function sendGroupProfileReq(groupId, callback)
{
    if(!groupId || groupId == undefined) {
        return null;
    }
    var action = "api.group.profile";
    var reqData = {
        "groupId": groupId
    };
    handleClientSendRequest(action, reqData, callback);
}


function handleGetGroupProfile(result)
{
    try{
        var groupProfile = result.profile;
        if(groupProfile) {
            groupProfile.memberType = result.memberType ? result.memberType : GroupMemberType.GroupMemberGuest;
            groupProfile.permissionJoin = groupProfile.permissionJoin ? groupProfile.permissionJoin : GroupJoinPermissionType.GroupJoinPermissionPublic;
            groupProfile['updateTime'] = Date.parse(new Date());
            localStorage.setItem(groupProfile.id, GROUP_MSG);

            var groupInfoKey = profileKey + groupProfile.id;
            localStorage.setItem(groupInfoKey, JSON.stringify(groupProfile));

            sessionStorage.removeItem(reqProfile+groupProfile.id);

            var muteKey = msgMuteKey + groupProfile.id;
            localStorage.setItem(muteKey, (result.isMute ? 1 : 0) );
            displayProfile(groupProfile.id, GROUP_MSG);
            return;
        }
    }catch (error) {

    }
}



function getFriendProfile(userId, isForceSend, callback)
{
    var friendInfoReqKey = reqProfile + userId;
    var nowTimestamp = Date.parse(new Date());
    var reqProfileTime = sessionStorage.getItem(friendInfoReqKey);

    var userInfoKey = profileKey+userId;
    var userProfile = localStorage.getItem(userInfoKey);
    if(userProfile) {
        userProfile = JSON.parse(userProfile);
        var nowTimestamp = Date.parse(new Date());
        if(!userProfile.hasOwnProperty("nickname")) {
            userProfile['nickname'] = defaultUserName;
        }
        if ((nowTimestamp - userProfile['updateTime'] ) < ProfileTimeout && isForceSend == false) {
            return userProfile;
        }
    }
    if(reqProfileTime != false && reqProfileTime != null && reqProfileTime != undefined && (nowTimestamp-reqProfileTime<reqTimeout) && isForceSend == false) {
        return false;
    }

    if(callback == undefined) {
        callback = handleGetFriendProfile;
    }
    sessionStorage.setItem(friendInfoReqKey, nowTimestamp);
    sendFriendProfileReq(userId, callback);
    return userProfile;
}

function sendFriendProfileReq(userId, callback)
{
    var action = "api.friend.profile";
    var reqData = {
        "userId" : userId
    };
    handleClientSendRequest(action, reqData, callback);
}

function handleGetFriendProfile(result)
{
    if(result == undefined) {
        return;
    }
    var profile = result.profile;

    if(profile != undefined && profile["profile"]) {
        try{
            var userProfile = profile["profile"];

            sessionStorage.removeItem(reqProfile+userProfile["userId"]);

            var userProfilekey = profileKey + userProfile["userId"];
            userProfile['updateTime'] = Date.parse(new Date());
            localStorage.setItem(userProfilekey, JSON.stringify(userProfile));

            var muteKey = msgMuteKey + userProfile["userId"];
            var mute = profile.mute ? 1 : 0;
            localStorage.setItem(muteKey, mute);

            var relationKey = friendRelationKey + userProfile["userId"];
            var relation = profile.relation == undefined ? FriendRelation.FriendRelationInvalid : profile.relation;
            localStorage.setItem(relationKey, relation);

            var customKey = friendCustomKey + userProfile["userId"];
            if(profile.hasOwnProperty("custom")) {
                localStorage.setItem(customKey, JSON.stringify(profile['custom']));
            }
            displayProfile(userProfile.userId, U2_MSG);

        }catch (error) {
            console.log(error);
        }
    }
}


function displayProfile(profileId, profileType)
{
    var name;
    var mute;
    if(profileType == U2_MSG) {
        var friendProfile = getFriendProfile(profileId, false, handleGetFriendProfile);
        name = friendProfile != false && friendProfile != null ? friendProfile.nickname : "";
        if(friendProfile != false && friendProfile != null && friendProfile.avatar) {
            var friendAvatarImg = getNotMsgImgUrl(friendProfile.avatar);
            $(".info-avatar-"+friendProfile.userId).attr("src", friendAvatarImg);
        }
    } else {
        var groupProfile = getGroupProfile(profileId);
        var groupName = groupProfile != false && groupProfile != null ? groupProfile.name : "";
        name = groupName;
        if(groupProfile != false && groupProfile != null  && groupProfile.avatar) {
            var groupProfileAvatarImg = getNotMsgImgUrl(groupProfile.avatar);
            $(".info-avatar-"+groupProfile.id).attr("src", groupProfileAvatarImg);
        }
    }

    var muteKey= msgMuteKey+profileId;
    mute = localStorage.getItem(muteKey);
    var chatSessionName = "";
    try{
        chatSessionName = name.substr(0, 8);
        chatSessionName = template("tpl-string", {
            string:chatSessionName
        });
        if(name.length>8) {
            chatSessionName += "...";
        }
    }catch (error) {
        console.log(error);
    }

    var name = template("tpl-string", {
        string : name
    });

    try{
        name = name.trim();
    }catch (error) {
        console.log(error);
    }

    $(".nickname_"+profileId).html(name);

}


function getSelfInfo()
{
    var action = "api.user.profile"
    handleClientSendRequest(action, {}, handleGetUserProfile);
}

function handleGetUserProfile(result)
{
    if(result && result.hasOwnProperty("profile") ) {
        var profile = result['profile'];
        serviceNickname = profile['public'].nickname;
        serviceLoginName = profile['public'].loginName;
        serviceAvatar = profile['public'].avatar;
        var src = getNotMsgImgUrl(serviceAvatar);
        $(".info-avatar-"+profile['public'].userId).attr("src", src);
    }
}


function getNotMsgImgUrl(avatarImgId) {
    try{
        var filePaths = avatarImgId.split('-');
        var path = "./attachment/"+filePaths[0]+"/"+filePaths[1];
        if(avatarImgId) {
            return  path;
        }
        return false;
    }catch (error) {
        return false;
    }
}




msgImageSize = "";
webObject={};

function getRoomList()
{
    var roomList = handleRoomListFromLocalStorage(undefined);
    $(".chatsession-lists").html("");
    if(roomList == undefined || roomList.length == 0) {
        var html = template("tpl-room-no-data", {});
        $(".chatsession-lists").html(html);
        displayRightPage(DISPLAY_CHAT);
        return ;
    }

    var length = roomList.length;
    var currentChatSessionId  = localStorage.getItem(chatSessionIdKey);


    var i;
    for(i=0;i <length; i++) {
        var msg = roomList[i];
        msg = handleMsgInfo(msg);
        if(!currentChatSessionId &&  i==length-1) {
            localStorage.setItem(chatSessionIdKey, msg.chatSessionId);
            currentChatSessionId = msg.chatSessionId;
        }
        if( msg.chatSessionId == currentChatSessionId) {
            localStorage.setItem(msg.chatSessionId, msg.roomType);
        }
        $(".right-chatbox").attr("chat-session-id", currentChatSessionId);

        appendOrInsertRoomList(msg, false, false);
    }

    getMsgFromRoom(currentChatSessionId);

    var roomType = localStorage.getItem(currentChatSessionId);
    getInitChatPlugin(roomType);
    displayCurrentProfile();
    msgBoxScrollToBottom();
}

function handleRoomListFromLocalStorage(roomMsg)
{
    try{
        var roomListStr = localStorage.getItem(roomListKey);
        var roomList;
        if(roomListStr) {
            roomList = JSON.parse(roomListStr);
        } else {
            roomList = new Array();
        }
        var isUpdate =false;

        if(roomMsg != undefined) {
            var length = roomList.length;
            var i;
            for(i =0; i<length;  i++) {
                var msg = roomList[i];
                if(msg!=null&& msg!= false && msg.hasOwnProperty("chatSessionId") && msg.chatSessionId == roomMsg.chatSessionId) {
                    if(msg.timeServer < roomMsg.timeServer) {
                        if(roomMsg.hasOwnProperty("text") && roomMsg["text"].body.length < 1){
                            msg.timeServer = roomMsg.timeServer;
                        } else {
                            msg = roomMsg;
                        }
                        roomList[i] = msg;
                    }
                    isUpdate = true;
                }
            }
            if(!isUpdate) {
                roomList.push(roomMsg);
            }
        }
        roomList.sort(compare);
        localStorage.setItem(roomListKey, JSON.stringify(roomList));
        return roomList;
    }catch (error) {
        storageError(error);
    }
}

function clearRoomMsgFromRoomList(chatSessionId)
{
    var roomListStr = localStorage.getItem(roomListKey);
    var roomList;
    if(roomListStr) {
        roomList = JSON.parse(roomListStr);
    } else {
        roomList = new Array();
    }
    var length = roomList.length;
    var i;
    for(i =0; i<length;  i++) {
        var msg = roomList[i];
        if(msg!=null && msg != false && msg != undefined &&  msg.hasOwnProperty("chatSessionId")) {
            if(chatSessionId == undefined) {
                localStorage.removeItem(roomKey+msg.chatSessionId);
                msg.type = MessageType.MessageText;
                msg.text = {body:""};
                $(".chatsession-row-desc-"+msg.chatSessionId).html("");
            } else if (msg.chatSessionId == chatSessionId){
                localStorage.removeItem(roomKey+msg.chatSessionId)
                msg.type = MessageType.MessageText;
                msg.text = {body:""};
                $(".chatsession-row-desc-"+msg.chatSessionId).html("");
            }
        }
        roomList[i] = msg;
    }
    localStorage.setItem(roomListKey, JSON.stringify(roomList));
}

function getMsgContentForChatSession(msg)
{
    var msgContent;
    var msgType = msg.msgType != undefined ? msg.msgType : msg.type;

    switch (msgType) {
        case MessageType.MessageText:
            msgContent = msg.hasOwnProperty("text") ? msg['text'].body: JSON.parse(msg['content']).body;
            msgContent = msgContent && msgContent.length > 10 ? msgContent.substr(0,10)+"..." : msgContent;
            break;
        case MessageType.MessageImage:
            msgContent = "[图片消息]";
            break;
        case MessageType.MessageAudio:
            msgContent = "[语音消息]";
            break;
        case MessageType.MessageNotice:
            msgContent = msg["notice"].body;
            msgContent = msgContent && msgContent.length > 10 ? msgContent.substr(0,10)+"..." : msgContent;
            break;
        case MessageType.MessageRecall:
            msgContent = "[通知]";
            break;
        case MessageType.MessageWebNotice:
            msgContent = msg["webNotice"].title;
            break
        case MessageType.MessageAudio:
            msgContent = "[语音消息]";
            break;
        case MessageType.MessageDocument:
            msgContent = "[文件]";
            break;
        case MessageType.MessageWeb:
            msgContent = "[" + msg["web"].title + "]";
            msgContent = msgContent && msgContent.length > 10 ? msgContent.substr(0,10)+"..." : msgContent;
            break;
        default:
            msgContent = "[暂不支持此类型消息]";
    }
    return msgContent;
}

//----------------------------------handle room list--------------------------------------------------------------------------

function updateRoomChatSessionContentForMsg(msg, nodes, msgContent) {

    var msgTime = msg.msgTime != undefined ? msg.msgTime : msg.timeServer;
    msgTime = getRoomMsgTime(msgTime);
    var childrens = $(nodes)[0].children;
    var unReadNum = getRoomMsgUnreadNum(msg.chatSessionId);
    var isMuteNum = localStorage.getItem(msgUnReadMuteKey+msg.chatSessionId);
    var isMute = localStorage.getItem(msgMuteKey+msg.chatSessionId);
    if(isMuteNum == 1 && isMute == 1) {
        $(".room-chatsession-unread_"+msg.chatSessionId)[0].style.display = "none";
        $(".room-chatsession-mute-num_"+msg.chatSessionId)[0].style.display = "block";
    } else {
        if(unReadNum>0 && (msg.chatSessionId != localStorage.getItem(chatSessionIdKey))) {
            $(".room-chatsession-unread_"+msg.chatSessionId).html(unReadNum);
            $(".room-chatsession-unread_"+msg.chatSessionId)[0].style.display = "block";
            $(".room-chatsession-mute-num_"+msg.chatSessionId)[0].style.display = "none";
        }
    }
    msgContent = template("tpl-string", {
        string:msgContent
    });
    if(msgContent != undefined && msgContent.length>0) {
        $(childrens[2]).html(msgContent);
    }

    var subChildrens = $(childrens[1])[0].children;
    $(subChildrens[1]).html(msgTime);
}

function appendOrInsertRoomList(msg, isInsert, showNotification)
{

    if(msg != undefined && msg.hasOwnProperty("type") && msg.type == MessageStatus.MessageEventSyncEnd) {
        return ;
    }
    var unReadNum = localStorage.getItem(roomMsgUnReadNum + msg.chatSessionId) ? localStorage.getItem(roomMsgUnReadNum + msg.chatSessionId): 0;
    unReadNum  = unReadNum > 99 ? "99+" : unReadNum;
    var name   =  msg.roomType == GROUP_MSG ? msg.name : msg.nickname;

    var nodes = $(".chat_session_id_" + msg.chatSessionId);
    var msgTime = msg.msgTime != undefined ? msg.msgTime : msg.timeServer;
    msgTime = getRoomMsgTime(msgTime);
    var msgContent = getMsgContentForChatSession(msg);

    if(isInsert) {
        handleRoomListFromLocalStorage(msg);
    }

    if(nodes.length) {
        if($(nodes).attr("msg_time") < msg.timeServer) {
            updateRoomChatSessionContentForMsg(msg, nodes, msgContent);
            sortRoomList($(nodes));
        }
        if(msg.chatSessionId == localStorage.getItem(chatSessionIdKey)) {
            $(".chat_session_id_"+msg.chatSessionId).addClass("chatsession-row-active");
        }
        if(msg.fromUserId != token && showNotification) {
            showMsgWebNotification(msg, msgContent);
        }
        return ;
    }

    var avatar = msg.roomType == GROUP_MSG ? msg.avatar : msg.userAvatar;
    avatar = getNotMsgImgUrl(avatar);
    try{
        name = name.trim();
    }catch (error) {

    }
    if(name !=undefined && name.length>10) {
        name = name.substr(0, 8) + "...";
    }

    var isSiteMaster = isJudgeSiteMasters(msg.chatSessionId);

    var html = template("tpl-chatSession", {
        className:msg.roomType == U2_MSG ? "u2-profile" : "group-profile",
        isMute:msg.isMute,
        isMuteMsgNum:msg.isMuteMsgNum,
        chatSessionId:msg.chatSessionId,
        roomType:msg.roomType,
        name:name,
        msgTime:msgTime,
        msgContent : msgContent,
        unReadNum : unReadNum,
        avatar:avatar,
        timeServer:msgTime,
        msgServerTime:msg.timeServer,
        isSiteMaster:isSiteMaster
    });

    if($(".chatsession-row").length > 0 ) {
        $(html).insertBefore($(".chatsession-row")[0]);
    } else {
        $(".chatsession-lists").html(html);
    }

    if(msg.chatSessionId == localStorage.getItem(chatSessionIdKey)) {
        $(".chat_session_id_"+msg.chatSessionId).addClass("chatsession-row-active");
        displayCurrentProfile();
    }
    if(msg.fromUserId != token && showNotification) {
        showMsgWebNotification(msg, msgContent);
    }
}
////防止两个浏览器开着，点击的时候消息列表的内容不是最新的
function updateRoomChatSessionContent(chatSessionId)
{
    var nodes = $(".chat_session_id_" + chatSessionId);
    var roomList = handleRoomListFromLocalStorage();
    var length = roomList.length;
    var i;
    for(i =0; i<length;  i++) {
        var msg = roomList[i];
        if(msg.chatSessionId == chatSessionId) {
            msg = handleMsgInfo(msg);
            var msgContent = getMsgContentForChatSession(msg);
            updateRoomChatSessionContentForMsg(msg, nodes, msgContent);
        }
    }
}


//----------------------------------handle msg info --------------------------------------------------------------------------

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
        if(msg.fromUserId == token) {
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
function handleSetItemError(error)
{
    if( e.name.toUpperCase().indexOf('QUOTA') >= 0) {
        console.log("error ==" + error.message);
    }
}

var isPingSendNum = 0;
var pingIntervalId = false;


function pingFunc() {
    ++isPingSendNum;
    var action = 'im.cts.ping';
    handleImSendRequest(action, "", handlePingFunc);
}

function handlePingFunc() {
    isPingSendNum = 0;
}

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
    handleImSendRequest(action, reqData, handleSyncMsgForRoom);
}

function handleSyncMsgForRoom(results)
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
        setDocumentTitle();
    } else if(msg.chatSessionId  == currentChatSessionId && !isNewMsg) {
        if(msg.type == MessageType.MessageRecall) {
            try{
                var msgId = msg['recall'].msgId;
                var msgContent = "";
                try{
                    msgContent = msg["recall"].msgText !== undefined && msg["recall"].msgText != null ? msg["recall"].msgText : "此消息被撤回";
                }catch (error) {
                    msgContent = "此消息被撤回";
                }
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
        if(msg.chatSessionId != token) {
            setRoomMsgUnreadNum(msg.chatSessionId);
            localStorage.setItem(newSiteTipKey, "new_msg");
            setDocumentTitle();
        }
    }
    appendOrInsertRoomList(msg, true, true);
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

function setRoomMsgUnreadNum(chatSessionId)
{
    var mute = localStorage.getItem(msgMuteKey+chatSessionId);
    if(mute == 1) {
        var  unreadMuteKey = msgUnReadMuteKey+chatSessionId;
        if(!localStorage.getItem(unreadMuteKey)) {
            var unReadAllMuteNum =  !localStorage.getItem(roomListMsgMuteUnReadNumKey) ? 1 : (Number(localStorage.getItem(roomListMsgMuteUnReadNumKey))+1);
            localStorage.setItem(roomListMsgMuteUnReadNumKey, unReadAllMuteNum);
        }
        localStorage.setItem(unreadMuteKey, 1);
    }else {
        var unreadKey = roomMsgUnReadNum + chatSessionId;
        var unreadNum = !localStorage.getItem(unreadKey) ? 1 : (Number(localStorage.getItem(unreadKey))+1);
        localStorage.setItem(unreadKey, unreadNum);

        var unReadAllNum = !localStorage.getItem(roomListMsgUnReadNum) ? 1 : (Number(localStorage.getItem(roomListMsgUnReadNum))+1);
        localStorage.setItem(roomListMsgUnReadNum, unReadAllNum);
    }
    displayRoomListMsgUnReadNum();
}

function getRoomMsgUnreadNum(chatSessionId)
{
    var unreadKey = roomMsgUnReadNum + chatSessionId;
    var unreadNum = !localStorage.getItem(unreadKey) ? 0 : Number(localStorage.getItem(unreadKey));
    unreadNum = unreadNum > 99 ? "99+" : unreadNum;
    return unreadNum;
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


function updateMsgPointer(reqData)
{
    var action = "im.cts.updatePointer";
    handleClientSendRequest(action, reqData, "");
}

function getMsgFromRoom(chatSessionId)
{
    clearRoomUnreadMsgNum(chatSessionId);
    var msgList = handleMsgForMsgRoom(chatSessionId, undefined);

    $(".right-chatbox").html("");
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
    addActiveForRoomList(jqElement);
    msgBoxScrollToBottom();
}

function clearRoomUnreadMsgNum(chatSessionId)
{
    var roomMuteKey = msgUnReadMuteKey+chatSessionId;
    localStorage.removeItem(roomMuteKey);
    var roomMuteNum = localStorage.getItem(roomListMsgMuteUnReadNumKey) ? Number(localStorage.getItem(roomListMsgMuteUnReadNumKey)) : 0;
    roomMuteNum = (roomMuteNum-1)>0 ? (roomMuteNum-1) : 0;
    localStorage.setItem(roomListMsgMuteUnReadNumKey, roomMuteNum);

    var unreadKey = roomMsgUnReadNum + chatSessionId;
    var unReadNum = Number(localStorage.getItem(unreadKey)) ?  Number(localStorage.getItem(unreadKey)) : 0 ;
    var roomListUnreadNum = localStorage.getItem(roomListMsgUnReadNum);
    roomListUnreadNum =  (roomListUnreadNum-unReadNum) >0 ? (roomListUnreadNum-unReadNum) : 0;
    roomListUnreadNum =  (roomListUnreadNum-unReadNum) >99 ? "99+": roomListUnreadNum;
    if(roomListUnreadNum == 0) {
        localStorage.setItem(newSiteTipKey, "clear");
    }
    localStorage.setItem(roomListMsgUnReadNum,roomListUnreadNum);
    localStorage.removeItem(unreadKey);

    if($(".room-chatsession-unread_"+chatSessionId)[0]) {
        $(".room-chatsession-unread_"+chatSessionId)[0].style.display = "none";
        $(".room-chatsession-mute-num_"+chatSessionId+"")[0].style.display = "none";
    }
    setDocumentTitle();
}

function compare(msg1, msg2) {
    if (msg1.timeServer < msg2.timeServer)
        return -1;
    if (msg1.timeServer > msg2.timeServer)
        return 1;
    return 0;
}

function getMsgTimeByMsg(time)
{
    time = Number(time);
    var date = new Date(time); //获取一个时间对象

    var minutes =  date.getMinutes()>=10 ? date.getMinutes():"0"+date.getMinutes();
    var month = date.getMonth() >= 9 ? (date.getMonth()+1) : "0"+ (date.getMonth()+1);

    return date.getFullYear() + '-' + month + '-' +date.getDate() + " " + date.getHours()+":"+minutes;  // 获取完整的年份(4位,1970)
}

function getRoomMsgTime(time)
{
    time = Number(time);
    var date = new Date(time); //获取一个时间对象
    var minutes =  date.getMinutes()>=10 ? date.getMinutes():"0"+date.getMinutes();
    return date.getHours()+":"+minutes;
}

//---------------------------------------------send msg-------------------------------------------------


function isCheckEndMsgDialog()
{
    var rightchatBox = $(".right-chatbox")[0];
    var sh = rightchatBox.scrollHeight;
    var ch = rightchatBox.clientHeight;
    var st = $(".right-chatbox").scrollTop();
    ///差值小于等于Math.ceil(ch*2) px末， 默认底部
    if(sh - ch - st <= Math.ceil(ch*2)) {
        return true
    }
    return false;
}

function msgBoxScrollToBottom()
{
    var rightchatBox = $(".right-chatbox")[0];
    var sh = rightchatBox.scrollHeight;
    var ch  = rightchatBox.clientHeight;
    var scrollTop = sh-ch;
    $(".right-chatbox").scrollTop(scrollTop);

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

    var node = $(".chat_dession_id_"+chatSessionId);
    sortRoomList(node);

    ///在上部分查看消息的时候不滚动
    msgBoxScrollToBottom();
}

function sendMsg( chatSessionId, chatSessionType, msgContent, msgType, params)
{
    var action = "im.cts.message";
    var msgId  = Date.now();

    var message = {};
    message['fromUserId'] = token;
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
    appendOrInsertRoomList(message, true, false);
    handleMsgForMsgRoom(chatSessionId, message);
    addMsgToChatDialog(chatSessionId, message);
};


function sendRecallMsg(recallMsgId, msgText, chatSessionId, chatSessionType)
{
    var action = "im.cts.message";
    var msgId  = Date.now();

    var message = {};
    message['fromUserId'] = token;
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

    message['recall'] = {msgId:recallMsgId, msgText:msgText};
    message['type'] = MessageType.MessageRecall;

    var reqData = {
        "message" : message
    };
    var msgIdInChatSession = msgIdInChatSessionKey + msgId;
    sessionStorage.setItem(msgIdInChatSession, chatSessionId);

    handleImSendRequest(action, reqData, handleSendRecallMsgResponse);
}

function handleSendRecallMsgResponse ()
{
    isSyncingMsg = false;
    syncMsgForRoom();
}

//---------------------------------------------msg realtion function-------------------------------------------------

//get msg  document size
function getMessageDocumentSize(size)
{
    if(Number(size) < 1024) {
        size = size+" bytes";
    } else if(Number(size)>=1024&&Number(size)<Number(1024*1024)) {
        size = Math.ceil(size/1024)+" KB";
    }  else if(Number(size)>=Number(1024*1024) && Number(size)<Number(1024*1024*1024)) {
        size = Math.ceil(size/(1024*1024))+" M";
    } else {
        size = Math.ceil(size/(1024*1024*1024))+" G";
    }
    return size;
}

//get msg  document name
function getMessageDocumentName(name)
{
    if(name.length>15) {
        var names = name.split('.');
        var ext = names.pop();
        var extLength = ext.length;
        var prefix = names.shift();
        var num = (15-extLength-3)/2;
        prefix = prefix.substr(0, num) + "..." + prefix.substr(prefix.length-num, prefix.length);
        name = prefix+"."+ext;
    }
    return name;
}

//get webMsg size
function getWebMessageSize(imageNaturalHeight, imageNaturalWidth, h, w)
{
    var webObject = {};
    if (imageNaturalWidth < w && imageNaturalHeight<h) {
        webObject.width  = imageNaturalWidth == 0 ? w : imageNaturalWidth;
        webObject.height = imageNaturalHeight == 0 ? h : imageNaturalHeight;
    } else {
        if (w / h <= imageNaturalWidth/ imageNaturalHeight) {
            webObject.width  = w;
            webObject.height = w* (imageNaturalHeight / imageNaturalWidth);
        } else {
            webObject.width  = h * (imageNaturalWidth / imageNaturalHeight);
            webObject.height = h;
        }
    }
    return webObject;
}

function getMsgImgSrc(msg)
{
    if(msg.hasOwnProperty("image")) {
        var imgUrlKey = sendMsgImgUrlKey + imgId;
        var src =  localStorage.getItem(imgUrlKey);
        if(!src) {
            var imgId = msg['image'].url;

            var isGroupMessage = msg.roomType == GROUP_MSG ? 1 : 0;
            getMsgImg(imgId, isGroupMessage, msg.msgId);
        } else {
            $(".msg-img-"+msg.msgId).attr("src", src);
        }
        localStorage.removeItem(imgUrlKey);
    }
}

function getMsgSizeForDiv(msg)
{
    var chatType = localStorage.getItem(chatTypeKey);
    var h;
    var w;
    if(chatType != DefaultChat) {
        h = 300;
        w = 200;
    } else {
        h = 400;
        w = 300;
    }
    return getMsgSize(msg['image'].width, msg['image'].height, h, w);
}


function getWebMsgHref(msgId, msgRoomType)
{
    var url = "./index.php?action=http.file.downloadWebMsg&msgId="+msgId+"&isGroupMessage="+(msgRoomType==GROUP_MSG ? 1 : 0);
    return url;
}


function getMsgImageSize(src)
{
    var image = new Image();
    image.src = src;
    image.onload = function (ev) {
        msgImageSize = {
            width:image.width,
            height:image.height
        }
    };
}

function autoMsgImgSize(imgObject, h, w)
{
    var image = new Image();
    image.src = imgObject.src;
    image.onload = function() {

    };
    var imageNaturalWidth  = image.naturalWidth;
    var imageNaturalHeight = image.naturalHeight;

    if (imageNaturalWidth < w && imageNaturalHeight<h) {
        imgObject.width  = imageNaturalWidth == 0 ? w : imageNaturalWidth;
        imgObject.height = imageNaturalHeight == 0 ? h : imageNaturalHeight;
    } else {
        if (w / h <= imageNaturalWidth/ imageNaturalHeight) {
            imgObject.width  = w;
            imgObject.height = w* (imageNaturalHeight / imageNaturalWidth);
        } else {
            imgObject.width  = h * (imageNaturalWidth / imageNaturalHeight);
            imgObject.height = h;
        }
    }
}

function getMsgSize(imageNaturalWidth,imageNaturalHeight, h, w)
{
    var imgObject = {};
    if (imageNaturalWidth < w && imageNaturalHeight<h) {
        imgObject.width  = imageNaturalWidth == 0 ? w : imageNaturalWidth;
        imgObject.height = imageNaturalHeight == 0 ? h : imageNaturalHeight;
    } else {
        if (w / h <= imageNaturalWidth/ imageNaturalHeight) {
            imgObject.width  = w;
            imgObject.height = w* (imageNaturalHeight / imageNaturalWidth);
        } else {
            imgObject.width  = h * (imageNaturalWidth / imageNaturalHeight);
            imgObject.height = h;
        }
    }
    imgObject = {
        width:imgObject.width + "px",
        height:imgObject.height + "px",
    };
    return imgObject;
}


function base64ToBlob(base64, mime)
{
    mime = mime || '';
    var sliceSize = 1024;
    var byteChars = window.atob(base64);
    var byteArrays = [];

    for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
        var endOffset = (offset+sliceSize);
        if(endOffset > byteChars.length) {
            endOffset = byteChars.length;
        }

        var slice = byteChars.slice(offset, endOffset);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    return new Blob(byteArrays, {type: mime});
}

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


//---------------------------------------------append msg html to chat dialog-------------------------------------------------
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
    if( msg.fromUserId != token) {
        sendBySelf = false;
    } else if(msg.fromUserId == token) {
        sendBySelf = true;
        msg.userAvatar = avatar;
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
                    nickname:nickname,
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
            case MessageType.MessageDocument:
                var size = getMessageDocumentSize(msg['document'].size);
                var fileName =  getMessageDocumentName(msg['document'].name);
                var url = msg['document'].url;
                var originName = msg['document'].name;
                html = template("tpl-send-msg-file", {
                    roomType: msg.roomType,
                    nickname:nickname,
                    msgId : msgId,
                    url:url,
                    msgTime : msgTime,
                    msgStatus:msgStatus,
                    avatar:userAvatar,
                    userId:msg.fromUserId,
                    fileSize:size,
                    fileName:fileName,
                    originName:originName,
                    msgType:msgType,
                    timeServer:msg.timeServer
                });
                break;
            case MessageType.MessageImage :
                var imgObject = getMsgSizeForDiv(msg);
                var imgId = msg['image'].url;
                var msgImgUrl = downloadFileUrl +  "&fileId="+imgId + "&returnBase64=0&lang="+languageNum;
                html = template("tpl-send-msg-img", {
                    roomType: msg.roomType,
                    nickname:nickname,
                    msgId : msgId,
                    msgTime : msgTime,
                    msgStatus:msgStatus,
                    avatar:userAvatar,
                    width:imgObject.width,
                    height:imgObject.height,
                    userId:msg.fromUserId,
                    timeServer:msg.timeServer,
                    msgImgUrl:msgImgUrl,
                    msgType:msgType,
                });
                break;
            case MessageType.MessageAudio:
                html = template("tpl-send-msg-audio", {
                    roomType: msg.roomType,
                    nickname:nickname,
                    msgId : msgId,
                    msgTime : msgTime,
                    msgStatus:msgStatus,
                    avatar:userAvatar,
                    userId:msg.fromUserId,
                    msgType:msgType,
                    timeServer:msg.timeServer
                });
            case MessageType.MessageWebNotice:
                var hrefUrl = getWebMsgHref(msg.msgId, msg.roomType);
                html = template("tpl-receive-msg-web-notice", {
                    hrefUrl:hrefUrl
                });
                break;
            case MessageType.MessageWeb :
                var linkUrl = getWebMsgHref(msg.msgId, msg.roomType);
                var hrefUrl =  msg['web'].hrefURL;
                var webSize = getWebMessageSize(msg['web'].height, msg['web'].width, 300, 400);
                html = template("tpl-send-msg-web", {
                    roomType: msg.roomType,
                    nickname: nickname,
                    webWidth:webSize.width,
                    webHeight:webSize.height,
                    msgId : msgId,
                    msgTime : msgTime,
                    groupUserImg : groupUserImageClassName,
                    avatar:userAvatar,
                    hrefURL:hrefUrl,
                    linkUrl :linkUrl,
                    userId:msg.fromUserId,
                    timeServer:msg.timeServer,
                    msgType:msgType,
                });
                break;
            case MessageType.MessageRecall:
                try{
                    var msgContent = msg["recall"].msgText !== undefined && msg["recall"].msgText != null ? msg["recall"].msgText : "此消息被撤回";
                }catch (error) {
                    var msgContent = "此消息被撤回";
                }
                html = template("tpl-receive-msg-notice", {
                    msgContent:msgContent,
                    timeServer:msg.timeServer
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
                    nickname:nickname,
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
        var isMaster = isJudgeSiteMasters(msg.fromUserId);
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
            case MessageType.MessageImage :
                var imgObject = getMsgSizeForDiv(msg);
                var imgId = msg['image'].url;
                var isGroupMessage = msg.roomType == GROUP_MSG ? 1 : 0;
                var msgImgUrl = downloadFileUrl +  "&fileId="+imgId + "&returnBase64=0&isGroupMessage="+isGroupMessage+"&messageId="+msgId+"&lang="+languageNum;

                html = template("tpl-receive-msg-img", {
                    roomType: msg.roomType,
                    nickname: msg.nickname,
                    msgId : msgId,
                    msgTime : msgTime,
                    userId :msg.fromUserId,
                    groupUserImg : groupUserImageClassName,
                    avatar:userAvatar,
                    width:imgObject.width,
                    height:imgObject.height,
                    msgImgUrl:msgImgUrl,
                    msgType:msgType,
                    isMaster:isMaster
                });
                break;
            case MessageType.MessageAudio:
                html = template("tpl-receive-msg-audio", {
                    roomType: msg.roomType,
                    nickname: msg.nickname,
                    msgId : msgId,
                    userId :msg.fromUserId,
                    msgTime : msgTime,
                    groupUserImg : groupUserImageClassName,
                    avatar:userAvatar,
                    isMaster:isMaster
                });
                break;
            case MessageType.MessageDocument:
                var size = getMessageDocumentSize(msg['document'].size);
                var fileName =  getMessageDocumentName(msg['document'].name);
                var url = msg['document'].url;
                var originName = msg['document'].name;
                html = template("tpl-receive-msg-file", {
                    roomType: msg.roomType,
                    nickname:msg.nickname,
                    msgId : msgId,
                    url:url,
                    msgTime : msgTime,
                    msgStatus:msgStatus,
                    avatar:userAvatar,
                    userId:msg.fromUserId,
                    fileSize:size,
                    fileName:fileName,
                    originName:originName,
                    timeServer:msg.timeServer,
                    msgType:msgType,
                    isMaster:isMaster
                });
                break;
            case MessageType.MessageWebNotice :
                var hrefUrl = getWebMsgHref(msg.msgId, msg.roomType);
                html = template("tpl-receive-msg-web-notice", {
                    hrefUrl:hrefUrl
                });
                break;
            case MessageType.MessageWeb :
                var linkUrl = getWebMsgHref(msg.msgId, msg.roomType);
                var hrefUrl =  msg['web'].hrefURL;
                var webSize = getWebMessageSize(msg['web'].height, msg['web'].width, 300, 400);
                html = template("tpl-receive-msg-web", {
                    roomType: msg.roomType,
                    nickname: msg.nickname,
                    msgId : msgId,
                    msgTime : msgTime,
                    webWidth:webSize.width,
                    webHeight:webSize.height,
                    leftWebWidth:Number(webSize.width+25),
                    userId :msg.fromUserId,
                    groupUserImg : groupUserImageClassName,
                    avatar:userAvatar,
                    hrefURL:hrefUrl,
                    linkUrl:linkUrl,
                    isMaster:isMaster,
                    msgType:msgType,
                });
                break;
            case MessageType.MessageNotice:
                var msgContent = msg["notice"].body;
                html = template("tpl-receive-msg-notice", {
                    msgContent:msgContent,
                });
                break;
            case MessageType.MessageRecall:
                try{
                    var msgContent = msg["recall"].msgText !== undefined && msg["recall"].msgText != null ? msg["recall"].msgText : "此消息被撤回";
                }catch (error) {
                    var msgContent = "此消息被撤回";
                }
                html = template("tpl-receive-msg-notice", {
                    msgContent:msgContent,
                    timeServer:msg.timeServer
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
        $(".right-chatbox[chat-session-id="+msg.chatSessionId+"]").append(html);
    }
}

//---------------------------------------------upload file -------------------------------------------------
function uploadMsgFileFromInput(obj, fileType) {

    if (obj) {
        if (obj.files) {
            formData = new FormData();
            formData.append("file", obj.files.item(0));
            formData.append("fileType", fileType);
            formData.append("isMessageAttachment",true);
            var src = window.URL.createObjectURL(obj.files.item(0));
            if(fileType == FileType.FileDocument) {
                var params = {
                    size:obj.files.item(0).size,
                    name:obj.files.item(0).name
                };
                uploadMsgFileToServer(formData, src, uploadFileForMsg, params);
            } else if(fileType == FileType.FileImage) {
                getMsgImageSize(src);
                uploadMsgFileToServer(formData, src, uploadImgForMsg, "");
            }

            return window.URL.createObjectURL(obj.files.item(0));
        }
        return obj.value;
    }
}

function uploadMsgImgFromCopy(image)
{
    var base64ImageContent = image.replace(/^data:image\/(png|jpg);base64,/, "");
    var blob = base64ToBlob(base64ImageContent, 'image/png');
    var formData = new FormData();

    formData.append("file", blob);
    formData.append("fileType", FileType.FileImage);
    formData.append("isMessageAttachment",true);
    var src = window.URL.createObjectURL(blob);
    getMsgImageSize(src);
    uploadMsgFileToServer(formData, src, uploadImgForMsg, "");
}


function uploadMsgFileToServer(formData, src, type, params)
{
    var chatSessionId = localStorage.getItem(chatSessionIdKey);
    var chatSessionType = localStorage.getItem(chatSessionId);

    $.ajax({
        url:uploadFileUrl,
        type:"post",
        data:formData,
        contentType:false,
        processData:false,
        success:function(fileInfo){
            var fileInfo = JSON.parse(fileInfo);
            var fileName = fileInfo['fileId'];
            var errorInfo = fileInfo['errorInfo'];

            if(fileName) {
                if(fileName == "failed") {
                    alert("发送失败,稍后重试");
                    return false;
                }
                if(type == uploadImgForMsg) {
                    // alert("上传成功！");
                    var imgKey = sendMsgImgUrlKey+fileName;
                    localStorage.setItem(imgKey, src);
                    sendMsg(chatSessionId, chatSessionType, fileName, MessageType.MessageImage, "");
                    $("#msgImage").html("");
                    $("#msgImage")[0].style.display = "none";
                } else if(type == uploadImgForSelfAvatar) {
                    updateUserAvatar(fileName);
                }else if(type == uploadFileForMsg) {
                    sendMsg(chatSessionId, chatSessionType, fileName, MessageType.MessageDocument, params);
                }
            } else {
                alert(errorInfo);
            }
        },
        error:function(err){
            alert("发送失败,稍后重试");
            return false;
        }
    });
}

function uploadUserImgFromInput(obj) {
    if (obj) {
        if (obj.files) {
            formData = new FormData();

            formData.append("file", obj.files.item(0));
            formData.append("fileType", FileType.FileImage);
            formData.append("isMessageAttachment", false);

            var src = window.URL.createObjectURL(obj.files.item(0));
            getMsgImageSize(src);
            uploadMsgFileToServer(formData, src, uploadImgForSelfAvatar, "");

            $(".user-image-upload").attr("src", src);
        }
        return obj.value;
    }
}

function updateUserAvatar(fileName)
{
    var values = new Array();
    var value = {
        type : ApiUserUpdateType.ApiUserUpdateAvatar,
        avatar : fileName,
    };
    values.push(value);
    updateUserInfo(values);
}


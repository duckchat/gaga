
$(".left-body-chatsession").html("");
$(".right-chatbox").html("");

function showMsgWebNotification(msg, msgContent)
{

    var msgId = msg.msgId;
    var nickname="";
    var name='';
    var notification;

    if(msg.roomType == "MessageRoomGroup") {
        name = msg.name;
        nickname=msg.nickname;
    } else {
         name=msg.nickname;
    }

    if(name == undefined || name.length<1) {
        name = "通知";
    }

    if(nickname == "") {
         notification = "["+name+"] "+ msgContent;
    } else {
         notification = "["+name+"] "+nickname+":" + msgContent;
    }
    var chatSessionId = msg.chatSessionId;
    var muteKey = msgMuteKey + chatSessionId;
    var mute = localStorage.getItem(muteKey);
    var icon =  $(".info-avatar-"+msg.chatSessionId).attr("src");

    var siteConfigStr = localStorage.getItem(siteConfigKey);
    var siteConfig = JSON.parse(siteConfigStr);

    if(msg.roomType == U2_MSG) {
        icon  =  downloadFileUrl + "&fileId="+msg.userAvatar+"&returnBase64=0&lang="+languageNum;
    } else {
        icon  =  downloadFileUrl + "&fileId="+msg.avatar+"&returnBase64=0&lang="+languageNum;
    }

    if(document.hidden && (mute == 0)) {
        if(window.Notification && Notification.permission !== "denied"){
            var notification = new Notification(notification, {
                "tag":siteConfig.serverAddressForApi,
                "icon":icon,
                "renotify": true,
            });
            notification.onclick = function(event) {
                window.focus();
            }
        }
    }
}


function showOtherWebNotification()
{
    if(document.hidden) {
        var siteConfigStr = localStorage.getItem(siteConfigKey);
        var siteConfig = JSON.parse(siteConfigStr);
        var icon = siteConfig.logo == undefined ? "" : siteConfig.logo;
        icon = downloadFileUrl + "&fileId=" + icon + "&returnBase64=0&lang=" + languageNum;
        var notification = languageNum == UserClientLangZH ? "新的好友请求" : "new friend apply request";
        if (window.Notification && Notification.permission !== "denied") {
            var notification = new Notification(notification, {"tag": siteConfig.serverAddressForApi, "icon": icon});
            notification.onclick = function (event) {
                window.focus();
            }
        }
    }
}

function displayFrontPage()
{
    try{
        var isDisplayFrontPage = localStorage.getItem(isDisplayFrontPageKey);

        if(isDisplayFrontPage != "is_display") {
            localStorage.setItem(isDisplayFrontPageKey, "is_display");
            var configStr = localStorage.getItem(siteConfigKey);
            var config = JSON.parse(configStr);
            if(config.hasOwnProperty("hiddenHomePage") && config['hiddenHomePage'] == true) {
                var isMaster = isJudgeSiteMasters(token);
                if(!isMaster) {
                    $(".l-sb-item[data='home']")[0].style.display="none";
                }
            } else {
                //1:Home 2:Chats 3:Contacts friend 4:Me
                if(config.hasOwnProperty('frontPage')) {
                    var frontPage = config['frontPage'];
                    switch (frontPage) {
                        case "FrontPageChats":
                            $(".l-sb-item[data='chatSession']").click();
                            break;
                        case "FrontPageContacts":
                            $(".l-sb-item[data='friend']").click();
                            break;
                        default:
                            $(".l-sb-item[data='home']").click();
                    }
                } else {
                    $(".l-sb-item[data='home']").click();
                }
                $(".l-sb-item[data='home']")[0].style.display="flex";
            }
        }

    }catch (error){
        $(".l-sb-item[data='chatSession']").click();
    }

    jump();
}



isSelfInfoCanHidden = true;

//点击触发一个对象的点击
function uploadFile(obj, type)
{

    if(type == 'user_avatar') {
        isSelfInfoCanHidden = false
    }

    $("#"+obj).val("");
    $("#"+obj).click();
}



function getSelfInfoByClassName()
{
    token = $('.token').attr("data");
    nickname = $(".nickname").attr("data");
    loginName=$(".loginName").attr("data");
    avatar = $(".self_avatar").attr("data");
    jumpPage = $(".jumpPage").attr("data");
    jumpRoomType = $(".jumpRoomType").attr("data");
    jumpRoomId = $(".jumpRoomId").attr("data");
    jumpRelation = $(".jumpRelation").attr("data");
}

getSelfInfoByClassName();

function jump()
{
    //群，好友
    // http://127.0.0.1/index.php?page=u2Msg&x=
    // http://127.0.0.1/index.php?page=groupMsg&x=
    getRoomList();
    if(jumpRoomType != "" && jumpRoomId != "") {
        if(jumpRoomType == JUMP_GroupMsg ) {
            if(jumpRelation == 1) {
                localStorage.setItem(chatSessionIdKey, jumpRoomId);
                localStorage.setItem(jumpRoomId, GROUP_MSG);
                handleClickRowGroupProfile(jumpRoomId);
            }
        } else if(jumpRoomType == JUMP_U2Msg || jumpRoomType == JUMP_U2Profile) {
            localStorage.setItem(chatSessionIdKey, jumpRoomId);
            localStorage.setItem(jumpRoomId, U2_MSG);
            sendFriendProfileReq(jumpRoomId, handleGetJumpFriendProfile);
        }
    }
}

function handleGetJumpFriendProfile(results)
{
    handleGetFriendProfile(results);

    if(results == undefined) {
        return;
    }
    var profile = results.profile;

    if(profile != undefined && profile["profile"]) {
        insertU2Room(undefined, jumpRoomId);
    }

}

//display unread msg
function displayRoomListMsgUnReadNum()
{

    var data = $(".l-sb-item-active").attr("data");
    if(data != "chatSession") {
        var unReadAllNum = localStorage.getItem(roomListMsgUnReadNum);
        if(unReadAllNum>0) {
            if(unReadAllNum>99) {
                unReadAllNum = "99+";
            }
            localStorage.setItem(newSiteTipKey, "new_msg");
            setDocumentTitle();
            $(".unread-num-mute")[0].style.display = "none";
            $(".room-list-msg-unread")[0].style.display = 'block';
            $(".room-list-msg-unread").html(unReadAllNum);
        } else {
            localStorage.setItem(newSiteTipKey, "clear");
            setDocumentTitle();
            $(".room-list-msg-unread")[0].style.display = 'none';
            var mute = localStorage.getItem(roomListMsgMuteUnReadNumKey);
            if(mute >= 1) {
                $(".unread-num-mute")[0].style.display = "block";
            }
        }
    } else {
        $(".room-list-msg-unread")[0].style.display = 'none';
        $(".unread-num-mute")[0].style.display = "none";
    }
    if(data == "friend") {
        $(".apply_friend_list_num")[0].style.display = "none";
    } else {
        $(".apply_friend_list_num")[0].style.display = "block";
    }

    var friendListNum = localStorage.getItem(applyFriendListNumKey);

    if(friendListNum > 0 && friendListNum != undefined && data != "friend" ) {
        localStorage.setItem(newSiteTipKey, "add_friend");
        setDocumentTitle();
        $(".apply_friend_list_num")[0].style.display = "block";
    } else {
        $(".apply_friend_list_num")[0].style.display = "none";
    }
}

function isJudgeSiteMasters(userId)
{
    try{
        var siteConfigJson = localStorage.getItem("site_config");
        var siteConfig = JSON.parse(siteConfigJson);
        var mastersStr = siteConfig.masters;
        if(mastersStr.indexOf(userId) != -1) {
            return true;
        }
        return false;
    }catch (error) {
        return false;
    }
}


groupOffset = 0;
getGroupList(initGroupList);

friendOffset = 0;
getFriendList(initFriendList);


$(document).on("click", ".l-sb-item", function(){
    $(".search-friend-group-lists")[0].style.display="none";
    $(".search_for_group_friend").val("");

    var currentActive = $(".left-sidebar").find(".l-sb-item-active");
    $(currentActive).removeClass("l-sb-item-active");
    $(this).addClass("l-sb-item-active");

    var dataType  = $(this).attr("data");
    var selectClassName   = dataType + "-select";
    var unselectClassName = dataType + "-unselect";

    var itemImgs = $(".left-sidebar").find(".item-img");
    var length = itemImgs.length;
    for(i=0; i<length; i++) {
        var item = itemImgs[i];
        var data = $(item).attr("data");
        if(data == "select") {
            $(item)[0].style.display = "none";
        } else {
            $(item)[0].style.display = "block";
        }
    }
    if($("."+unselectClassName)[0]) {
        $("."+unselectClassName)[0].style.display = "none";
        $("."+selectClassName)[0].style.display = "block";
    }
    $(".left-body-item[default='1']").attr("default", 0);

    switch (dataType){
        case "home":
            $(".home-page")[0].style.display = "block";
            $(".group-lists")[0].style.display = "none";
            $(".chatsession-lists")[0].style.display = "none";
            $(".friend-lists")[0].style.display = "none";
            $(".home-page").attr("default", 1);
            pluginOffset = 0;
            getPluginList(pluginOffset, PluginUsageType.PluginUsageIndex, initPluginList);
            displayRightPage(DISPLAY_HOME);
            break;
        case "group":
            $(".home-page")[0].style.display = "none";
            $(".group-lists")[0].style.display = "block";
            $(".chatsession-lists")[0].style.display = "none";
            $(".friend-lists")[0].style.display = "none";
            $(".group-lists").attr("default", 1);
            groupOffset = 0;
            getGroupList(initGroupList);

            break;
        case "chatSession" :
            getRoomList();
            $(".home-page")[0].style.display = "none";
            $(".chatsession-lists")[0].style.display = "block";
            $(".group-lists")[0].style.display = "none";
            $(".friend-lists")[0].style.display = "none";
            $(".chatsession-lists").attr("default", 1);
            displayRightPage(DISPLAY_CHAT);
            break;
        case "friend":
            $(".home-page")[0].style.display = "none";
            $(".friend-lists")[0].style.display = "block";
            $(".chatsession-lists")[0].style.display = "none";
            $(".group-lists")[0].style.display = "none";
            $(".friend-lists").attr("default", 1);

            friendOffset = 0;
            getFriendList(initFriendList);

            break;
        case "more":
            displayDownloadApp();
            break;
    }
    displayRoomListMsgUnReadNum();
});

window.onresize = function(){
    try{
        if ($(".right-head")[0].clientWidth<680) {
            $(".right-body-sidebar").hide();
        }
    }catch (error) {
        // console.log(error.message);
    }
}



function handleSendFriendApplyReq()
{
    alert("已经发送好友请求");
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

function searchGroupAndFriendByKeyDown(event)
{
    if(!checkIsEnterBack(event)) {
        return;
    }
    var searchVal = $(".search_for_group_friend").val();

    $(".search_display_friend").html("");
    $(".search_display_group").html("");

    $(".search-group-div")[0].style.display = "block";
    $(".search-friend-div")[0].style.display = "block";


    var friendListRow = $(".friend-list-contact-row .contact-row-u2-profile");
    var friendListRowLength = friendListRow.length;
    var currentFriendCount = 0;
    $(".hide_all_friend")[0].style.display = "none";
    $(".search_hidden_friend")[0].style.display = "none";
    $(".display_all_friend")[0].style.display = "none";

    for(var i=0; i< friendListRowLength; i++) {
        var friendRow = friendListRow[i];
        var newFriendRow = friendRow.cloneNode(true);
        var friendName = $(friendRow).attr("friend-name");
        var friendNameLatin = $(friendRow).attr("friend-name-latin");
        try{
            if(friendName.indexOf(searchVal)!=-1 || friendNameLatin.indexOf(searchVal) != -1) {
                currentFriendCount +=1;
                if(currentFriendCount > 2) {
                    $(".display_all_friend")[0].style.display = "flex";
                    $(".search_hidden_friend").append($(newFriendRow));
                } else {
                    $(".search_display_friend").append($(newFriendRow));
                }
            }
        }catch (error) {

        }
    }

    if(currentFriendCount > 2) {
        var currentFriendCountStr = "("+currentFriendCount+")";
        $(".search_friend_count").html(currentFriendCountStr);
    }

    $(".hide_all_group")[0].style.display = "none";
    $(".search_hidden_group")[0].style.display = "none";
    $(".display_all_group")[0].style.display = "none";

    var groupListRow = $(".contact-row-group-profile");
    var groupListRowLength = groupListRow.length;
    var currentGroupCount = 0;
    for(var i=0; i< groupListRowLength; i++) {
        var groupRow = groupListRow[i];
        var newGroupRow = groupRow.cloneNode(true);
        var groupName = $(groupRow).attr("group-name");
        var groupNameLatin = $(groupRow).attr("group-name-latin");

        try{
            if(groupName.indexOf(searchVal)!=-1 || groupNameLatin.indexOf(searchVal) != -1) {
                currentGroupCount +=1;
                if(currentGroupCount > 2) {
                    $(".display_all_group")[0].style.display = "flex";
                    $(".search_hidden_group").append($(newGroupRow));
                } else {
                    $(".search_display_group").append($(newGroupRow));
                }
            }
        }catch (error) {

        }
    }
    if(currentGroupCount > 2) {
        var currentGroupCountStr = "("+currentGroupCount+")";
        $(".search_group_count").html(currentGroupCountStr);
    }

    $(".left-body-item[default='1']")[0].style.display = "none";
    $(".left-body-item[default='1']").attr("default", 0)

    $(".search-friend-group-lists")[0].style.display = "block";
    $(".search-friend-group-lists").attr("default", 1);

}

$(document).on("click", ".display_all_friend", function () {
    $(".search_hidden_friend")[0].style.display = "block";
    $(".display_all_friend")[0].style.display = "none";
    $(".hide_all_friend")[0].style.display = "flex";
    var clientHeight = $(".search-friend-group-lists")[0].clientHeight - $(".friend-list-div")[0].clientHeight;
    $(".search-friend-group-lists-div")[0].style.height = clientHeight+"px";
});

$(document).on("click", ".hide_all_friend", function () {
    $(".display_all_friend")[0].style.display = "flex";
    $(".hide_all_friend")[0].style.display = "none";
    $(".search_hidden_friend")[0].style.display = "none";
});


$(document).on("click", ".display_all_group", function () {
    $(".search_hidden_group")[0].style.display = "block";
    $(".display_all_group")[0].style.display = "none";
    $(".hide_all_group")[0].style.display = "flex";
    var clientHeight = $(".search-friend-group-lists")[0].clientHeight - $(".group-list-div")[0].clientHeight;
    $(".search-friend-group-lists-div")[0].style.height = clientHeight+"px";
});

$(document).on("click", ".hide_all_group", function () {
    $(".display_all_group")[0].style.display = "flex";
    $(".hide_all_group")[0].style.display = "none";
    $(".search_hidden_group")[0].style.display = "none";
});

function deleteSearchInfo()
{
    $(".search_for_group_friend").val('');
    $(".search-group-div")[0].style.display = "none";
    $(".search-friend-div")[0].style.display = "none";

}

$(document).on("input onpropertychange", ".search_for_group_friend", function () {
    var value = $(".search_for_group_friend").val();
    if(!value ||  value.length<1) {
        deleteSearchInfo();
    }
});


//-------------------------------------------api.plugin.list------------------------------------------------
/// plugin operation - api.plugin.list
function getPluginList(offset, type, callback)
{
    var action = "api.plugin.list";
    var reqData = {
        "offset" : offset,
        "count"  : defaultCountKey,
        "usageType":type
    }
    handleClientSendRequest(action, reqData, callback);
}

function handlePluginListHtml(results)
{
    if(results.hasOwnProperty("list") && results.list) {
        var list = results.list;
        var listLength = list.length;
        for(var i=0;i<listLength;i++) {
            var plugin = list[i];
            var logo = false;
            var displayPlugin = localStorage.getItem(defaultPluginDisplay);
            if(!displayPlugin || displayPlugin == null) {
                localStorage.setItem(defaultPluginDisplay,plugin.id);
            }
            if(plugin.hasOwnProperty("logo")){
                logo = getNotMsgImgUrl(plugin.logo);
            }
            var loadingType = PluginLoadingType.PluginLoadingNewPage;
            if(plugin.hasOwnProperty("loadingType")) {
                loadingType = plugin.loadingType
            }
            var html = template("tpl-plugin-item", {
                id:plugin.id,
                name:plugin.name,
                landingPageUrl:plugin.landingPageUrl,
                duckchatSessionId:plugin.userSessionId,
                logo:logo,
                loadingType:loadingType,
                siteAddress:siteAddress
            });
            $(".mini-program-row").append(html);
        }
    }
}

function initPluginList(results)
{
    $(".mini-program-row").html("");
    handlePluginListHtml(results);
    displayPlugin();
}

function displayPlugin()
{
    var pluginId =  localStorage.getItem(defaultPluginDisplay);
    $(".plugin-info[plugin-id='"+pluginId+"']").click();
}

$(document).on("click", ".plugin-info", function () {
    var landingPageUrl = $(this).attr("plugin-landingPageUrl");
    var name = $(this).attr("plugin-name");
    var duckchatSessionId = $(this).attr("plugin-duckchatSessionId");
    addActiveForPwContactRow($(this));
    displayRightPage(DISPLAY_HOME);
    if(landingPageUrl.indexOf("?") > -1) {
        landingPageUrl = landingPageUrl+"&duckchat_sessionid="+duckchatSessionId;
    } else {
        landingPageUrl = landingPageUrl+"?duckchat_sessionid="+duckchatSessionId;
    }
    var clientHeight = $(".plugin-list-dialog")[0].clientHeight - $(".plugin-head")[0].clientHeight;
    $(".plugin-right-body")[0].style.height = clientHeight+"px";

    var pluginId = $(this).attr("plugin-id");
    localStorage.setItem(defaultPluginDisplay, pluginId);
    $(".title").html(name);
    var pluginId = "plugin_id_"+pluginId;
    $(".plugin-src").attr("src", landingPageUrl);
    $(".plugin-src").attr("id",pluginId);
    setPluginTitle(pluginId);
    $(".open_new_page").attr("landingPageUrl", landingPageUrl);
    deleteCookie("duckchat_page_url");
    setCookie("duckchat_sessionid", duckchatSessionId, 1 );
});


function setPluginTitle(pluginId)
{
    var iframe = document.getElementById(pluginId);
    var pluginSrc = $("#"+pluginId).attr("src");
    try{
        var host = location.host;
        if(location.port) {
            host = host + ":"+location.port;
        }
        if(pluginSrc.indexOf(host) != -1 || (( pluginSrc.indexOf("http") == -1) && ( pluginSrc.indexOf("https") == -1))) {
            iframe.onload = function (ev) {
                var pluginTitle = iframe.contentWindow.document.title;
                $(".plugin-title").html(pluginTitle);
            }
        }
    }catch (error){

    }

}

$(document).on("click", ".open_new_page", function () {
    var landingPageUrl = $(this).attr("landingPageUrl");
    window.open(landingPageUrl, "_blank");
});

function getInitChatPlugin(roomType)
{
    if(roomType == U2_MSG) {
        pluginU2ChatOffset=0;
        getPluginList(pluginU2ChatOffset, PluginUsageType.PluginUsageU2Message, getChatPluginList);
    }else {
        pluginGroupChatOffset=0;
        getPluginList(pluginGroupChatOffset, PluginUsageType.PluginUsageGroupMessage, getChatPluginList);
    }
}

function getChatPluginList(results)
{
    $(".input-plugin-tools").html('');
    if(results.hasOwnProperty("list") ) {
        var list = results.list;
        var lengthList = list.length;
        for(var i=0; i< lengthList;i++) {
            var plugin = list[i];
            var logo = false;
            if(plugin.hasOwnProperty("logo")){
                logo = getNotMsgImgUrl(plugin.logo);
            }
            var loadingType = PluginLoadingType.PluginLoadingNewPage;
            if(plugin.hasOwnProperty("loadingType")) {
                loadingType = plugin.loadingType
            }
            var html = template("tpl-input-tools-item", {
                id:plugin.id,
                name:plugin.name,
                landingPageUrl:plugin.landingPageUrl,
                duckchatSessionId:plugin.userSessionId,
                logo:logo,
                loadingType:loadingType,
            });
            $(".input-plugin-tools").append(html);
        }
    }
}


$(document).on("click", ".chat_plugin", function () {
    var pluginId = $(this).attr("plugin-id");
    var duckchatSessionId = $(this).attr("plugin-duckchatSessionId");
    var loadingType = $(this).attr("plugin-loadingType");
    var landingPageUrl = $(this).attr("plugin-landingPageUrl");
    setCookie("duckchat_sessionid", duckchatSessionId, 30);

    var chatSessionId = localStorage.getItem(chatSessionIdKey);
    var roomType = localStorage.getItem(chatSessionId);
    var url = getPluginDuckchatPageUrl(roomType, chatSessionId);
    setCookie("duckchat_page_url", url, 30);

    if(landingPageUrl.indexOf("?") > -1) {
        landingPageUrl = landingPageUrl+"&duckchat_sessionid="+duckchatSessionId+"&duckchat_page_url="+encodeURIComponent(url);
    } else {
        landingPageUrl = landingPageUrl+"?duckchat_sessionid="+duckchatSessionId+"&duckchat_page_url="+encodeURIComponent(url);
    }

    if(loadingType == PluginLoadingType.PluginLoadingNewPage) {
        window.open(landingPageUrl, "_blank");
    } else {
        $("#chat_plugin")[0].style.display = "block";
        $(".chat_plugin_iframe").attr("src", landingPageUrl);
    }
});



$(document).on("click", ".plugin_back", function () {
    try{
        var pluginId =  $(".plugin-iframe").attr("id");
        $(".plugin-iframe")[0].contentWindow.history.go(-1); // back
        var onReload = false;
       try{
           $(".plugin-iframe")[0].onload = function() {
               if( onReload == false) {
                   $(".plugin-iframe")[0].contentWindow.self.location.href = $(".plugin-iframe")[0].contentWindow.self.location.href;
                   setPluginTitle(pluginId);
                   onReload = true;
               }
           }
       }catch (error){

       }
    }catch (error){
        console.log(error);
    }
});

//--------------------------------------http.file.downloadFile----------------------------------------------

function getNotMsgImgUrl(avatarImgId) {
    if(avatarImgId) {
        return  downloadFileUrl + "&fileId="+avatarImgId+"&returnBase64=0&lang="+languageNum;
    }
    return false;
}

function getNotMsgImg(userId, avatarImgId)
{
    if(avatarImgId == undefined || avatarImgId == "" || avatarImgId.length<1) {
        return false;
    }
    var userImgKey = userId+avatarImgId;
    var isReqTime = sessionStorage.getItem(userImgKey);

    var nowTimeStamp = Date.parse(new Date());
    ////5分钟的过期时间，如果还没有请求回来，下一个请求会继续冲重新请求
    if(isReqTime != false &&  nowTimeStamp-isReqTime<reqTimeout && isReqTime != null) {
        return ;
    }
    sessionStorage.setItem(userImgKey, Date.parse(new Date()));

    var requestUrl =  downloadFileUrl + "&fileId="+avatarImgId+"&returnBase64=0&lang="+languageNum;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && (this.status == 200 || this.status == 304)) {
            var blob = this.response;
            var src = window.URL.createObjectURL(blob);
            // Typical action to be performed when the document is ready:
            var img = new Image();
            img.src = src;
            img.onload = function(){
                $(".info-avatar-"+userId).attr("src", src);
                if(userId == token) {
                    localStorage.setItem(selfInfoAvatar, src);
                }
            }
            img.onerror = function (ev) {
            }
        }
        sessionStorage.removeItem(userImgKey);
    };
    xhttp.open("GET", requestUrl, true);
    xhttp.responseType = "blob";
    xhttp.setRequestHeader('Cache-Control', "max-age=84600, public");
    xhttp.send();
}


function getMsgImg(imgId, isGroupMessage, msgId)
{
    if(imgId == undefined || imgId == "" || imgId.length<1) {
        return false;
    }
    var requestUrl = downloadFileUrl +  "&fileId="+imgId + "&returnBase64=0&isGroupMessage="+isGroupMessage+"&messageId="+msgId+"&lang="+languageNum;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && (this.status == 200 || this.status == 304)) {
            var blob = this.response;
            var src = window.URL.createObjectURL(blob);
            // Typical action to be performed when the document is ready:
            $(".msg-img-"+msgId).attr("src", src);
        }
    };
    xhttp.open("GET", requestUrl, true);
    xhttp.responseType = "blob";
    xhttp.setRequestHeader('Cache-Control', "max-age=2592000, public");
    xhttp.send();
}
///下载自己头像
// getNotMsgImg(token, avatar);

//--------------------------------------site share---------------------------------------------

function changeZalySchemeToDuckChat(chatSessionId, type)
{

    var urlLink = getAddressDomain();
    if(chatSessionId != "") {
        urlLink = urlLink.indexOf("?") > -1 ? urlLink+"&x="+type+"-"+chatSessionId : urlLink+"/?x="+type+"-"+chatSessionId;
    }
    urlLink = jumpPage.indexOf("?") > -1 ? jumpPage+"&jumpUrl="+encodeURI(urlLink) :jumpPage+"?jumpUrl="+encodeURI(urlLink);
    return encodeURI(urlLink);
}

function getAddressDomain()
{
    try{
        var siteConfigJsonStr = localStorage.getItem(siteConfigKey);
        if(siteConfigJsonStr ) {
            siteConfig = JSON.parse(siteConfigJsonStr);
        }
        serverAddress = siteConfig.serverAddressForApi;

        var parser = document.createElement('a');
        parser.href = serverAddress;
        var domain = serverAddress;
        if(parser.protocol == 'zaly:') {
            var protocol = "duckchat:";
            var hostname = parser.hostname;
            var pathname = parser.pathname;
            domain =  protocol+"//"+hostname+pathname;
        }
        return domain;
    }catch (error){

    }
}

function getPluginDuckchatPageUrl(type, x)
{
    var page = ""
    switch (type) {
        case U2_MSG:
            page = "u2Msg";
            break;
        case GROUP_MSG:
            page="groupMsg";
            break;
    }
    var urlLink = getAddressDomain();
    if(urlLink.indexOf("?") > -1) {
        urlLink = urlLink + "&page="+page+"&x="+x;
    } else {
        urlLink = urlLink + "?page="+page+"&x="+x;
    }
    return urlLink;
}


function displayDownloadApp() {
    var html = template("tpl-download-app-div", {});
    html = handleHtmlLanguage(html);
    $("#download-app-div").html(html);
    var urlLink = changeZalySchemeToDuckChat("", "download_app");
    var src = "../../public/img/duckchat.png";
    generateQrcode($('#qrcodeCanvas'), urlLink, src, false, "more");
    showWindow($("#download-app-div"));
}

//--------------------------------------generate qrcode---------------------------------------------

function generateQrcode(qrCodeObj, urlLink, src, isCircle, type)
{
    var idName, className,width,height,canvasWidth,canvasHeight;

    if(type == "self") {
         idName = "selfQrcode";
         className = "selfCanvas";
        width  = getRemPx()*17;
        height = getRemPx()*17;
        canvasWidth = getRemPx()*15;
        canvasHeight = getRemPx()*15;
    } else if(type == 'group') {
         width  = getRemPx()*24.5;
         height = getRemPx()*24.5;
         canvasWidth = getRemPx()*22;
         canvasHeight = getRemPx()*22;
         className = "qrcodeCanvas";
         idName = "groupQrcode";
    } else {
         width  = getRemPx()*24.5;
         height = getRemPx()*24.5;
         canvasWidth = getRemPx()*22;
         canvasHeight = getRemPx()*22;
         idName = "appDownload";
         className = "appDownload";
    }

    qrCodeObj.qrcode({
        idName:idName,
        render : "canvas",
        text    :urlLink,
        className : className,
        canvasWidth:canvasWidth,
        canvasHeight:canvasHeight,
        width : width,               //二维码的宽度
        height : height,              //二维码的高度
        background : "#ffffff",       //二维码的后景色
        foreground : "#000000",        //二维码的前景色
        src: src, //二维码中间的图片
        isCircle:isCircle
    });
}

//download qrcode img
function downloadImgFormQrcode(idName)
{
    var canvas = document.getElementById(idName);
    var image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream"); //Convert image to 'octet-stream' (Just a download, really)
    window.location.href = image;
}

//--------------------------------------set document tile---------------------------------------------
intervalId = undefined

isHidden = false;

function setDocumentTitle()
{
    try{
        iconNum = 0;
        if(document.hidden == true) {
            isHidden = true;
            var siteTip = localStorage.getItem(newSiteTipKey);
            var unReadAllNum = localStorage.getItem(roomListMsgUnReadNum);
            if(Number(unReadAllNum) == 0 || unReadAllNum == undefined || unReadAllNum == false)  {
                siteTip = "clear";
            }

            if(intervalId == undefined && siteTip != "clear") {
                intervalId = setInterval(function () {
                    if(siteTip == "clear") {
                        $(".icon").attr("href", "./favicon.ico?_v="+intervalId);
                        iconNum = 0;
                    } else {
                        if(Number(iconNum%2) == 0) {
                            $(".icon").attr("href", "./favicon.ico?_v="+intervalId);
                        } else {
                            $(".icon").attr("href", "./tip.png?_v="+intervalId);
                        }
                        iconNum = Number(iconNum+1);
                    }
                }, 100);
            }
            return ;
        }
        if(isHidden) {
            try{
                var chatSessionId = $(".chatsession-lists .chatsession-row-active").attr("chat-session-id");
                var currentChatSessionId = localStorage.getItem(chatSessionIdKey);

                if(chatSessionId != currentChatSessionId) {
                    window.location.reload();
                }
            } catch (error) {

            }

            isHidden = false;
        }

        $(".icon").attr("href", "./favicon.ico");
        iconNum = 0;
        clearInterval(intervalId);
        intervalId = undefined
    }catch (error) {

    }
}


document.addEventListener('visibilitychange', function(){
   setDocumentTitle();
}, false);

//--------------------------------------logout----------------------------------------------
$(document).on("click", "#logout", function (event) {
    logout(event);
});

function logout(event)
{
    event.stopPropagation();
    var tip = $.i18n.map['logoutJsTip'] != undefined ? $.i18n.map['logoutJsTip']: "退出账号，将会清空聊天记录";
    if(confirm(tip)) {
        localStorage.clear();
        window.location.href = "./index.php?action=page.logout";
    }
}

//------------------------------------*********Group function*********--------------------------------------------


$(document).on("click", ".see_group_profile", function () {
    var chatSessionId   = localStorage.getItem(chatSessionIdKey);
    var chatSessionType = localStorage.getItem(chatSessionId);
    var isShowProfile = $(this).attr("is_show_profile");
    if(1 == Number(isShowProfile)) {
        $('.right-body-sidebar').hide();
        $(this).attr("is_show_profile", 0);
    } else {
        $(this).attr("is_show_profile", 1);
        if(chatSessionType == U2_MSG) {
            sendFriendProfileReq(chatSessionId);
            $('.right-body-sidebar').show();
        } else if(chatSessionType == GROUP_MSG) {
            sendGroupProfileReq(chatSessionId, handleClickSeeGroupProfile);
        } else {
            $('.right-body-sidebar').hide();
        }
    }
});

function  handleClickSeeGroupProfile(results)
{

    try{
        if(results.hasOwnProperty("header") &&  results.header[HeaderErrorCode] == errorGroupNotExitsKey) {
            $(this).attr("is_show_profile", 0);
            var tip = $.i18n.map['notInGroupTip'] != undefined ? $.i18n.map['errorGroupExitsTip'] : "此群已解散";
            alert(tip);
            return;
        }
    }catch (error) {
        console.log(error);
    }

    var groupProfile = results != undefined && results.hasOwnProperty("profile") ? results.profile : false;
    if(!groupProfile) {
        $(this).attr("is_show_profile", 0);
        var tip = $.i18n.map['notInGroupTip'] != undefined ? $.i18n.map['notInGroupTip'] : "你已不在此群";
        alert(tip);
    } else {
        $('.right-body-sidebar').show();
    }
    handleGetGroupProfile(results);
}


////check is group speaker
function checkGroupMemberSpeakerType(userId, groupProfile)
{
    var users = groupProfile.speakers;
    if(users == null ){
        return false;
    }
    var length = users.length;
    var i;
    for(i=0; i<length; i++) {
        var user = users[i];
        if(user.userId == userId) {
            return user;
        }
    }
    return false;
}

////check is group admin
function checkGroupMemberAdminType(userId, groupProfile)
{
    var users = groupProfile.admins;
    if(users == null ){
        return false;
    }
    var length = users.length;
    var i;
    for(i=0; i<length; i++) {
        var user = users[i];
        if(user.userId == userId) {
            return user;
        }
    }
    return false;
}
////check is group owner
function checkGroupOwnerType(userId, groupProfile)
{
    try{
        var owner = groupProfile.owner;
        ///检查是否为群主
        if(owner.userId == userId) {
            return true;
        }
        return false;
    }catch (error) {
        console.log(error)
        return false;
    }
}

function checkGroupAdminContainOwner(userId, groupProfile)
{
    var isOwnerType = checkGroupOwnerType(userId, groupProfile);
    if(isOwnerType == true) {
        return true;
    }
    var isAdminType = checkGroupMemberAdminType(userId, groupProfile);
    return isAdminType;
}

////get  group admins
function getGroupAdmins(groupProfile)
{
    var users = groupProfile.admins;
    var groupAdminId =[];
    if(users == null ){
        return false;
    }
    var length = users.length;
    var i;
    for(i=0; i<length; i++) {
        var user = users[i];
        groupAdminId.push(user.userId);
    }
    return groupAdminId;
}

////get  group speakers
function getGroupSpeakers(groupProfile)
{
    var groupSpeakerId = [];

    var users = groupProfile.speakers;
    if(users == null ){
        return groupSpeakerId;
    }
    var length = users.length;
    var i;
    for(i=0; i<length; i++) {
        var user = users[i];
        groupSpeakerId.push(user.userId);
    }
    return groupSpeakerId;
}
////get  group owner
function  getGroupOwner(groupProfile)
{
    var owner = groupProfile.owner;
    return  owner.userId;
}

function checkGroupCanAddFriend()
{
    try{
        var groupId = localStorage.getItem(chatSessionIdKey);
        var groupProfileStr = localStorage.getItem("profile_"+groupId);
        if(groupProfileStr) {
            var groupProfile = JSON.parse(groupProfileStr);
            var isCanAddFriend =  groupProfile != null && groupProfile != undefined && groupProfile.hasOwnProperty("canAddFriend") ? groupProfile.canAddFriend : false;
            return isCanAddFriend;
        }
        return false;
    }catch (error){
        return false;
    }
}


//-------------------------------------------api.group.list-------------------------------------------------

/// group operation - api.group.list
function getGroupList(callback)
{
    var action = "api.group.list";
    var reqData = {
        "offset" : groupOffset,
        "count"  : defaultCountKey,
    }
    handleClientSendRequest(action, reqData, callback);
}

/// group operation - api.group.list - init html
function initGroupList(results)
{
    $(".group-list-contact-row").html("");
    if(results.hasOwnProperty("list")) {
        appendGroupListHtml(results);
    }
}
/// group operation - api.group.list - append html
function appendGroupListHtml(results) {
    var html = "";
    if(results == undefined || !results.hasOwnProperty("list")) {
        return ;
    }
    var groupList = results.list;

    if(groupList) {
        var groupCount = "("+results.totalCount+")";
        $(".group-count").html(groupCount);
        groupOffset = Number(groupOffset + defaultCountKey);
        var groupLength = groupList.length;
        html = "";
        for(i=0; i<groupLength; i++) {
            var group = groupList[i];
            var groupAvatarImg = getNotMsgImgUrl(group.avatar);
            html = template("tpl-group-contact", {
                groupId : group.id,
                groupName : group.name,
                groupAvatarImg:groupAvatarImg,
                nameInLatin:group.nameInLatin
            });
            html = handleHtmlLanguage(html);
            $(".group-list-contact-row").append(html);
        }
        var groupsDivHeight = $(".left-body-groups")[0].clientHeight;
        var groupToolsHeight = $(".group-tools")[0].clientHeight;
        $(".group-list-contact-row")[0].style.height = Number(groupsDivHeight-groupToolsHeight)+"px";
        getGroupList(appendGroupListHtml);
    }
}

//-------------------------------------------api.group.invitableFriends-------------------------------------------------
unselectMemberOffset = 0;

////group operation -  api.group.invitableFriends - init
$(document).on("click", ".invite_people", function () {
    unselectMemberOffset = 0;
    var action = "api.group.invitableFriends";
    var groupId = localStorage.getItem(chatSessionIdKey);
    var reqData = {
        "groupId": groupId,
        "offset" : unselectMemberOffset,
        "count" : defaultCountKey,
    }
    handleClientSendRequest(action, reqData, initUnselectMemberList);
});

////group operation -  api.group.invitableFriends - init html
function initUnselectMemberList(results)
{
    $(".pw-right-body").html("");
    $(".pw-left").html("");
    var list = results.list;
    var html = "";
    if(list) {
        getUnselectMemberListHtml(results);
    } else {
        html = template("tpl-invite-member-no-data", {
            siteAddress:siteAddress
        });
        html = handleHtmlLanguage(html);
        $(".pw-left").append(html);
    }
    showWindow($("#group-invite-people"));
}

////group operation -  api.group.invitableFriends - append html
function getUnselectMemberListHtml(results)
{
    var list = results.list;
    var html = "";
    if(list) {
        $(".pw-left").html("");
        var i;
        unselectMemberOffset = Number(unselectMemberOffset+defaultCountKey);
        var length = list.length;
        for(i=0; i<length ; i++) {
            var user = list[i];
            var friendAvatarImg = getNotMsgImgUrl(user.avatar);
            html = template("tpl-invite-member", {
                userId : user.userId,
                nickname:user.nickname ?  user.nickname : defaultUserName,
                friendAvatarImg:friendAvatarImg,
                siteAddress:siteAddress
            });
            html = handleHtmlLanguage(html);
            $(".pw-left").append(html);
        }
    }
}
////group operation -  api.group.invitableFriends - append html
$(function(){
    ////加载邀请好友入群列表
    $('.pw-left').scroll(function(){
        var pwLeft = $(".pw-left")[0];
        var ch  = pwLeft.clientHeight;
        var sh = pwLeft.scrollHeight;
        var st = $('.pw-left').scrollTop();
        ////文档的高度-视口的高度-滚动条的高度
        if((sh - ch - st) == 0){
            groupId = localStorage.getItem(chatSessionIdKey);
            var action = "api.group.invitableFriends";
            var reqData = {
                "groupId": groupId,
                "offset" : unselectMemberOffset,
                "count"  : defaultCountKey
            }
            handleClientSendRequest(action, reqData, getUnselectMemberListHtml);
        }
    });
});

//---------------------------------------api.group.profile-----------------------------------------------

$(document).on("click", ".group-desc-title", function () {
    showGroupDescWindow();
});

$(document).on("click", ".group-desc-body", function () {
    showGroupDescWindow();
});

function showGroupDescWindow()
{
    var groupId = localStorage.getItem(chatSessionIdKey);
    var groupProfile = getGroupProfile(groupId);
    var descBody = "";
    if(groupProfile != false && groupProfile!= null && groupProfile.hasOwnProperty("description")){
        descBody = groupProfile.description["body"];
    }
    descBody = descBody == undefined ? "" : descBody;

    var isAdmin = checkGroupAdminContainOwner(token, groupProfile);
    if(isAdmin && descBody=="") {
        descBody = $.i18n.map['defaultGroupDescTip'] != undefined ? $.i18n.map['defaultGroupDescTip'] : "点击填写群介绍，让大家更了解你的群～";
    }
    var html = template("tpl-desc-group-div", {
        descBody:descBody,
        isAdmin:isAdmin
    });
    html = handleHtmlLanguage(html);
    $("#group-desc-div").html(html);
    showWindow($("#group-desc-div"));
}

$(document).on("click", ".edit_group_desc", function (){

    var type = $(this).attr("type");
    var groupId = localStorage.getItem(chatSessionIdKey);
    if(type == 'edit') {
        $(this).attr("type", "done");
        var doneTip = $.i18n.map['groupProfileDoneTip'] != undefined?$.i18n.map['groupProfileDoneTip'] : "完成";
        $(this).html(doneTip);
        var groupProfile = getGroupProfile(groupId);
        var descBody = "";
        if(groupProfile != false && groupProfile!= null && groupProfile.hasOwnProperty("description")){
            descBody = groupProfile.description["body"];
        }
        descBody = descBody == undefined ? "" : descBody;
        var html = template("tpl-desc-group-textarea", {
            descBody:descBody
        });
        $(".group-desc-area").html(html);
    } else {
        $(this).attr("type", "edit");
        var doneTip = $.i18n.map['groupProfileEditTip'] != undefined ? $.i18n.map['groupProfileEditTip'] : "编辑";
        $(this).html(doneTip);
        var groupDesc = $(".textarea_desc").val();
        $(".group-desc-area").html(groupDesc);
        var values = {
            type : ApiGroupUpdateType.ApiGroupUpdateDescription,
            writeType:DataWriteType.WriteUpdate,
            description : {
                type: GroupDescriptionType.GroupDescriptionText,
                body: groupDesc
            }
        }
        updateGroupProfile(groupId, values);
    }

});


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

//---------------------------------------api.group.members-----------------------------------------------
////group operation - api.group.members - get member list
function getGroupMembers(groupId, offset, count, callback)
{
    var action = "api.group.members";
    var currentGroupId = localStorage.getItem(chatSessionIdKey);
    var type = localStorage.getItem(currentGroupId);

    if(type == U2_MSG) {
        return;
    }
    //not current group
    if(currentGroupId != groupId) {
        return;
    }
    var reqData = {
        "groupId": currentGroupId,
        "offset" : offset,
        "count" : count,
    }
    handleClientSendRequest(action, reqData, callback);
}


// reload group members for group profile
function displayGroupMemberForGroupInfo(results)
{
    var list = results.list;
    $(".group-member-body").html("");
    if(list) {
        var memberCount = "("+results.totalCount+")";
        $(".group-member-count").html(memberCount);
        var length = list.length;
        var html = "";
        var bodyDivNum = undefined;
        var divNum = 0;
        var groupId = localStorage.getItem(chatSessionIdKey);
        for(i=0; i<length ; i++) {
            var newBodyNum=Math.floor((i/6));
            if(newBodyNum != bodyDivNum) {
                divNum = divNum+1;
                html = template("tpl-group-member-body", {
                    num:divNum
                })
                $(".group-member-body").append(html);
            }
            var user = list[i].profile;
            var stroageUserProfileStr = localStorage.getItem(profileKey+user.userId);
            var nickname = user.nickname;
            try{
                nickname = user.nickname;
                if(stroageUserProfileStr != undefined && stroageUserProfileStr != false) {
                   var stroageUserProfile = JSON.parse(stroageUserProfileStr);
                   nickname = stroageUserProfile.nickname;
               }
            }catch (error){
            }
            var memberAvatarImg = getNotMsgImgUrl(user.avatar);
            html = template("tpl-group-member-body-detail", {
                userId : user.userId,
                nickname:nickname,
                memberAvatarImg:memberAvatarImg
            });
            html = handleHtmlLanguage(html);
            $(".member_body_"+divNum).append(html);
            bodyDivNum = newBodyNum;
        }
    }
}


//---------------------------------------api.group.inviteFriends-----------------------------------------------

$(document).on("click", ".cancle_invite_people", function () {
    removeWindow($("#group-invite-people"));
});

$(document).on("click", ".del_select_people", function () {
    var userId = $(this).attr("user-id");
    $(this).parent().remove();
    var selectHtml = '<img src="../../public/img/msg/member_unselect.png" /> ';
    $("."+userId).find(".select_people").attr("is_select", "not_selected");
    $("."+userId).find(".select_people").html(selectHtml);
});

//click invite friends
$(document).on("click", ".pw-left .choose-member", function(){
    var isSelect = $(this).find(".select_people").attr("is_select");
    if(isSelect != "is_select") {
        var userId = $(this).attr("user-id");
        var selectHtml = '<img src="../../public/img/msg/member_select.png" /> ';
        $(this).find(".select_people").attr("is_select", "is_select");
        $(this).find(".select_people").html(selectHtml);
        var obj = $(this).clone();
        obj.find(".select_people").remove();
        var html = '<div class="pw-contact-row-checkbox del_select_people " user-id="'+userId+'"> <img src="../../public/img/msg/btn-x.png" /> </div>';
        obj.append(html);
        obj.appendTo(".pw-right-body");
    } else {
        var userId = $(this).attr("user-id");
        $(".pw-right .pw-right-body ."+userId).remove();
        var selectHtml = '<img src="../../public/img/msg/member_unselect.png" /> ';
        $(this).find(".select_people").attr("is_select", "no_select");
        $(this).find(".select_people").html(selectHtml);
    }
});

//---------------------------------------api.group.invite-----------------------------------------------

function handleAddMemberToGroup()
{
    removeWindow($("#group-invite-people"));
    syncMsgForRoom();
    var groupId = localStorage.getItem(chatSessionIdKey);
    getGroupMembers(groupId, 0, 18, displayGroupMemberForGroupInfo);
}

function addMemberToGroup(userIds, groupId)
{
    var action  = "api.group.invite";
    var reqData = {
        "groupId": groupId,
        "userIds" : userIds,
    }
    handleClientSendRequest(action, reqData, handleAddMemberToGroup);
}

$(document).on("click", ".add_member_to_group", function () {
    var rowList = $(".pw-right-body .pw-contact-row");
    var userIds = [];
    rowList.each(function(index, row) {
        var userId = $(row).attr("user-id");
        userIds.push(userId);
    });
    var groupId = localStorage.getItem(chatSessionIdKey);

    addMemberToGroup(userIds, groupId)
});


//---------------------------------------api.group.create-----------------------------------------------

$(".create_group_box_div_input").bind('input porpertychange',function() {
    if($(".create_group_box_div_input").val().length>0) {
        $(".create_group_box_div_input").addClass("rgb108");
    }
});


function groupCreateSuccess(results) {
    removeWindow($("#create-group"));

    var groupProfile = results.profile["profile"];

    localStorage.setItem(chatSessionIdKey, groupProfile.id);
    localStorage.setItem(groupProfile.id, GROUP_MSG);

    var groupName = groupProfile.name;
    groupName = template("tpl-string", {
        string : groupName
    });

    $(".chatsession-title").html(groupName);
    getGroupMembers(groupProfile.id, 0, 18, displayGroupMemberForGroupInfo);
    handleGetGroupProfile(results);
    insertGroupRoom(groupProfile.id, groupProfile.name);
    handleMsgRelation(undefined, groupProfile.id);
    $(".l-sb-item[data='chatSession']").click();
}


function createGroup()
{
    var enableCreateGroup = getEnableCreateGroup();
    if(!enableCreateGroup) {
        enableCreateGroupTip();
        return;
    }
    var groupName = $(".group_name").val();
    if(groupName.length > 10 || groupName.length < 1) {
        var tip = $.i18n.map['createGroupNameTip'] != undefined ? $.i18n.map['createGroupNameTip']: "群组名称长度限制1-10";
        alert(tip);
        return false;
    }
    var reqData = {
        "groupName" : groupName,
    };
    var action = "api.group.create";
    handleClientSendRequest(action, reqData, groupCreateSuccess);
}

function insertGroupRoom(groupId, groupName)
{
    var msg = {
        "fromUserId": token,
        "name" : groupName,
        "timeServer": Date.parse(new Date()),
        "roomType": GROUP_MSG,
        "toGroupId": groupId,
        "type": "MessageText",
        "text": {
            "body": ""
        },
        "className": "group-profile",
        "chatSessionId": groupId
    };
    msg = handleMsgInfo(msg);
    appendOrInsertRoomList(msg, true, false);
}


$(document).on("click", ".group_cancle", function(){
    $(".group_name").val("");
});

$(document).on("click", ".create-group", function () {
    showMiniLoading($(".create-group"));
    cancelLoadingBySelf();
    requestSiteConfig(checkEnableCreateGroup);
});

function getEnableCreateGroup()
{
    var enableCreateGroup = false;
   try{
       var siteConfigStr = localStorage.getItem(siteConfigKey);
       var siteConfig = JSON.parse(siteConfigStr);
       enableCreateGroup = siteConfig.enableCreateGroup == true ? true : false;
       var master = siteConfig.hasOwnProperty("masters") ? siteConfig.masters : undefined;
       if(master != undefined) {
           var masterStr = JSON.parse(master).join(",");
           if(masterStr.indexOf(token) != -1) {
               enableCreateGroup = true;
           }
       }
   }catch (error) {
   }
    return enableCreateGroup;
}
function checkEnableCreateGroup(results)
{
    ZalyIm(results);
    var enableCreateGroup = getEnableCreateGroup();
    var html = template("tpl-create-group-div", {
        enableCreateGroup:enableCreateGroup,
    });
    html = handleHtmlLanguage(html);
    $("#create-group").html(html);
    hideLoading();
    showWindow($("#create-group"));
}

function enableCreateGroupTip()
{

    var tip = $.i18n.map['notEnableCreateGroupTip'] ? $.i18n.map['notEnableCreateGroupTip'] : "站点禁止创建群组";
    alert(tip);
}

$(document).on("click", ".create_group_button" , function(){
    createGroup();
});

function createGroupByKeyDown(event)
{

    if(checkIsEnterBack(event) == false) {
        return;
    }
    createGroup();
}

//---------------------------------------click group member avatar-----------------------------------------------

var clickImgUserMsgId = '';
var clickImgUserId = '';

function handleClickGroupUserImg(results)
{
    var groupProfile = results.profile;

    if(groupProfile) {
        groupProfile.memberType = results.memberType ? results.memberType : GroupMemberType.GroupMemberGuest;

        var isOwner = groupProfile.memberType == GroupMemberType.GroupMemberOwner ? 1 : 0;
        var isAdmin = groupProfile.memberType == GroupMemberType.GroupMemberAdmin || isOwner ? 1 : 0 ;

        var memberIsAdmin = checkGroupMemberAdminType(clickImgUserId, groupProfile);
        var memberIsSpeaker = checkGroupMemberSpeakerType(clickImgUserId, groupProfile);
        var memberIsOwner = checkGroupOwnerType(clickImgUserId, groupProfile);
        var isFriend = localStorage.getItem(friendRelationKey+clickImgUserId) == FriendRelation.FriendRelationFollow ? 1 : 0;
        var isCanAddFriend = groupProfile.canAddFriend == true ? true : false;

        var html = template("tpl-group-user-menu", {
            userId : clickImgUserId,
            isFriend : isFriend,
            isOwner:isOwner,
            isAdmin:isAdmin,
            memberIsSpeaker:memberIsSpeaker == false ? false : true,
            memberIsAdmin:memberIsAdmin == false ? false : true,
            memberIsOwner:memberIsOwner == false ? false : true,
            isCanAddFriend : isCanAddFriend
        });

        html = handleHtmlLanguage(html);
        var node = $(".group-user-img-"+clickImgUserMsgId)[0].parentNode.nextSibling.nextSibling;
        $(node).append($(html));
    }
    handleGetGroupProfile(results);
}

$(document).on("click", ".group-user-img", function(){
    var groupId = localStorage.getItem(chatSessionIdKey);
    var userId = $(this).attr("userId");
    clickImgUserMsgId = $(this).attr("msgId");
    clickImgUserId = userId;
    $("#group-user-menu").attr("userId", userId);
    sendGroupProfileReq(groupId, handleClickGroupUserImg);
});


////设置新的聊天界面
$(document).on("click", "#open-temp-chat", function () {
    var node = $(this)[0].parentNode;
    var userId = $(node).attr("userId");
    sendFriendProfileReq(userId, openU2Chat);
});

function openU2Chat(result)
{
    handleGetFriendProfile(result);

    if(result == undefined) {
        return;
    }
    var profile = result.profile;

    if(profile != undefined && profile["profile"]) {
        var userProfile = profile["profile"];
        var userId = userProfile.userId;

        if(userId == undefined) {
            return ;
        }
        localStorage.setItem(chatSessionIdKey, userId);
        localStorage.setItem(userId, U2_MSG);
        $(".right-chatbox").attr("chat-session-id", userId);
        insertU2Room(undefined, userId);
    }
}

function insertU2Room(jqElement, userId)
{
    handleMsgRelation(jqElement, userId);
    var msg = {
        "fromUserId": token,
        "pointer": "78",
        "timeServer": Date.parse(new Date()),
        "roomType": "MessageRoomU2",
        "toUserId": userId,
        "type": "MessageText",
        "text": {
            "body": ""
        },
        "className": "u2-profile",
        "chatSessionId": userId,
    };
    msg = handleMsgInfo(msg);
    appendOrInsertRoomList(msg, true, false);
}


//---------------------------------------api.group.removeMember-----------------------------------------------
////group operation - api.group.removeMember
function removeMemberFromGroup(groupId, removeUserIds, callback)
{
    var action = "api.group.removeMember";
    var reqData = {
        "groupId": groupId,
        "userIds" : removeUserIds,
    }
    handleClientSendRequest(action, reqData, callback);
}

removeMemberId="";
function handleRemoveMember()
{
    try{
        $("."+removeMemberId).remove();
        var chatSessionId = localStorage.getItem(chatSessionIdKey);
        getGroupMembers(chatSessionId, 0, 18, displayGroupMemberForGroupInfo);
    }catch (error) {

    }
}
////group operation - api.group.removeMember - click remove group btn
$(document).on("click", ".remove_group_btn", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);
    var userId = $(this).attr("userId");
    removeMemberId=userId;
    var removeUserIds = new Array();
    removeUserIds.push(userId);
    removeMemberFromGroup(groupId, removeUserIds, handleRemoveMember);
});

////group operation - api.group.removeMember - click user avatar in group chat
$(document).on("click", "#remove-group-chat", function () {
    var tip = $.i18n.map['removeMemberFromGroupJsTip'] != undefined ? $.i18n.map['removeMemberFromGroupJsTip']: "确定要移除群聊?";
    if(confirm(tip)) {
        var groupId = localStorage.getItem(chatSessionIdKey);
        var node = $(this)[0].parentNode;
        var userId = $(node).attr("userId");
        var removeUserIds = new Array();
        removeUserIds.push(userId);
        removeMemberFromGroup(groupId, removeUserIds, reloadPage);
    }
});

//group operation - api.group.removeMember - click in group member list


function handleGetGroupMemberInfo(result)
{
    if(result == undefined) {
        return;
    }
    var profile = result.profile;

    if(profile != undefined && profile["profile"]) {
        var userProfile = profile["profile"];
        var relation = profile.relation == undefined ? FriendRelation.FriendRelationInvalid : profile.relation;
        var isSelf = userProfile.userId == token ? true : false;

        var groupId = localStorage.getItem(chatSessionIdKey);
        var groupProfileStr = localStorage.getItem(profileKey+groupId);
        var isAdmin = false;
        if(groupProfileStr) {
            var groupProfile = JSON.parse(groupProfileStr);
            isAdmin = checkGroupAdminContainOwner(token, groupProfile);
        }
        var isCanAddFriend = checkGroupCanAddFriend();
        var memberAvatarImg = getNotMsgImgUrl(userProfile.avatar);
        var html = template("tpl-group-member-info", {
            userId : userProfile.userId,
            nickname:userProfile.nickname,
            loginName:userProfile.loginName,
            relation:relation,
            isSelf:isSelf,
            isCanAddFriend:isCanAddFriend,
            isAdmin:isAdmin,
            memberAvatarImg:memberAvatarImg
        });
        html = handleHtmlLanguage(html);
        $(".group-member-info").html(html);
        $(".group-member-info")[0].style.display='block';
    }
    handleGetFriendProfile(result);
}


$(document).on("click", ".group-member", function (event) {
    event.stopPropagation();
    event.preventDefault();
    if(event.target.className == "remove_group_btn") {
        return;
    }
    var userId = $(this).attr("userId");
    var isSelf = userId == token ? true : false;
    var relation = localStorage.getItem(friendRelationKey+userId);

    var isCanAddFriend = checkGroupCanAddFriend();
    var groupId = localStorage.getItem(chatSessionIdKey);
    var groupProfileStr = localStorage.getItem(profileKey+groupId);
    var isAdmin = false;
    if(groupProfileStr) {
        var groupProfile = JSON.parse(groupProfileStr);
        isAdmin = checkGroupAdminContainOwner(token, groupProfile);
    }

    var html = template("tpl-group-member-info", {
        userId : userId,
        nickname:$(this).attr("nickname"),
        relation:relation,
        avatar:$(".info-avatar-"+userId).attr("src"),
        isSelf:isSelf,
        isCanAddFriend:isCanAddFriend,
        isAdmin:isAdmin
    });
    html = handleHtmlLanguage(html);
    $(".group-member-info").html(html);
    getFriendProfile(userId, true, handleGetGroupMemberInfo);
});

function  reloadPage() {
    window.location.reload();
}


//---------------------------------------group speakers-----------------------------------------------

// group operation -- group speakers from group profile
$(".group_speakers").on("click", function () {
    showWindow($("#group-speaker-people"));
    unselectSpeakerMemberOffset =0;
    var groupId = localStorage.getItem(chatSessionIdKey);
    sendGroupProfileReq(groupId, handelGroupSpeakerList);
});

// group operation -- group speakers from group profile - init html
function handelGroupSpeakerList(result)
{
    var groupProfile = result.profile;
    if(groupProfile) {
        var isSelfAdminRole = false;
        if(checkGroupMemberAdminType(token, groupProfile)) {
            isSelfAdminRole = true;
        }
        if(checkGroupOwnerType(token, groupProfile)){
            isSelfAdminRole = true;
        }
        $(".speaker-people-div").html('');
        if(isSelfAdminRole == true) {
            $(".remove-all-speaker")[0].style.display = "flex";
            $(".set_group_speakers")[0].style.display = "flex";
        }

        if(groupProfile.hasOwnProperty("speakers")) {
            var speakers = groupProfile.speakers;
            var speakersLength = speakers.length;
            for(var i=0; i<speakersLength;i++){
                var speakerInfo = speakers[i];
                var html = getSpeakerMemberHtml(speakerInfo,  true, "member", isSelfAdminRole);
                $(".speaker-people-div").append(html);
            }
            var openSrc = "./public/img/msg/icon_switch_on.png";
            $(".group_speakers_set").attr("src", openSrc);
            $(".group_speakers_set").attr("value", "on");


        } else {
            var closeSrc = "./public/img/msg/icon_switch_off.png";
            $(".group_speakers_set").attr("src", closeSrc);
            $(".group_speakers_set").attr("value", "off");
        }
        // group operation -- group speakers from group profile - init member html
        if(isSelfAdminRole) {
            $(".speaker-group-member").remove();
            var html = template("tpl-group-member-for-speaker", {});
            html = handleHtmlLanguage(html);
            $(".speaker-content").append(html);
            $(".speaker-group-member-div").html('');
            getGroupMembers(groupProfile.id, unselectSpeakerMemberOffset, defaultCountKey, initSpeakerGroupMemberList);
        }
    }
    handleGetGroupProfile(result);
}

$(document).on("click", ".group_speakers_set", function(){
    var value = $(this).attr("value");
    if(value == "on") {
        //off
        var closeSrc = "./public/img/msg/icon_switch_off.png";
        $(".group_speakers_set").attr("src", closeSrc);
        $(".group_speakers_set").attr("value", "off");
        $(".remove-all-speaker").click();
    } else {
        //on
        var openSrc = "./public/img/msg/icon_switch_on.png";
        $(".group_speakers_set").attr("src", openSrc);
        $(".group_speakers_set").attr("value", "on");
        var groupId = localStorage.getItem(chatSessionIdKey);
        var speakerUserIds = new Array();
        speakerUserIds.push(token);
        updateGroupSpeaker(groupId, speakerUserIds, SetSpeakerType.AddSpeaker, handleSetSpeaker);

    }

});

// group operation -- group speakers from group profile - init member html
unselectSpeakerMemberOffset = 0;
function initSpeakerGroupMemberList(results)
{
    var list = results.list;
    if(list) {
        unselectSpeakerMemberOffset = Number(unselectSpeakerMemberOffset+defaultCountKey);
        var length = list.length;
        var html = "";
        var groupId = localStorage.getItem(chatSessionIdKey);
        var groupProfile = getGroupProfile(groupId);
        var groupOwnerId = getGroupOwner(groupProfile);
        var groupAdminIds = getGroupAdmins(groupProfile);
        var speakerListMemberIds = getGroupSpeakers(groupProfile);
        var isSelfAdminRole = false;
        if(checkGroupMemberAdminType(token, groupProfile)) {
            isSelfAdminRole = true;
        }
        if(checkGroupOwnerType(token, groupProfile)){
            isSelfAdminRole = true;
        }
        for(i=0; i<length ; i++) {
            var user = list[i].profile;
            var userId = user.userId;
            var isType = "member";

            if(speakerListMemberIds && speakerListMemberIds.indexOf(userId) != -1) {
                continue;
            }
            var html = getSpeakerMemberHtml(user,  false, "member", isSelfAdminRole);
            $(".speaker-group-member-div").append(html);
        }
        $(".speaker-group-member-div")[0].style.height = $(".speaker-group-member-div")[0].clientHeight+"px";
    }
}
// group operation -- group speakers from group profile
$(function () {
    ////加载设置群成员列表
    $('.speaker-content').scroll(function(){
        var pwLeft = $(".speaker-content")[0];
        var ch  = pwLeft.clientHeight;
        var sh = pwLeft.scrollHeight;
        var st = $('.speaker-content').scrollTop();
        ////文档的高度-视口的高度-滚动条的高度
        if((sh - ch - st) == 0){
            var groupId = localStorage.getItem(chatSessionIdKey);
            getGroupMembers(groupId, unselectSpeakerMemberOffset, defaultCountKey, initSpeakerGroupMemberList);
        }
    });
});


//set group speakers by click user avatar from group chat dialog
$(document).on("click", "#set-speaker", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);
    var node = $(this)[0].parentNode;
    var userId = $(node).attr("userId");
    var speakerUserIds = [];
    ////追加操作
    var tip = $.i18n.map['setSpeakerJsTip'] != undefined ? $.i18n.map['setSpeakerJsTip']: "设置发言人";
    if(confirm(tip)) {
        speakerUserIds.push(userId);
        updateGroupSpeaker(groupId, speakerUserIds, SetSpeakerType.AddSpeaker, handleSetSpeaker);
        removeWindow($("#group-user-menu"));
    }
});

//remove group speakers by click user avatar from group chat dialog
$(document).on("click", "#remove-speaker", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);
    var node = $(this)[0].parentNode;
    var userId = $(node).attr("userId");
    var speakerUserIds = [];
    ////追加操作
    var tip = $.i18n.map['removeSpeakerJsTip'] != undefined ? $.i18n.map['removeSpeakerJsTip']: "确定要移除发言权限?";
    if(confirm(tip)) {
        speakerUserIds.push(userId);
        updateGroupSpeaker(groupId, speakerUserIds, SetSpeakerType.RemoveSpeaker, handleSetSpeaker);
        removeWindow($("#group-user-menu"));
        sendGroupProfileReq(groupId, handleGetGroupProfile);
    }
});

function updateGroupSpeaker(groupId, speakerUserIds, type, callback)
{
    var action = "api.group.setSpeaker";
    var reqData;
    if(speakerUserIds.length > 0 ) {
        reqData = {
            "groupId": groupId,
            "setType" : type,
            "speakerUserIds" :speakerUserIds,
        }
    } else {
        reqData = {
            "groupId": groupId,
            "setType" : type,
        }
    }

    handleClientSendRequest(action, reqData, callback);
}

function handleSetSpeaker(result)
{
    try{
        $(".add_speaker_btn[userId="+token+"]").click();

        var speakerUserIds = result.speakerUserIds;
        var speakerKey = speakerUserIdsKey+localStorage.getItem(chatSessionIdKey);
        localStorage.setItem(speakerKey, JSON.stringify(speakerUserIds));
        var groupId = localStorage.getItem(chatSessionIdKey);

        sendGroupProfileReq(groupId, handleGetGroupProfile);
    }catch (error) {

    }
}
addSpeakerInfo=[];

function handleAddSpeaker()
{
    //开启禁言
    var openSrc = "./public/img/msg/icon_switch_on.png";
    $(".group_speakers_set").attr("src", openSrc);
    $(".group_speakers_set").attr("value", "on");

    var groupId = localStorage.getItem(chatSessionIdKey);
    var groupProfile = getGroupProfile(groupId);

    var isSelfAdminRole = false;
    if(checkGroupMemberAdminType(token, groupProfile)) {
        isSelfAdminRole = true;
    }
    if(checkGroupOwnerType(token, groupProfile)){
        isSelfAdminRole = true;
    }

    var addSpeakerIdLength = addSpeakerInfo.length;
    for(var i=0; i<addSpeakerIdLength; i++) {
        var speakerInfo = addSpeakerInfo[i];
        $("."+speakerInfo.userId).remove();
        var html = getSpeakerMemberHtml(speakerInfo,  true, "member", isSelfAdminRole);
        $(".speaker-people-div").append(html);
    }
    addSpeakerInfo=[];
    sendGroupProfileReq(groupId, handleGetGroupProfile);
}

function getSpeakerMemberHtml(speakerInfo,  isSpeaker, isMemberType, isSelfAdminRole)
{
    var memberAvatarImg = getNotMsgImgUrl(speakerInfo.avatar);
    var html = template("tpl-speaker-member",{
        nickname:speakerInfo.nickname,
        userId:speakerInfo.userId,
        avatar:speakerInfo.avatar,
        isSpeaker:isSpeaker,
        isMemberType:isMemberType,
        isSelfAdminRole:isSelfAdminRole,
        memberAvatarImg:memberAvatarImg
    });
    return  handleHtmlLanguage(html);
}

$(document).on("click", ".add_speaker_btn", function () {
    var userId = $(this).attr("userId");
    var groupId = localStorage.getItem(chatSessionIdKey);
    var speakerUserIds = [];
    speakerUserIds.push(userId);
    var speakerInfo = {
        userId:userId,
        nickname:$(this).attr("nickname"),
        avatar:$(this).attr("avatar"),
    }
    addSpeakerInfo.push(speakerInfo);
    updateGroupSpeaker(groupId, speakerUserIds, SetSpeakerType.AddSpeaker, handleAddSpeaker)
});

deleteSpeakerInfo=[];
function handleRemoveSpeaker()
{

    var delSpeakerLength=deleteSpeakerInfo.length;
    var groupId = localStorage.getItem(chatSessionIdKey);
    var groupProfile = getGroupProfile(groupId);

    var isSelfAdminRole = false;
    if(checkGroupMemberAdminType(token, groupProfile)) {
        isSelfAdminRole = true;
    }
    if(checkGroupOwnerType(token, groupProfile)){
        isSelfAdminRole = true;
    }
    for(var i=0; i<delSpeakerLength; i++) {
        var speakerInfo = deleteSpeakerInfo[i];
        $("."+speakerInfo.userId).remove();
        var html = getSpeakerMemberHtml(speakerInfo,  false, "member", isSelfAdminRole);
        $(".speaker-group-member-div").append(html);
    }
    $(".speaker-group-member-div")[0].style.height = $(".speaker-group-member-div")[0].scrollHeight+"px";

    //关闭禁言
    if($(".speaker_remove_people").length < 1) {
        var closeSrc = "./public/img/msg/icon_switch_off.png";
        $(".group_speakers_set").attr("src", closeSrc);
        $(".group_speakers_set").attr("value", "off");
    }

    deleteSpeakerInfo=[];
    sendGroupProfileReq(groupId, handleGetGroupProfile);
}

$(document).on("click", ".remove_speaker_btn", function () {
    var userId = $(this).attr("userId");
    var groupId = localStorage.getItem(chatSessionIdKey);
    var speakerUserIds = [];
    speakerUserIds.push(userId);
    var speakerInfo = {
        userId:userId,
        nickname:$(this).attr("nickname"),
        avatar:$(this).attr("avatar"),
    }
    deleteSpeakerInfo.push(speakerInfo);
    updateGroupSpeaker(groupId, speakerUserIds, SetSpeakerType.RemoveSpeaker, handleRemoveSpeaker)
});

$(document).on("click", ".remove-all-speaker", function () {
    var removeSpeakers = $(".remove-speaker");
    var removeSpeakersLength = removeSpeakers.length;
    var groupId = localStorage.getItem(chatSessionIdKey);
    for(var i=0; i<removeSpeakersLength;i++) {
        var speakers = removeSpeakers[i];
        var userId = $(speakers).attr("userId");
        var speakerInfo = {
            userId:userId,
            nickname:$(speakers).attr("nickname"),
            avatar:$(speakers).attr("avatar"),
        }
        deleteSpeakerInfo.push(speakerInfo);
    }
    updateGroupSpeaker(groupId, [], SetSpeakerType.CloseSpeaker, handleRemoveSpeaker)
});



// click user avatar in group dialog
$(document).on("click", ".open_chat", function () {
    var userId = $(this).attr("userId");
    sendFriendProfileReq(userId, openU2Chat);
    removeWindow($("#group-member-list-div"));
});

// click user avatar in group dialog
$(document).on("click", ".add-friend-by-group-member",function () {
    var userId = $(this).attr("userId");
    sendFriendApplyReq(userId, "", "");
    $(this).attr("disabled", "disabled");
    alert("发送申请成功");
    $(".group-member-info")[0].style.display='none';
});

function closeGroupMemberInfo()
{
    $(".group-member-info")[0].style.display='none';
}


//---------------------------------------display group qrcode-----------------------------------------------
$(document).on("click", ".share-group", function () {
    $("#qrcodeCanvas").html("");

    var chatSessionId = localStorage.getItem(chatSessionIdKey);
    var groupProfile = getGroupProfile(chatSessionId);
    var groupName = groupProfile != false && groupProfile.name != "" ? groupProfile.name : $(".chatsession-title").html();


    var siteConfigJsonStr = localStorage.getItem(siteConfigKey);
    var siteName = "";
    if(siteConfigJsonStr ) {
        siteConfig = JSON.parse(siteConfigJsonStr);
        siteName = siteConfig.name;
    }

    var html = template("tpl-share-group-div", {
        siteName:siteName,
        groupName:groupName,
        groupId:chatSessionId
    });

    html = handleHtmlLanguage(html);
    $("#share_group").html(html);
    showWindow($("#share_group"));

    var src = $("#share_group").attr("src");

    if(src == "" || src == undefined) {
        src="./public/img/msg/group_default_avatar.png";
    }
    var urlLink = changeZalySchemeToDuckChat(chatSessionId, "g");
    $("#share_group").attr("urlLink", urlLink);
    generateQrcode($('#qrcodeCanvas'),  urlLink, src, true, "group");
});

$(document).on("click",".copy-share-group", function(){
    var urlLink = $("#share_group").attr("urlLink");
    const input = document.createElement('input');
    document.body.appendChild(input);
    input.setAttribute('value', urlLink);
    input.select();
    if (document.execCommand('copy')) {
        document.execCommand('copy');
        alert('复制成功');
    }
    document.body.removeChild(input);

});

$(document).on("click", ".save-share-group", function () {
    downloadImgFormQrcode("groupQrcode");
});


//---------------------------------------display group members-----------------------------------------------
groupMemberListOffset=0;
groupMemberListAdmins=[];

function addHtmlToGroupList(user, isType)
{
    var groupId = localStorage.getItem(chatSessionIdKey);
    var groupProfile = getGroupProfile(groupId);

    var isGroupOwner = checkGroupOwnerType(token, groupProfile);
    var isGroupAdmin = checkGroupMemberAdminType(token, groupProfile);
    var isPermission = isGroupOwner || isGroupAdmin ? "admin" : "member";
    var memberAvatarImg = getNotMsgImgUrl(user.avatar);
    var html = template("tpl-group-member-list", {
        userId : user.userId,
        nickname:user.nickname,
        isType:isType,
        isPermission:isPermission,
        memberAvatarImg:memberAvatarImg
    })
    html = handleHtmlLanguage(html);
    $(".group-member-content").append(html);
}

function initGroupMemberForGroupMemberList(results)
{
    var list = results.list;
    if(list) {
        groupMemberListOffset = Number(groupMemberListOffset+defaultCountKey);
        var length = list.length;
        for(var i=0; i<length ; i++) {
            var user = list[i].profile;
            if(groupMemberListAdmins.indexOf(user.userId) == -1) {
                addHtmlToGroupList(user, "member");
            }
        }
    }
}

function addGroupMemberToGroupMemberList(result)
{
    handleGetGroupProfile(result);
    var groupProfile = result.profile;
    if(groupProfile) {
        var owner = groupProfile.owner;
        groupMemberListAdmins.push(owner.userId);
        addHtmlToGroupList(owner, "owner", "admin");

        if(groupProfile.hasOwnProperty("admins")) {
            var admins = groupProfile.admins;
            if(admins == null ){
                return false;
            }
            var length = admins.length;
            for(var i=0; i<length; i++) {
                var admin = admins[i];
                addHtmlToGroupList(admin, "admin");
                groupMemberListAdmins.push(admin.userId);
            }
        }
        getGroupMembers(groupProfile.id, groupMemberListOffset, defaultCountKey, initGroupMemberForGroupMemberList);
    }
}

//click see_all_group_member , get group members
$(document).on("click", ".see_all_group_member", function () {
    groupMemberListOffset = 0;
    showWindow($("#group-member-list-div"));
    $(".group-member-info")[0].style.display="none";
    $(".group-member-content").html("");
    var groupId = localStorage.getItem(chatSessionIdKey);
    sendGroupProfileReq(groupId, addGroupMemberToGroupMemberList);
});

$(function () {
    ////加载设置群成员列表
    $('.group-member-content').scroll(function(){
        var pwLeft = $(".group-member-content")[0];
        var ch  = pwLeft.clientHeight;
        var sh = pwLeft.scrollHeight;
        var st = $('.group-member-content').scrollTop();

        ////文档的高度-视口的高度-滚动条的高度
        if((sh - ch - st) == 0){
            var groupId = localStorage.getItem(chatSessionIdKey);
            getGroupMembers(groupId, groupMemberListOffset, defaultCountKey, initGroupMemberForGroupMemberList )
        }
    });
});


function getGroupProfileByClickChatSessionRow(jqElement)
{
    var groupId =  jqElement.attr("chat-session-id");

    if(groupId == undefined || !groupId) {
        $(this).remove();
        return ;
    }
    var groupName = $('.nickname_'+groupId).html();
    groupName = template("tpl-string", {
        string : groupName
    });
    $(".chatsession-title").html(groupName);

    sendGroupProfileReq(groupId, handleGetGroupProfile);

    localStorage.setItem(chatSessionIdKey, groupId);
    localStorage.setItem(groupId, GROUP_MSG);

    $("#share_group").removeClass();
    $("#share_group").addClass("info-avatar-"+groupId);

    handleMsgRelation($(this), groupId);
}

$(document).on("click", ".group-profile", function () {
    getGroupProfileByClickChatSessionRow($(this));
});

// contact-row-u2-profile
$(document).on("click", ".contact-row-group-profile", function () {
    var groupId =  $(this).attr("chat-session-id");
    if(groupId == undefined) {
        alert("not found group-id by click group-profile");
        return ;
    }
    localStorage.setItem(chatSessionIdKey, groupId);
    localStorage.setItem(groupId, GROUP_MSG);
    $(".right-chatbox").attr("chat-session-id", groupId);

    handleClickRowGroupProfile(groupId);
    getInitChatPlugin(GROUP_MSG);

});


function handleClickGroupProfile(results)
{
    try {
        var groupProfile = results.profile;
        if (groupProfile) {
            insertGroupRoom(groupProfile.id, groupProfile.name);
            handleMsgRelation($(this), groupProfile.id);
        }
    }catch (error) {
        console.log(error)
    }
    handleGetGroupProfile(results);

}

function handleClickRowGroupProfile(groupId)
{
    sendGroupProfileReq(groupId, handleClickGroupProfile);

    var groupName = $('.nickname_'+groupId).html();
    groupName = template("tpl-string", {
        string : groupName
    });
    $(".chatsession-title").html(groupName);
}

//---------------------------------------api.group.update-----------------------------------------------
function updateGroupProfile(groupId, values)
{
    var reqValues = [];
    reqValues.push(values);

    var action = "api.group.update";
    var reqData = {
        "groupId": groupId,
        "values" :reqValues,
    }
    handleClientSendRequest(action, reqData, handleGetGroupProfile);
}

// can_guest_read_message click ,游客是否可以查看消息
$(document).on("click", ".can_guest_read_message", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);
    var canRead = $(".can_guest_read_message").attr("is_on");

    if(canRead == "on") {
        $(".can_guest_read_message").attr("is_on", "off");
        $(".can_guest_read_message").attr("src", "../../public/img/msg/icon_switch_off.png");
        canRead = false;
    } else {
        $(".can_guest_read_message").attr("is_on", "on");
        $(".can_guest_read_message").attr("src", "../../public/img/msg/icon_switch_on.png");
        canRead = true;
    }

    var values = {
        type : ApiGroupUpdateType.ApiGroupUpdateCanGuestReadMessage,
        writeType:DataWriteType.WriteUpdate,
        canGuestReadMessage :canRead,
    }
    updateGroupProfile(groupId, values);
});
//update group introduce

$(document).on("click", ".save-permission-join", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);

    var permissionJoin = $(".permission-join-select").attr("permissionJoin");
    var values = {
        type : ApiGroupUpdateType.ApiGroupUpdatePermissionJoin,
        writeType:DataWriteType.WriteUpdate,
        permissionJoin : permissionJoin,
    };
    removeWindow($("#permission-join"));
    updateGroupProfile(groupId, values);
});

//set group  admin
$(document).on("click", "#set-admin", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);
    var node = $(this)[0].parentNode;
    var userId = $(node).attr("userId");
    var adminUserIds = [];
    ////追加操作
    var tip = $.i18n.map['setAdminJsTip'] != undefined ? $.i18n.map['setAdminJsTip']: "设置管理员";
    if(confirm(tip)) {
        adminUserIds.push(userId);
        var values = {
            type : ApiGroupUpdateType.ApiGroupUpdateAdmin,
            writeType:DataWriteType.WriteAdd,
            adminUserIds : adminUserIds,
        }
        updateGroupProfile(groupId, values);
        removeWindow($("#group-user-menu"));
    }
});

//remove group admin
$(document).on("click", "#remove-admin", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);
    var node = $(this)[0].parentNode;
    var userId = $(node).attr("userId");
    var adminUserIds = [];
    ////追加操作
    var tip = $.i18n.map['removeAdminJsTip'] != undefined ? $.i18n.map['removeAdminJsTip']: "移除管理员";
    if(confirm(tip)) {
        adminUserIds.push(userId);
        var values = {
            type : ApiGroupUpdateType.ApiGroupUpdateAdmin,
            writeType:DataWriteType.WriteDel,
            adminUserIds : adminUserIds,
        }
        updateGroupProfile(groupId, values);
        removeWindow($("#group-user-menu"));
    }
});

//update group mute
$(document).on("click", ".group_mute", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);
    var mute = $(".group_mute").attr("is_on");

    clearRoomUnreadMsgNum(groupId);

    if(mute == "on") {
        $(".group_mute").attr("is_on", "off");
        $(".group_mute").attr("src", "../../public/img/msg/icon_switch_off.png");
        mute = false;
    } else {
        $(".group_mute").attr("is_on", "on");
        $(".group_mute").attr("src", "../../public/img/msg/icon_switch_on.png");
        mute = true;
    }

    var values = {
        type : ApiGroupUpdateType.ApiGroupUpdateIsMute,
        writeType:DataWriteType.WriteUpdate,
        isMute :mute,
    }
    updateGroupProfile(groupId, values);
});

// update group name
function updateGroupNameName(event)
{
    if(checkIsEnterBack(event) == false) {
        return;
    }

    var groupName = $("#groupName").val();
    var groupId = localStorage.getItem(chatSessionIdKey);

    if(groupName.length >10 || groupName.length < 1) {
        var tip = $.i18n.map['createGroupNameTip'] != undefined ? $.i18n.map['createGroupNameTip']: "群组名称长度限制1-10";
        alert(tip);
        return;
    }

    var values = {
        type : ApiGroupUpdateType.ApiGroupUpdateName,
        writeType:DataWriteType.WriteUpdate,
        name :groupName,
    }
    updateGroupProfile(groupId, values);

    var html = template("tpl-group-name-div", {
        groupName:groupName,
        editor:0
    });
    $("#groupName")[0].parentNode.replaceChild($(html)[0], $("#groupName")[0]);
}

//click group name in group profile right body
$(document).on("click", ".groupName",function () {
    var groupName = $(this).html();
    var html = template("tpl-group-name-div", {
        groupName:groupName,
        editor:1
    });
    $(this)[0].parentNode.replaceChild($(html)[0], $(this)[0]);
});

//---------------------------------api.group.quit api.group.delete-------------------------------------------

$(document).on("click", ".quit-group", function () {
    var tip = $.i18n.map['quitGroupJsTip'] != undefined ? $.i18n.map['quitGroupJsTip']: "退出群组?";

    if(confirm(tip)) {
        var groupId = localStorage.getItem(chatSessionIdKey);
        var action = "api.group.quit";
        var reqData = {
            "groupId": groupId
        };
        handleClientSendRequest(action, reqData, handleDeleteOrQuitGroup);
    }
});

//---------------------------------api.group.delete-------------------------------------------

function handleDeleteOrQuitGroup() {
    $(".see_group_profile").attr("is_show_profile", 0);
    $(".right-body-sidebar").hide();
}


$(document).on("click", ".delete-group", function () {
    var tip = $.i18n.map['disbandGroupJsTip'] != undefined ? $.i18n.map['disbandGroupJsTip']: "解散群?";
    if(confirm(tip)) {
        var groupId = localStorage.getItem(chatSessionIdKey);
        var action = "api.group.delete";
        var reqData = {
            "groupId": groupId
        };
        handleClientSendRequest(action, reqData, handleDeleteOrQuitGroup());
    }
});



//-------------------------------*******Friend*******----------------------------------------------

//---------------------------------------api.friend.list----------------------------------------------
// friend operation -- api.friend.list - display apply friend num
function displayApplyFriendNum()
{
    try{
        var friendListNum = localStorage.getItem(applyFriendListNumKey);
        if(friendListNum > 0 && friendListNum != undefined) {
            $(".apply_friend_num")[0].style.display = "block";
            $(".apply_friend_num").html(friendListNum);
        } else {
            $(".apply_friend_list_num")[0].style.display = "none";
            $(".apply_friend_num")[0].style.display = "none";
        }
    }catch (error) {
        // console.log(error);
    }
}
// operation apply friend list num
function setFriendListTip(count)
{
    localStorage.setItem(applyFriendListNumKey, count);
}

function deleteFriendListTip()
{
    var count = localStorage.getItem(applyFriendListNumKey) ? Number(localStorage.getItem(applyFriendListNumKey)) : 0;
    count = (count-1>0) ? (count-1) : 0;
    localStorage.setItem(applyFriendListNumKey, count);
    displayApplyFriendNum();
}

// friend operation -- api.friend.list
function getFriendList(callback)
{
    var action = "api.friend.list";
    var reqData = {
        "offset" : friendOffset,
        "count"  : defaultCountKey,
    }
    handleClientSendRequest(action, reqData, callback);
}

// friend operation -- api.friend.list - append html
function  appendFriendListHtml(results)
{
    if(results == undefined || !results.hasOwnProperty("friends")) {
        return ;
    }
    var u2List = results.friends;
    if(u2List) {
        var friendCount = "("+results.totalCount+"人)";
        $(".friend-count").html(friendCount);

        friendOffset = Number(friendOffset + defaultCountKey);
        var u2Length = u2List.length;
        for(i=0; i<u2Length; i++) {
            var u2 = u2List[i].profile;
            var friendAvatarImg = getNotMsgImgUrl(u2.avatar);
            var html = template("tpl-friend-contact", {
                userId : u2.userId,
                nickname: u2.nickname ? u2.nickname : defaultUserName,
                friendAvatarImg:friendAvatarImg,
                nicknameInLatin:u2.nicknameInLatin
            });
            html = handleHtmlLanguage(html);
            $(".friend-list-contact-row").append(html);
        }
        var friendsDivHeight = $(".left-body-friends")[0].clientHeight;
        var friendToolsHeight = $(".friend-tools")[0].clientHeight;
        $(".friend-list-contact-row")[0].style.height = Number(friendsDivHeight-friendToolsHeight)+"px";
        getFriendList(appendFriendListHtml);
    }
}

// friend operation -- api.friend.list - init html
function initFriendList(results)
{
    $(".friend-list-contact-row").html("");
    if(results != undefined && results.hasOwnProperty("friends")) {
        appendFriendListHtml(results);
    }
    displayApplyFriendNum();
}

//---------------------------------------api.friend.profile----------------------------------------------

function getFriendProfileByClickChatSessionRow(jqElement)
{

    var userId = jqElement.attr("chat-session-id");
    if(userId == undefined) {
        return false;
    }

    $(".user-image-for-add").attr("class", "user-image-for-add");
    $(".user-image-for-add").attr("src", "../../public/img/msg/default_user.png");

    getFriendProfile(userId, true, handleGetFriendProfile);
    var nickname = $(".nickname_"+userId).html();
    var nickname = template("tpl-string", {
        string : nickname
    });
    $(".chatsession-title").html(nickname);

    localStorage.setItem(chatSessionIdKey, userId);
    localStorage.setItem(userId, U2_MSG);
    handleMsgRelation($(this), userId);
}

$(document).on("click", ".u2-profile", function () {
    getFriendProfileByClickChatSessionRow($(this));
});

//insert u2 room, when click user in friend lists
$(document).on("click", ".contact-row-u2-profile", function () {
    var userId = $(this).attr("chat-session-id");
    if(userId == undefined) {
        return false;
    }
    localStorage.setItem(chatSessionIdKey, userId);
    localStorage.setItem(userId, U2_MSG);
    $(".right-chatbox").attr("chat-session-id", userId);

    var friendName = $('.profile_nickname_'+userId).html();
    friendName = template("tpl-string", {
        string : friendName
    });
    $(".chatsession-title").html(friendName);

    sendFriendProfileReq(userId, handleGetFriendProfile);

    $(".user-image-for-add").attr("class", "user-image-for-add");
    $(".user-image-for-add").attr("src", "../../public/img/msg/default_user.png");
    insertU2Room($(this), userId);

    getInitChatPlugin(U2_MSG);
});

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

function insertU2Room(jqElement, userId)
{
    handleMsgRelation(jqElement, userId);
    var msg = {
        "fromUserId": token,
        "pointer": "78",
        "timeServer": Date.parse(new Date()),
        "roomType": "MessageRoomU2",
        "toUserId": userId,
        "type": "MessageText",
        "text": {
            "body": ""
        },
        "className": "u2-profile",
        "chatSessionId": userId,
    };
    msg = handleMsgInfo(msg);
    appendOrInsertRoomList(msg, true, false);
}

function displayProfile(profileId, profileType)
{
    var chatSessionId   = localStorage.getItem(chatSessionIdKey);
    updateInfo(profileId, profileType);

    if(profileId == chatSessionId) {
        displayCurrentProfile();
        return;
    }
}

function updateInfo(profileId, profileType)
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
    }

    var name = template("tpl-string", {
        string : name
    });

    try{
        name = name.trim();
    }catch (error) {
    }

    $(".nickname_"+profileId).html(name);


    try{

        if(chatSessionName == "") {
            chatSessionName = name;
        }
        $(".chatsession_nickname_"+profileId).html(chatSessionName);

        $(".aria-lable-"+profileId).attr("aria-lable", name);
        if(mute>0) {
            $(".room-chatsession-mute_"+profileId)[0].style.display = "block";
        } else {
            $(".room-chatsession-mute_"+profileId)[0].style.display = "none";
        }
    }catch (error) {
    }
}


function displayCurrentProfile()
{
    try{
        var chatSessionId   = localStorage.getItem(chatSessionIdKey);
        var chatSessionType = localStorage.getItem(chatSessionId);

        var muteKey = msgMuteKey + chatSessionId;
        var mute = localStorage.getItem(muteKey);

        if(chatSessionType == U2_MSG) {
            try{
                $(".group-profile-desc")[0].style.visibility = "hidden";
                $(".user-profile-desc")[0].style.visibility = "visible";
                $(".user-profile-desc")[0].style.width = "100%";
                $(".invite_people")[0].style.visibility="hidden";
                $(".add_friend")[0].style.display="inline";
                $(".user-image-for-add").addClass("info-avatar-"+chatSessionId);

                var friendProfile = getFriendProfile(chatSessionId, false, handleGetFriendProfile);

                if(friendProfile) {
                    var trueNickname = friendProfile.nickname;
                    var nickname = template("tpl-string", {
                        string : trueNickname
                    });
                    $(".nickname_"+chatSessionId).html(nickname);

                    $(".chatsession-title").html(nickname);

                    var isMaster = isJudgeSiteMasters(chatSessionId);

                    var html = template("tpl-friend-profile", {
                        isMaster:isMaster,
                        nickname:trueNickname,
                        loginName:friendProfile.loginName,
                    });
                    $(".user-desc-body").html(html);
                } else {
                    $(".chatsession-title").html("");
                    $(".user-desc-body").html("");
                }

                $(".chat_session_id_"+chatSessionId).addClass("chatsession-row-active");
                var relationKey = friendRelationKey + chatSessionId;
                var relation = localStorage.getItem(relationKey) ;
                if(relation == FriendRelation.FriendRelationFollow) {
                    $(".delete-friend")[0].style.display = "flex";
                    $(".add-friend")[0].style.display = "none";
                    $(".add_friend")[0].style.display = "none";
                    $(".edit-remark")[0].style.display = "flex";
                    $(".mute-friend")[0].style.display = "flex";
                } else {
                    $(".delete-friend")[0].style.display = "none";
                    $(".add-friend")[0].style.display = "flex";
                    $(".edit-remark")[0].style.display = "none";
                    $(".mute-friend")[0].style.display = "none";
                    $(".add_friend")[0].style.display = "inline";
                }

                if(mute == 1) {
                    $(".friend_mute").attr("src", "../../public/img/msg/icon_switch_on.png");
                    $(".friend_mute").attr("is_on", "on");
                } else {
                    $(".friend_mute").attr("src", "../../public/img/msg/icon_switch_off.png");
                    $(".friend_mute").attr("is_on", "off");
                }
            }catch (error) {

            }

        } else if(chatSessionType == GROUP_MSG ) {
            $(".group-profile-desc")[0].style.visibility = "visible";
            $(".group-profile-desc")[0].style.width = "100%";
            $(".user-profile-desc")[0].style.visibility = "hidden";
            $(".invite_people")[0].style.visibility="visible";
            $(".add_friend")[0].style.display = "none";

            var groupProfile = getGroupProfile(chatSessionId);

            if(groupProfile != false && groupProfile != null) {
                var groupName = groupProfile.name
                groupName = template("tpl-string", {
                    string : groupName
                });
                $(".chatsession-title").html(groupName);
                $(".nickname_"+groupProfile.id).html(groupName);
                $(".groupName").html(groupName);
            }

            $("#share_group").removeClass();
            $("#share_group").addClass("info-avatar-"+groupProfile.id);

            $(".group-desc-body").html("");

            try{
                var descBody = "";
                if(groupProfile!=false && groupProfile!= null && groupProfile.hasOwnProperty("description")) {
                    var descBody = groupProfile.description["body"];
                    if(descBody != undefined && groupProfile.description['type'] == GroupDescriptionType.GroupDescriptionMarkdown) {
                        var md = window.markdownit();
                        descBody = md.render(descBody);
                    } else {

                        if(descBody == null || descBody == undefined || descBody.length<1 ) {
                            descBody = $.i18n.map['defaultGroupDescTip'] != undefined ? $.i18n.map['defaultGroupDescTip'] : "点击填写群介绍，让大家更了解你的群～";
                        }
                        try{
                            if(descBody.trim().length > 70) {
                                descBody = descBody.trim().substr(0, 70)+"......";
                            }
                        }catch (error){

                        }
                        descBody = template("tpl-string", {
                            string:descBody
                        });
                    }
                    $(".group-desc-body").html(descBody);
                }
            }catch (error) {
                console.log(error.message)
            }
            getGroupMembers(groupProfile.id, 0, 18, displayGroupMemberForGroupInfo);

            try{
                var permissionJoin = groupProfile.permissionJoin;
                var memberType = groupProfile != false && groupProfile != null ? groupProfile.memberType : GroupMemberType.GroupMemberGuest ;
                switch (memberType) {
                    case GroupMemberType.GroupMemberOwner:
                        $('.invite_people')[0].style.display = "inline";
                        $('.quit-group')[0].style.display = "none";
                        $('.delete-group')[0].style.display = "flex";
                        $('.permission-join')[0].style.display = "flex";
                        $(".mute-group")[0].style.display = "flex";
                        $(".group-introduce").attr("disabled", false);
                        break;
                    case GroupMemberType.GroupMemberAdmin:
                        $('.invite_people')[0].style.display = "inline";
                        $('.quit-group')[0].style.display = "flex";
                        $('.delete-group')[0].style.display = "none";
                        $('.permission-join')[0].style.display = "flex";
                        $(".mute-group")[0].style.display = "flex";
                        $(".group-introduce").attr("disabled", "disabled");
                        $('.permission-join')[0].style.display = "none";
                        break;
                    case GroupMemberType.GroupMemberNormal:
                        if(permissionJoin == GroupJoinPermissionType.GroupJoinPermissionMember
                            || permissionJoin == GroupJoinPermissionType.GroupJoinPermissionPublic){
                            $('.invite_people')[0].style.display = "inline";
                        } else {
                            $('.invite_people')[0].style.display = "none";
                        }

                        $('.permission-join')[0].style.display = "none";
                        $('.quit-group')[0].style.display = "flex";
                        $('.delete-group')[0].style.display = "none";
                        $(".mute-group")[0].style.display = "flex";
                        $(".group-introduce").attr("disabled", "disabled");
                        break;
                    case GroupMemberType.GroupMemberGuest:
                        $('.quit-group')[0].style.display = "none";
                        $('.delete-group')[0].style.display = "none";
                        $('.permission-join')[0].style.display = "none";
                        $(".mute-group")[0].style.display = "none";
                        $(".group-introduce").attr("disabled", "disabled");
                        break;
                }

            } catch (error) {
            }

            if(mute == 1) {
                $(".group_mute").attr("src", "../../public/img/msg/icon_switch_on.png");
                $(".group_mute").attr("is_on", "on");
                $(".room-chatsession-mute_"+groupProfile.id)[0].style.display = "block";
            } else {
                $(".group_mute").attr("src", "../../public/img/msg/icon_switch_off.png");
                $(".group_mute").attr("is_on", "off");
                $(".room-chatsession-mute_"+groupProfile.id)[0].style.display = "none";
            }

            var canGuestReadMsg = groupProfile != false && groupProfile != null ? groupProfile.canGuestReadMessage : 0;

            if(canGuestReadMsg == 1) {
                $(".can_guest_read_message").attr("src", "../../public/img/msg/icon_switch_on.png");
                $(".can_guest_read_message").attr("is_on", "on");
            } else {
                $(".can_guest_read_message").attr("src", "../../public/img/msg/icon_switch_off.png");
                $(".can_guest_read_message").attr("is_on", "off");
            }

        }
        try{
            $("."+chatSessionId).addClass("chatsession-row-active");
        }catch (error) {

        }
        displayRightPage(DISPLAY_CHAT);
    }catch (error){
        // console.log(error.message)
    }
}


$(document).mouseup(function(e){
    var targetId = e.target.id;
    var targetClassName = e.target.className;

   try{
       if(targetId == "wrapper-mask") {
           var wrapperMask = document.getElementById("wrapper-mask");
           var length = wrapperMask.children.length;
           var i;
           for(i=0;i<length; i++) {
               var node  = wrapperMask.children[i];
               node.remove();
               addTemplate(node);
           }
           wrapperMask.style.visibility = "hidden";
       }
       ////隐藏群组点击头像之后的弹出菜单
       if(targetClassName != "group-user-img" && targetClassName != "item p-2") {
           hideGroupUserMenu();
       }

       if(targetClassName != "emotion-item") {
           document.getElementById("emojies").style.display = "none";
       }
       if(targetClassName != "gif") {
           document.getElementById("chat_plugin").style.display = "none";
       }
       if(targetClassName.indexOf("siteSelfInfo") == -1) {
           $("#selfInfo").remove();
       }

   }catch (error) {

   }
});

$(document).mousedown(function (e) {
    try{
        if($("#msg-menu").length > 0 && e.target.className.indexOf("item") == -1) {
            $("#msg-menu").remove();
            return false;
        }
    }catch (error) {

    }

});


function hideGroupUserMenu()
{
    var groupUserMenu = document.getElementById("group-user-menu");
    if(groupUserMenu) {
        groupUserMenu.remove();
        addTemplate(groupUserMenu);
    }
}

$(document).on("click", ".edit-remark", function () {
    var userId = localStorage.getItem(chatSessionIdKey);
    $("#edit-remark").attr("userId", userId);
    var userProfile = getFriendProfile(userId, false, handleGetFriendProfile);
    if(userProfile) {
        $(".remark_name").val(userProfile['nickname']);
    }
    showWindow($('#edit-remark'));
});

/// update group Profile
$(document).on("click", ".permission-join", function () {
    var groupId = localStorage.getItem(chatSessionIdKey);
    var currentGroupProfileJson = localStorage.getItem(profileKey+groupId);
    var currentGroupProfile  = JSON.parse(currentGroupProfileJson);
    var permissionJoin = currentGroupProfile.permissionJoin;

    var imgDivs = $(".imgDiv");
    var length = imgDivs.length;
    for(i=0;i<length;i++){
        var node = imgDivs[i];
        if($(node).attr("permissionJoin") == permissionJoin) {
            $(node).attr("src",  "../../public/img/msg/member_select.png");
            $(node).addClass("permission-join-select");
        } else  {
            $(node).attr("src",  "../../public/img/msg/member_unselect.png");
            $(node).removeClass("permission-join-select");
        }
    }

    showWindow($("#permission-join"));
});

$(document).on("click", ".mark_down", function () {

    var isMarkDown = $(".mark_down").attr("is_on");
    if(isMarkDown == "on") {
        $(".mark_down").attr("is_on", "off");
        $(".mark_down").attr("src", "../../public/img/msg/icon_switch_off.png");
    } else {
        $(".mark_down").attr("is_on", "on");
        $(".mark_down").attr("src", "../../public/img/msg/icon_switch_on.png");
    }
});

$(document).on("click", ".imgDiv", function () {
    var imgDivs = $(".imgDiv");
    var length = imgDivs.length;
    for(i=0;i<length;i++){
        var node = imgDivs[i];
        $(node).attr("src", "../../public/img/msg/member_unselect.png");
        $(node).removeClass("permission-join-select");
    }
    $(this).attr("src",  "../../public/img/msg/member_select.png");
    $(this).addClass("permission-join-select");
});

$(document).on("click", ".more-info", function () {

    var chatSessionId = localStorage.getItem(chatSessionIdKey);
    sendFriendProfileReq(chatSessionId, handleFriendMoreInfo);
});

function handleFriendMoreInfo(result) {
    if (result == undefined) {
        return;
    }
    var profile = result.profile;
    var customs = [];

    if (profile != undefined && profile["profile"]) {
        try {
            if (profile.hasOwnProperty("custom")) {
                 customs = profile['custom'];
            }
        } catch (error) {
        }
    }

    var html = template("tpl-friend-profile-more-info", {
        customs: customs
    });
    html = handleHtmlLanguage(html);
    $("#more-info").html(html);

    showWindow($("#more-info"));
    var trueFriendMoreInfoHeight = $("#more-info")[0].clientHeight -  $(".more-info-title")[0].style.clientHeight;
    if($(".friend_more_info")[0].clientHeight < trueFriendMoreInfoHeight) {
        $(".friend_more_info")[0].style.overflowY = "hidden";
    }
    $(".friend_more_info")[0].style.height =  trueFriendMoreInfoHeight+"px";

}


//添加好友
$(document).on("click", ".add-friend-btn", function(){
    var userId = localStorage.getItem(chatSessionIdKey);
    $("#add-friend-div").attr("userId", userId);
    sendFriendProfileReq(userId, displayAddFriend);
});

$(document).on("click", "#add-friend", function () {
    var node = $(this)[0].parentNode;
    var userId = $(node).attr("userId");
    sendFriendProfileReq(userId, displayAddFriend);
});

function displayAddFriend(result)
{
    handleGetFriendProfile(result);
    if(result == undefined) {
        return;
    }
    var profile = result.profile;

    if(profile != undefined && profile["profile"]) {
        var friendProfile = profile["profile"];
        $("#add-friend-div").attr("userId", friendProfile.userId);
        var html = template("tpl-add-friend-div", {
            nickname: friendProfile.nickname,
            userId : friendProfile.userId,
        });
        $("#add-friend-div").html(html);
        getNotMsgImg(friendProfile.userId, friendProfile.avatar);
        showWindow($('#add-friend-div'));
    }
}



////好友申请

applyFriendListOffset = 0;

$(document).on("click", ".apply-friend-list", function () {
    addActiveForPwContactRow($(this));
    var tip = languageNum == $.i18n.map['newFriendsTip'] != undefined? $.i18n.map['newFriendsTip'] : "好友申请";
    $(".title").html(tip);
    applyFriendListOffset = 0;
    getFriendApplyList();

});

$(document).on("click", ".search-user", function () {
    addActiveForPwContactRow($(this));
    var html = template("tpl-search-user-div", {});
    html = handleHtmlLanguage(html);
    $("#search-user-div").html(html);
    showWindow($("#search-user-div"));
});

function getFriendApplyList()
{
    var action = "api.friend.applyList";
    var reqData = {
        "offset" : 0,
        "count" : defaultCountKey,
    }
    handleClientSendRequest(action, reqData, handleApplyFriendList)
}

$(function () {
    $(".friend-right-body").scroll(function () {
        var pwLeft = $(".friend-right-body")[0];
        var ch  = pwLeft.clientHeight;
        var sh = pwLeft.scrollHeight;
        var st = $('.friend-right-body').scrollTop();

        ////文档的高度-视口的高度-滚动条的高度
        if((sh - ch - st) == 0){
            var action = "api.friend.applyList";
            var reqData = {
                "offset" : applyFriendListOffset,
                "count" : defaultCountKey,
            }
            handleClientSendRequest(action, reqData, getApplyFriendListHtml);
        }
    });
});


function handleApplyFriendList(results)
{
    $(".friend-right-body").html("");
    getApplyFriendListHtml(results);
    var data = $(".l-sb-item-active").attr("data");
    if(data == "friend") {
        displayRightPage(DISPLAY_APPLY_FRIEND_LIST);
    }
    displayRoomListMsgUnReadNum();
}

function getApplyFriendListHtml(results)
{
    var lists = results.list;
    var html = "";
    setFriendListTip(results.totalCount);
    if(lists) {
        applyFriendListOffset = Number(applyFriendListOffset + defaultCountKey);
        var length = lists.length;
        for (i = 0; i < length; i++) {
            var applyInfo = lists[i];
            var user = applyInfo.public;
            html = template("tpl-apply-friend-info", {
                greetings : applyInfo.greetings,
                userId : user.userId,
                nickname : user.hasOwnProperty('nickname') ? user.nickname : defaultUserName
            });
            html = handleHtmlLanguage(html);
            $(".friend-right-body").append(html);
            getNotMsgImg(user.userId, user.avatar)
        }
    }
}

function handleHtmlLanguage(html)
{
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

    return html;
}



//-----------------------------------api.friend.update---------------------------------------------------------


function friendUpdate(userId, value)
{
    var values = new Array();
    values.push(value);
    var reqData = {
        userId : userId,
        values : values
    };
    var action = 'api.friend.update';
    handleClientSendRequest(action, reqData, handleGetFriendProfile);
}

//update friend mute
$(document).on("click", ".friend_mute", function () {
    var userId = localStorage.getItem(chatSessionIdKey);
    var mute = $(".friend_mute").attr("is_on");
    clearRoomUnreadMsgNum(userId);
    if(mute == "on") {
        $(".friend_mute").attr("is_on", "off");
        $(".friend_mute").attr("src", "../../public/img/msg/icon_switch_off.png");
        mute = false;
    } else {
        $(".friend_mute").attr("is_on", "on");
        $(".friend_mute").attr("src", "../../public/img/msg/icon_switch_on.png");
        mute = true;
    }
    var value = {
        type :ApiFriendUpdateType.ApiFriendUpdateIsMute,
        isMute : mute,
    }
    friendUpdate(userId, value);
});

//update friend remark name
$(document).on("click", ".edit_remark_for_friend", function () {
    editFriendRemark();
});

function editFriendRemarkByKeyDown(event) {
    if(!checkIsEnterBack(event)) {
        return false;
    }
    editFriendRemark();
}


function editFriendRemark()
{
    var remarkName = $(".remark_name").val();
    var userId = $("#edit-remark").attr("userId");
    var value = {
        type :ApiFriendUpdateType.ApiFriendUpdateRemark,
        remark : remarkName,
    }
    removeWindow($("#edit-remark"));
    friendUpdate(userId, value);
}



//-------------------------------------self qrcode-------------------------------------------------------

function handleGetUserProfile(result)
{
    var customs = new Array();
    if(result && result.hasOwnProperty("profile") ) {
        var profile = result['profile'];
        if(profile.hasOwnProperty("custom")) {
            customs = profile.custom;
        }
    }
    try{
        $("#selfInfo").remove();
    }catch (error) {

    }
    var finishTip = getLanguage() == 1? '完成': "finish";
    var isMaster = isJudgeSiteMasters(token);
    var html = template("tpl-self-info", {
        userId:token,
        avatar:getNotMsgImgUrl(avatar),
        nickname:profile['public'].nickname,
        loginName:loginName,
        isMaster:isMaster,
        customs:customs,
        finishTip:finishTip
    });
    // try{
    //     hideLoading();
    // }catch (error){
    // }
    html = handleHtmlLanguage(html);
    $(".wrapper").append(html);
}

function getSelfInfo()
{
    var action = "api.user.profile"
    handleClientSendRequest(action, {}, handleGetFriendProfile);
}

getSelfInfo();

////展示个人消息
function displaySelfInfo()
{
    // try{
    //     showMiniLoading($(".l-sb-item-selfInfo"));
    // }catch (error){
    // }
    var action = "api.user.profile"
    handleClientSendRequest(action, {}, handleGetUserProfile);
}


$(document).on("click", ".sound_mute", function () {
    var type = $(this).attr("is_on");
    var switchOnSrc = siteAddress+"/public/img/msg/icon_switch_on.png";
    var switchOffSrc = siteAddress+"/public/img/msg/icon_switch_off.png";

    if(type == "off") {
        $(this).attr("src", switchOnSrc);
        $(this).attr("is_on", 'on');
        localStorage.setItem(soundNotificationKey, "on");
    }else {
        $(this).attr("src", switchOffSrc);
        $(this).attr("is_on", 'off');
        localStorage.setItem(soundNotificationKey, "off");
    }
});

$(document).on("click", ".selfInfo", function () {
    displaySelfInfo();
});

$(".selfInfo").mouseover(function(){
    displaySelfInfo();
}).mouseout(function () {
    try{
        hideLoading();
    }catch (error) {

    }
});

$(document).on("mouseleave","#selfInfoDiv", function () {
    if( isSelfInfoCanHidden == true) {
        removeWindow($("#selfInfo"));
    }
});

$(document).on("click", "#self-qrcode", function () {
    getSelfQrcode();
});

function getSelfQrcode() {
    $("#selfQrcodeDiv")[0].style.display = 'block';
    $("#selfInfoDiv")[0].style.display = 'none';

    $("#selfQrcodeCanvas").html("");
    var src = $(".selfInfo").attr("src");
    var urlLink = changeZalySchemeToDuckChat(token, "u");
    $("#selfQrcodeCanvas").attr("urlLink", urlLink);
    generateQrcode($('#selfQrcodeCanvas'), urlLink, src, true , "self");
}

//-------------------------------------api.user.update-------------------------------------------------------

function updateSelfNickName(event)
{
    if(checkIsEnterBack(event) == false) {
        return;
    }
    var nickname = $(".nickname").val();
    if(nickname.length > 16) {
        alert("长度16个字符");
        return;
    }
    var values = new Array();
    var value = {
        type : ApiUserUpdateType.ApiUserUpdateNickname,
        nickname : nickname,
    };
    values.push(value);
    updateUserInfo(values);
}

function updateUserInfo(values)
{
    var reqData = {
        values : values
    };
    var action = "api.user.update";
    handleClientSendRequest(action, reqData, handleUpdateUserInfo);
}


function handleUpdateUserInfo(results)
{
    if(results && results.hasOwnProperty("profile")) {
        var public = results['profile'].public;
        avatar = public['avatar'];
        var url = getNotMsgImgUrl(avatar);
        $(".info-avatar-"+public['userId']).attr("src", url);
        nickname = public['nickname'];
        var nicknameHtml = template("tpl-string", {
            string:nickname
        });
        var html ='<div style="margin-left: 1rem;" class="nickNameDiv siteSelfInfo">'+nicknameHtml+'<img src="./public/img/edit.png" style="width: 1rem;height:1rem"></div>';

        $(".nickname_"+public['userId']).html(nicknameHtml);
        $(".editSelfNickNameDiv").html(html);
        getNotMsgImg(token, avatar);
    }
}

$(document).on("click", ".nickNameDiv",function () {
    var html = template("tpl-nickname-div", {
        nickname:nickname
    });
    $(this)[0].parentNode.replaceChild($(html)[0], $(this)[0]);
});

// self_custom_edit_info

function updateUserCustomInfo(event, jqElement)
{
    // var isEnter = checkIsEnterBack(event);
    // if(!isEnter) {
    //     return;
    // }
    //
    // var customKey = $(jqElement).attr("customKey");
    // var customValue = $(jqElement).val();
    // var customName = $(jqElement).attr("customName");
    //
    // var customInfo = {
    //     "customKey":customKey,
    //     "customValue":customValue,
    //     "customName":customName
    // }
    //
    // var values = new Array();
    // var value = {
    //     type : ApiUserUpdateType.ApiUserUpdateCustom,
    //     custom : customInfo,
    // };
    // values.push(value);
    // updateUserInfo(values);
}

function editSelfCustom(type)
{
    if(type == 'edit') {
        isSelfInfoCanHidden = false;
        $("#selfInfoDiv")[0].style.display = "none";
        $("#selfCutsomInfoDiv")[0].style.display = "block";
        var customHeight =  $("#selfCutsomInfoDiv")[0].scrollHeight;
        var layoutLeftHeight = $(".layout-left")[0].clientHeight;
        if(layoutLeftHeight-customHeight>30) {
            $("#selfCutsomInfoDiv")[0].style.overflowY = "hidden";
        }
        $("#selfCutsomInfoDiv")[0].style.height = customHeight+"px";
        $("#selfInfo")[0].style.height = customHeight+"px";

    } else {
        var values = new Array();

        $(".edit_custom_info").each(function (index, target) {
            var customKey = $(target).attr("customKey");
            var customValue = $(target).val();
            var customName = $(target).attr("customName");

            var customInfo = {
                "customKey":customKey,
                "customValue":customValue,
                "customName":customName
            }

            var value = {
                type : ApiUserUpdateType.ApiUserUpdateCustom,
                custom : customInfo,
            };
            values.push(value);
        });
        updateUserInfo(values);
        $("#selfInfoDiv")[0].style.display = "block";
        $("#selfInfoDiv")[0].style.height = "20rem";
        $("#selfInfo")[0].style.height = "20rem";
        $("#selfCutsomInfoDiv")[0].style.display = "none";
        isSelfInfoCanHidden = true;
    }
}
//------------------------------------api.friend.delete--------------------------------------------------------

$(document).on("click", ".delete-friend", function () {
    var tip = $.i18n.map['deleteFriendJsTip'] != undefined ? $.i18n.map['deleteFriendJsTip']: "确定要删除好友么?";
    if(confirm(tip)){
        var userId = localStorage.getItem(chatSessionIdKey);
        var action = "api.friend.delete";
        var reqData = {
            toUserId : userId,
        };
        handleClientSendRequest(action, reqData, handleFriendDelete(userId))
    };
});

function handleFriendDelete(userId)
{
    var relation = friendRelationKey+userId;
    localStorage.setItem(relation, FriendRelation.FriendRelationFollowForWeb);
    displayCurrentProfile();
}


$(document).on("click", "#selfQrcode", function () {
    downloadImgFormQrcode("selfQrcode");
});


$(document).on("click", ".web-msg-click", function(){
    var url = $(this).attr("src-data");
    window.open(url);
});



//---------------------------------------api.friend.search------------------------------------------------
///解决回车和失去焦点冲突
var isSearchUser=false;
function searchUserByKeyDown(event)
{
    if(checkIsEnterBack(event) == false) {
        return;
    }
    isSearchUser = true;
    searchUser();
}
$(document).on("input porpertychange", ".search-user-input", function () {
    isSearchUser = false;
});

function searchUserByOnBlur() {
    if(isSearchUser == true) {
        return;
    }
    searchUser();
}

function searchUser() {
    var searchValue = $(".search-user-input").val();
    if(searchValue.length<1) {
        return;
    }
    var action = "api.friend.search";
    var reqData = {
        keywords:searchValue,
        offset:0,
        count:defaultCountKey
    };
    handleClientSendRequest(action, reqData, handleSearchUser);
    $(".search-user-content").html('');
}

function handleSearchUser(results)
{
    if(results.hasOwnProperty("friends")) {
        var friends = results.friends;
        var friendsLength = friends.length;
        for(var i=0; i<friendsLength; i++) {
            var friendProfile = friends[i].profile;
            var html = template("tpl-search-user-info", {
                nickname:friendProfile.nickname,
                userId:friendProfile.userId,
                token:token
            });
            $(".search-user-content").append(html);
            getNotMsgImg(friendProfile.userId, friendProfile.avatar);
        }
    } else {
        var html = template("tpl-search-user-info-void", {});
        $(".search-user-content").append(html);
    }
}

//---------------------------------------api.friend.apply------------------------------------------------
function handleFriendApplyReq(type)
{
    if(type == errorFriendIsKey) {
        hanleAddFriendForFriendIs(friendApplyUserId);
        removeWindow($("#search-user-div"));
        return;
    }
}

function hanleAddFriendForFriendIs(friendApplyUserId)
{
    localStorage.setItem(chatSessionIdKey, friendApplyUserId);
    localStorage.setItem(friendApplyUserId, U2_MSG);
    insertU2Room(undefined, friendApplyUserId);
    sendFriendProfileReq(friendApplyUserId, handleGetFriendProfile);
}

// send friend apply by search
$(document).on("click", ".search-add-friend-btn", function () {
    var userId = $(this).attr("userId");
    friendApplyUserId = userId;
    sendFriendApplyReq(userId, "", handleFriendApplyReq);
    $(this).attr("disabled", "disabled");
    $(this)[0].style.backgroundColor = "#cccccc";
});

// send friend apply by click add friend
var friendApplyUserId ;
function sendFriendApplyReq(userId, greetings, callback)
{
    friendApplyUserId = userId;
    var action = "api.friend.apply";
    var reqData  = {
        "toUserId" : userId,
        "greetings" : greetings,
    };
    handleClientSendRequest(action, reqData, callback)
}


function handleApplyFriend(type)
{
    removeWindow($("#add-friend-div"));
    if(type == errorFriendIsKey) {
        hanleAddFriendForFriendIs(friendApplyUserId);
    }
}

function applyFriend() {
    var userId = $("#add-friend-div").attr("userId");
    var greetings = $(".apply-friend-reason").val();
    sendFriendApplyReq(userId, greetings, handleApplyFriend);
}

$(document).on("click", ".apply-friend", function () {
    $(this)[0].style.disabled = "disabled";
    applyFriend();
});

function addFriendByKeyDown(event)
{
    if(checkIsEnterBack(event)) {
        applyFriend();
    }
}


function closeMaskDiv(str)
{
    removeWindow($(str));
}
//---------------------------------------api.friend.accept------------------------------------------------


function handleFriendApplyAccept(jqElement)
{
    jqElement[0].parentNode.parentNode.parentNode.parentNode.remove();
    deleteFriendListTip();
}


function friendApplyAccept(jqElement, agree)
{
    var userId = jqElement.attr("userId");
    var action = "api.friend.accept";
    var reqData = {
        applyUserId : userId,
        agree : agree
    };
    handleClientSendRequest(action, reqData, handleFriendApplyAccept(jqElement));
}
//refused apply
$(document).on("click", ".refused-apply", function () {
    var node =  $(this)[0].parentNode;
    var tip = $.i18n.map['refuseFriendJsTip'] != undefined ? $.i18n.map['refuseFriendJsTip']: "确定拒绝对方?";
    if(confirm(tip)) {
        friendApplyAccept($(node), false);
    }
});

//agreed apply
$(document).on("click", ".agreed-apply", function () {
    var node =  $(this)[0].parentNode;
    var tip = $.i18n.map['agreeFriendJsTip'] != undefined ? $.i18n.map['agreeFriendJsTip']: "确定同意对方的好友申请?";
    if(confirm(tip)) {
        friendApplyAccept($(node), true);
    }
});



//---------------------------------------*******Msg*******-------------------------------------------------
//click chat room
$(document).on("click", ".chatsession-row", function(){
    var roomType = $(this).attr("roomType");
    var chatSessionId = $(this).attr("chat-session-id");
    localStorage.setItem(chatSessionIdKey, chatSessionId);

    $(".right-chatbox").attr("chat-session-id", chatSessionId);
    if(roomType == U2_MSG) {
        localStorage.setItem(chatSessionId, U2_MSG);
        getFriendProfileByClickChatSessionRow($(this));
    } else if(roomType == GROUP_MSG) {
        localStorage.setItem(chatSessionId, U2_MSG);
        getGroupProfileByClickChatSessionRow($(this));
    }
    getInitChatPlugin(roomType);
    updateRoomChatSessionContent(chatSessionId);
    addActiveForRoomList($(this));

});




// click msg image , open a new window
$(document).on("click", ".msg_img", function () {
    var src = $(this).attr("src");
    window.open(src);
});


//---------------------------------------http.file.downloadFile-------------------------------------------------
function downloadFile(elementObject) {
    var fileId = elementObject.attr("url");
    var msgId = elementObject.attr("msgId");
    var originName = elementObject.attr("originName");
    var currentRoom = localStorage.getItem(chatSessionIdKey);
    var isGroupMessage = localStorage.getItem(currentRoom) == GROUP_MSG ? 1 : 0;
    var requestUrl = downloadFileUrl +  "&fileId="+fileId + "&returnBase64=0&isGroupMessage="+isGroupMessage+"&messageId="+msgId+"&lang="+languageNum;
    requestUrl = encodeURI(requestUrl);
    var downloadLink = document.createElement('a');
    downloadLink.download = originName;
    downloadLink.href =requestUrl;
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

$(document).on("click", ".right_msg_file_div", function () {
    downloadFile($(this));

});

$(document).on("click", ".left_msg_file_div", function () {
    downloadFile($(this));
});

//---------------------------------------msg emotion-------------------------------------------------

$(document).on("click", ".emotions", function () {
    document.getElementById("emojies").style.display = "block";
});

$(document).on("click", ".emotion-item", function () {
    var  html = $(this).html();
    var htmls = $(".msg_content").val() + html;
    $(".msg_content").val(htmls);
});

//window 7 一下暂时不支持emotion
function checkOsVersion()
{
    var userAgent = navigator.userAgent;
    if(userAgent.indexOf("Windows") != -1 && ((userAgent.indexOf("Windows NT 5") != -1)
            || (userAgent.indexOf("Windows NT 6") != -1) || (userAgent.indexOf("Windows NT 7") != -1) )) {
        try{
            $(".emotions")[0].style.display = "none";
        }catch (error) {

        }
    }
}

//---------------------------------------msg dialog-------------------------------------------------

$(document).on("click", ".clear_room_chat", function () {
    var roomId = localStorage.getItem(chatSessionIdKey);
    var tip = languageNum == UserClientLangZH ?  "将删除聊天记录，确认？" : "Sure?" ;
    if(confirm(tip)) {
        clearRoomMsgFromRoomList(roomId);
       try{
           $(".msg-row").each(function (index, target) {
               $(target).remove();
           });
       }catch (error) {

       }
    }
});

function handleMsgRelation(jqElement, chatSessionId)
{
    if(jqElement != undefined) {
        addActiveForPwContactRow(jqElement);
    }
    hideGroupUserMenu();
    getMsgFromRoom(chatSessionId);
    syncMsgForRoom();
    displayCurrentProfile();
}

function judgeDefaultChat()
{
    var chatType = localStorage.getItem(chatTypeKey);
    if(chatType != DefaultChat && chatType != null) {
        return false;
    }
    return true;
}

function displayRightPage(displayType)
{
    try{
        switch (displayType){
            case DISPLAY_HOME:
                $(".plugin-list-dialog")[0].style.display = "block";
                $(".msg-chat-dialog")[0].style.display = "none";
                $(".friend-apply-dialog")[0].style.display = "none";
                break;
            case DISPLAY_CHAT:
                $(".friend-apply-dialog")[0].style.display = "none";
                $(".plugin-list-dialog")[0].style.display = "none";
                var chatSessionId  = localStorage.getItem(chatSessionIdKey);
                var chatSessionRowLength = $(".chatsession-row").length;
                $(".msg-chat-dialog")[0].style.display = "block";
                if(chatSessionId && chatSessionRowLength>0) {
                    $(".chat-dialog")[0].style.display = "block";
                    $(".no-chat-dialog-div")[0].style.display = "none";
                    $(".right-chatbox").attr("chat-session-id", chatSessionId);
                } else {
                    $(".no-chat-dialog-div")[0].style.display = "block";
                    $(".chat-dialog")[0].style.display = "none";
                }
                $(".msg_content").focus();

                displayWaterMark();
                checkOsVersion();
                break;
            case DISPLAY_APPLY_FRIEND_LIST:
                $(".msg-chat-dialog")[0].style.display = "none";
                $(".friend-apply-dialog")[0].style.display = "block";
                $(".plugin-list-dialog")[0].style.display = "none";

                break;
        }
    }catch (error) {
        // console.log(error.message);
    }
}

function displayWaterMark()
{

   try{
       var configStr = localStorage.getItem(siteConfigKey);
       var config = JSON.parse(configStr);
        var chatSessionId = localStorage.getItem(chatSessionIdKey);
       if(config.hasOwnProperty("openWaterMark") && config['openWaterMark']) {
           var time = Date.parse(new Date()) / 1000;
           //前10位
           var suffixToken = token.substr(0, 10);
           var suffixChatsessionId = chatSessionId.substr(0,10)
           var params =  suffixToken +" "+suffixChatsessionId+" "+time;
           var data = { watermark_txt:params, watermark_width:60, watermark_y_space:30, watermark_x_space:30 }

           try{
               $("#otdivid").remove();
           }catch (error) {

           }
           watermark.load(data, $(".right-body-chat"), $(".right-chatbox"));
       }

   }catch (error)  {
   }
}

$(".input-box").on("click",function () {
    $(".msg_content").focus();
});

function addActiveForPwContactRow(jqElement)
{
    var pwContactRows = $(".pw-contact-row");
    var length = pwContactRows.length;
    for(i=0;i<length;i++){
        var node = pwContactRows[i];
        $(node).removeClass("chatsession-row-active");
    }
    jqElement.addClass("chatsession-row-active");
}

function addActiveForRoomList(jqElement)
{
    var chatSessionRowNodes = $(".chatsession-row");
    var length = chatSessionRowNodes.length;
    var i;
    for(i=0;i<length;i++){
        var node = chatSessionRowNodes[i];
        $(node).removeClass("chatsession-row-active");
    }
    jqElement.addClass("chatsession-row-active");
}


//---------------------------------------send msg -------------------------------------------------

$(document).on("click", ".send_msg" , function(){
    sendMsgBySend();
});



//发送消息
function sendMsgBySend()
{
    var chatSessionId   = localStorage.getItem(chatSessionIdKey);
    var chatSessionType = localStorage.getItem(chatSessionId);
    var msgContent = $(".msg_content").val();
    var imgData = $("#msgImage img").attr("src");
    $("#msgImage").html("");
    $("#msgImage")[0].style.display = "none";
    if(imgData) {
        uploadMsgImgFromCopy(imgData);
    }

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

//粘贴图片
document.getElementById("msg_content").addEventListener('paste', function(event) {
   try{
       var imgFile = null;
       var idx;
       var items = event.clipboardData.items;
       if(items == undefined) {
           return;
       }
       for(var i=0,len=items.length; i<len; i++) {
           var item = items[i];
           if (item.kind == 'file' ||item.type.indexOf('image') > -1) {
               var blob = item.getAsFile();
               var reader = new FileReader();
               reader.onload = function(event) {
                   var data = event.target.result;
                   var img = new Image();
                   img.src = data;
                   img.onload =  function (ev) {
                       autoMsgImgSize(img, 400, 300);
                   };
                   document.getElementById("msgImage").style.display = "block";
                   document.getElementById("msgImage").appendChild(img);
                   return false;
               }; // data url!
               reader.readAsDataURL(blob);
           }
       }
   }catch (error){

   }
});

function sendMsgByKeyDown(event)
{

    var isIE = (document.all) ? true : false;
    var key;
    if(isIE) {
        key = window.event.keyCode;
    } else {
        key = event.which;
    }
    if(key ==8 || key == 46) {
        $("#msgImage").html("");
    }
    if(key == 13) {
        sendMsgBySend();
        event.preventDefault();
    }

}

function sortRoomList(jqElement)
{
    var chatSessionRows = $(".chatsession-row");
    var chatSessionRowsLength = chatSessionRows.length;
    var i;
    for(i=0; i<chatSessionRowsLength; i++) {
        var node = chatSessionRows[i];
        $(node).removeClass("chatsession-row-up");
    }

    jqElement.addClass("chatsession-row-up");

    var activeNode = $(".chatsession-row-up");
    var activeNum = 0;
    var i;
    for(i=0; i<chatSessionRowsLength; i++) {
        var node = chatSessionRows[i];
        if($(node).hasClass("chatsession-row-up")) {
            activeNum = i;
            if(activeNum != 0) {
                $(node).remove();
            }
        }
    }
    if(activeNum != 0) {
        $(activeNode).insertBefore($(".chatsession-row")[0]);
    }
}

$(document).bind("contextmenu", ".msg_content_for_click", function(event){
   try{
       var msgId = $(event.target).attr("msgId");
       var msgType = $(event.target).attr("msgType");
       var sendtime = $(event.target).attr("sendtime");
       var userId = $(event.target).attr("userId");

       var trueTarget = $(event.target)

       if(msgId == undefined) {
           trueTarget = '';
           var findNode = false;
           var targets = $(event.target).parents();
           targets.each(function (index, target) {
               if($(target).hasClass("msg_content_for_click")) {
                   msgId = $(target).attr("msgId");
                   msgType = $(target).attr("msgType");
                   sendtime = $(target).attr("sendtime");
                   userId = $(target).attr("userId");
                   trueTarget = target;
               }
           });
       }

       if(!trueTarget) {
           return false;
       }
       var currentChatSessionId = localStorage.getItem(chatSessionIdKey);
       var chatSessionType = localStorage.getItem(currentChatSessionId);


       try{
           $("#msg-menu")[0].remove();
       }catch (error) {
       }
       var clientX = event.offsetX;
       var clientY = event.offsetY;

       if(msgId == undefined) {
           return false;
       }
       var isCopy = false;
       var isSave = false;
       var isRecall = false;
       var isSee = false;
       var recallDisabled = false;

       var nowTime =  Date.now();

       //两分钟内的允许撤回
       if(userId == token ) {
           isRecall = true;
           if(nowTime-sendtime > 120000) {
               recallDisabled = true
           }
       }else {
           if(chatSessionType == GROUP_MSG) {
               var groupProfileStr = localStorage.getItem(profileKey+currentChatSessionId);
               var groupProfile = JSON.parse(groupProfileStr);
               var isAdmin = checkGroupAdminContainOwner(token, groupProfile);
               if(isAdmin) {
                   isRecall = true;
               }
           }
       }

       switch (msgType) {
           case MessageType.MessageText:
               isCopy = true;
               break;
           case MessageType.MessageDocument:
               isSave = true;
               break;
           case MessageType.MessageImage:
               isSee = true;
               break;
       }

       var html = template("tpl-msg-menu", {
           msgId : msgId,
           isCopy:isCopy,
           isSave:isSave,
           isRecall:isRecall,
           isSee:isSee,
           left:clientX,
           top:clientY,
           recallDisabled:recallDisabled
       });
       html = handleHtmlLanguage(html);
       $(trueTarget).append(html);
   }catch (error) {
   }
    return false;
});

function copyMsg( msgId, event) {
    event.preventDefault();
    event.stopPropagation();

    try{
        $("#msg-menu")[0].remove();
    }catch (error) {
    }

    try{
        $(".msg_content_for_click_"+msgId)[0].onclick = function () {
            document.execCommand('copy');
        }

        $(".msg_content_for_click_"+msgId)[0].addEventListener('copy', function (e) {
            var value = $(this).find("pre").html();
            value = trimMsgContentNewLine(value);
            e.preventDefault();
            if (e.clipboardData) {
                e.clipboardData.setData('text/plain', value);
            } else if (window.clipboardData) {
                window.clipboardData.setData('Text', value);
            }
        });

        $(".msg_content_for_click_"+msgId).click();
    }catch (error) {

    }
}


function downloadMsg(msgId, event) {
    event.preventDefault();
    event.stopPropagation();
    var msgType = $(".msg_content_for_click_"+msgId).attr("msgType");
    var msgTime = $(".msg_content_for_click_"+msgId).attr("msgTime");

    try{
        $("#msg-menu")[0].remove();
    }catch (error) {
    }

    switch (msgType) {
        case MessageType.MessageImage:
            break;
        case MessageType.MessageDocument:
            if($(".msg_content_for_click_"+msgId).hasClass("right_msg_file_div")) {
                $(".right_msg_file_div[msgId="+msgId+"]").click();
            } else {
                $(".left_msg_file_div[msgId="+msgId+"]").click();
            }
    }
}

function recallMsg(msgId,event) {
    event.preventDefault();
    event.stopPropagation();

    if(msgId == "") {
        return;
    }
    try{
        $("#msg-menu")[0].remove();
    }catch (error) {
    }


    var chatSessionId = localStorage.getItem(chatSessionIdKey);
    var chatSessionType = localStorage.getItem(chatSessionId)
    var msgText = "此消息被撤回";
    sendRecallMsg(msgId, msgText, chatSessionId, chatSessionType);
}

function seeMsg( msgId,event) {
    event.preventDefault();
    event.stopPropagation();

    try{
        $("#msg-menu")[0].remove();
    }catch (error) {
    }

    var src = $(".msg-img-"+msgId).attr("src");
    window.open(src);
}

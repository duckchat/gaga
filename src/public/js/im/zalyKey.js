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

MessageType = {
    MessageInvalid : "MessageInvalid",
    MessageNotice  : "MessageNotice",
    MessageText    : "MessageText",
    MessageImage   : "MessageImage",
    MessageAudio   : "MessageAudio",
    MessageWeb     : "MessageWeb",
    MessageWebNotice : "MessageWebNotice",
    MessageDocument:"MessageDocument",
    MessageVideo:"MessageVideo",
    MessageRecall:"MessageRecall",

    // event message start
    MessageEventFriendRequest : "MessageEventFriendRequest",
    MessageEventStatus  : "MessageEventStatus",   // -> StatusMessage
    MessageEventSyncEnd :"MessageEventSyncEnd",
};


SetSpeakerType = {
    AddSpeaker    : "AddSpeaker",    //add new speakers
    RemoveSpeaker  : "RemoveSpeaker",    //remove old speakers
    CloseSpeaker  : "CloseSpeaker",    //close speaker function
}

UserClientLangZH = "1";
UserClientLangEN = "0";

FriendRelation = {
    FriendRelationInvalid : "FriendRelationInvalid",
    FriendRelationFollow  : "FriendRelationFollow",
    FriendRelationFollowForGroup : "FriendRelationFollowForGroup",
    FriendRelationFollowForWeb   : "FriendRelationFollowForWeb",
}

MessageStatus  = {
    MessageStatusSending : "MessageStatusSending",
    MessageEventStatus : "MessageEventStatus",
    MessageStatusFailed : "MessageStatusFailed",
    MessageStatusServer : "MessageStatusServer",
    MessageEventSyncEnd : "MessageEventSyncEnd",
}


ApiUserUpdateType  = {
    ApiUserUpdateInvalid  : "ApiUserUpdateInvalid",
    ApiUserUpdateAvatar   : "ApiUserUpdateAvatar",
    ApiUserUpdateNickname :"ApiUserUpdateNickname",
    ApiUserUpdateCustom :"ApiUserUpdateCustom",
}


DataWriteType = {
    WriteUpdate : "WriteUpdate",
    WriteAdd : "WriteAdd",
    WriteDel : "WriteDel"
}

FileType =  {
    FileInvalid : "0",
    FileImage : "1", // the server should find the exactly extension, ex: http://php.net/manual/en/function.mime-content-type.php
    FileAudio : "2", // the server should find the exactly extension, ex: http://php.net/manual/en/function.mime-content-type.php
    FileDocument:"3",
    FileVideo:"4",
}

ApiGroupUpdateType  = {
    ApiGroupUpdateInvalid : "ApiGroupUpdateInvalid",
    ApiGroupUpdateName    : "ApiGroupUpdateName",
    ApiGroupUpdatePermissionJoin : "ApiGroupUpdatePermissionJoin",
    ApiGroupUpdateCanGuestReadMessage : "ApiGroupUpdateCanGuestReadMessage",
    ApiGroupUpdateDescription : "ApiGroupUpdateDescription",
    ApiGroupUpdateAdmin      : "ApiGroupUpdateAdmin",
    ApiGroupUpdateSpeaker     : "ApiGroupUpdateSpeaker",
    ApiGroupUpdateIsMute      : "ApiGroupUpdateIsMute"
}

GroupJoinPermissionType  = {
    GroupJoinPermissionPublic   : "GroupJoinPermissionPublic",
    GroupJoinPermissionMember   : "GroupJoinPermissionMember",
    GroupJoinPermissionAdmin    : "GroupJoinPermissionAdmin",
}

GroupDescriptionType  = {
    GroupDescriptionText : "GroupDescriptionText",
    GroupDescriptionMarkdown : "GroupDescriptionMarkdown",
}

GroupMemberType = {
    GroupMemberGuest    : "GroupMemberGuest",
    GroupMemberNormal   : "GroupMemberNormal",
    GroupMemberAdmin    : "GroupMemberAdmin",
    GroupMemberOwner    : "GroupMemberOwner",
}

ApiFriendUpdateType =  {
    ApiFriendUpdateInvalid  : "ApiFriendUpdateInvalid",
    ApiFriendUpdateRemark   : "ApiFriendUpdateRemark",
    ApiFriendUpdateIsMute   : "ApiFriendUpdateIsMute",
}


PluginUsageType = {
    PluginUsageNone:"PluginUsageNone",
    PluginUsageIndex:"PluginUsageIndex",
    PluginUsageLogin:"PluginUsageLogin",
    PluginUsageU2Message:"PluginUsageU2Message",
    PluginUsageTmpMessage:"PluginUsageTmpMessage",
    PluginUsageGroupMessage:"PluginUsageGroupMessage",
    PluginUsageAccountSafe:"PluginUsageAccountSafe",
}


PluginLoadingType = {
    PluginLoadingNewPage:"PluginLoadingNewPage",
    PluginLoadingFloat:"PluginLoadingFloat",
    PluginLoadingMask:"PluginLoadingMask",
    PluginLoadingChatbox:"PluginLoadingChatbox",
    PluginLoadingFullScreen:"PluginLoadingFullScreen"

};

KeepSocket  = "KeepSocket";
websocketGW = "enable_websocket_gw";
websocketGWUrl = "websocket_gw_url";
apiUrl = "server_address_for_api";

ErrorSessionCode = "error.session";
PageLoginAction  = "page.index";
ErrorSiteInit = "error.site.init";
errorFriendIsKey = "error.friend.is";
errorGroupNotExitsKey = "error.group.notExists";



sessionId = $(".session_id").attr("data");
domain    = $(".domain").attr("data");

siteConfigKeys = {
    logo : "logo",
    name : "name",
    respGW : "respGW",
    httpGW : "httpGW",
    masters : "masters",
    serverAddressForIM : "serverAddressForIM",
    loginPluginId : "loginPluginId",
    enableTmpChat :"enableTmpChat",
    enableRealName : "enableRealName",
    enableWidgetWeb : "enableWidgetWeb",
    siteIdPubkBase64 : "siteIdPubkBase64",
    enableCreateGroup : "enableCreateGroup",
    enableInvitationCode: "enableInvitationCode",
};
siteConfigKey = "site_config";
siteLoginPluginKey = "site_login_plugin";

// CONNECTING：值为0，表示正在连接。
// OPEN：值为1，表示连接成功，可以通信了。
// CLOSING：值为2，表示连接正在关闭。
// CLOSED：值为3，表示连接已经关闭，或者打开连接失败。

WS_CONNTENTING = 0;
WS_OPEN = 1;
WS_CLOSING = 2;
WS_CLOSED = 3;
PACKAGE_ID = "packageId";
lockReconnect = false;

U2_MSG = "MessageRoomU2";
GROUP_MSG = "MessageRoomGroup";
roomKey  = "room_";
roomMsgUnReadNum = "room_msg_unread_num_";
roomListMsgUnReadNum = "room_list_msg_unread_num";
roomListKey = "room_list";
MaxStorageStore=3;


JUMP_U2Profile = "u2Profile";
JUMP_U2Msg = "u2Msg";
JUMP_GroupProfile = "groupProfile";
JUMP_GroupMsg = "groupMsg";

DISPLAY_HOME = "home";
DISPLAY_CHAT = "chat";
DISPLAY_APPLY_FRIEND_LIST = "apply_friend_list";

defaultCountKey = 200;
chatSessionIdKey = "chat_session_id";
localPotiner    = "group_pointer_";
profileKey = "profile_";
friendRelationKey = "user_id_relation_";
friendCustomKey = "user_custom_";
msgMuteKey = "msg_mute_";
msgUnReadMuteKey = "msg_unread_mute_";
roomListMsgMuteUnReadNumKey = "room_list_msg_mute_unread_num";
applyFriendListNumKey = "apply_friend_list_num";
chatTypeKey = "chat_type";
WidgetChat = "widget_chat";
ServiceChat = "service_chat";
DefaultChat = "default_chat";
MobileChat = "mobile_chat";
speakerUserIdsKey="speaker_userIds_";
newSiteTipKey = "site_tip";
defaultPluginDisplay="display_plugin";

DefaultTitle = "DuckChat 聊天室";

////session Storage
userIdsKey  = "user_ids";
groupIdsKey = "group_ids";
sendMsgImgUrlKey = "msg_img_url_";
msgIdInChatSessionKey = "msgId_in_chatSession_";
reqProfile = "req_profile_";
selfInfoAvatar="self_avatar";

uploadImgForMsg  = "uploadImgForMsg";
uploadImgForSelfAvatar = "uploadImgForSelfAvatar";
uploadFileForMsg = "uploadFileForMsg";

ProfileTimeout =  1000*60*60*24*30;////1个月
reqTimeout = 1000*60*5;///5分钟

defaultUserName = "匿名";

downloadFileUrl = "./index.php?action=http.file.downloadFile";

function getLanguage() {
    var nl = navigator.language;
    if ("zh-cn" == nl || "zh-CN" == nl) {
        return UserClientLangZH;
    }
    return UserClientLangEN;
}

function getLanguageName() {
    var nl = navigator.language;
    if ("zh-cn" == nl || "zh-CN" == nl) {
        return "zh";
    }
    return "en";
}

languageName = getLanguageName();
languageNum = getLanguage();

uploadFileUrl = './index.php?action=http.file.uploadWeb';
isSyncingMsg = false;
isPreSyncingMsgTime="";


soundNotificationKey = "sound_notification";
isDisplayFrontPageKey = "is_display_front_page";
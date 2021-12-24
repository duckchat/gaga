<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 10:34 AM
 */

class SiteConfig
{
    const SITE_NAME = "name";
    const SITE_LOGO = "logo";


    const SITE_ENABLE_REAL_NAME = "enableRealName"; //是否实名
    const SITE_ENABLE_INVITATION_CODE = "enableInvitationCode";//邀请码
    const SITE_LOGIN_PLUGIN_ID = "loginPluginId";


    const SITE_ENABLE_CREATE_GROUP = "enableCreateGroup";
    const SITE_MAX_GROUP_MEMBERS = "maxGroupMembers";
    const SITE_ENABLE_ADD_FRIEND_IN_GROUP = "enableAddFriendInGroup";

    const SITE_ENABLE_ADD_FRIEND = "enableAddFriend";
    const SITE_ENABLE_TMP_CHAT = "enableTmpChat";
    const SITE_SUPPORT_PUSH_TYPE = "pushType";


    const SITE_ENABLE_SHARE_GROUP = "enableShareGroup";
    const SITE_ENABLE_SHARE_USR = "enableShareUser";


    const SITE_OPEN_SSL = "openSSL";
    const SITE_OPEN_WEB_EDITION = "openWebEdition";
    const SITE_ENABLE_WEB_WIDGET = "enableWebWidget";
    const SITE_WS_ADDRESS = "wsAddress";
    const SITE_WS_HOST = "wsHost";
    const SITE_WS_PORT = "wsPort";
    const SITE_ZALY_ADDRESS = "zalyAddress";
    const SITE_ZALY_HOST = "zalyHost";
    const SITE_ZALY_PORT = "zalyPort";


    //masters = administrator + managers
    const SITE_MANAGERS = "managers";// json
    const SITE_DEFAULT_FRIENDS = "defaultFriends";//json
    const SITE_DEFAULT_GROUPS = "defaultGroups";//json


    const SITE_ADDRESS_FOR_API = "serverAddressForApi";
    const SITE_ADDRESS_FOR_IM = "serverAddressForIM";
    const SITE_GROUP_QR_CODE_EXPIRE_TIME = "groupQRCodeExpireTime";
    const SITE_MAX_CLIENTS_NUM = "maxClientsNum";

    const SITE_ID = "siteId";
    const SITE_ID_PRIK_PEM = "sitePrikPem";
    const SITE_ID_PUBK_PEM = "sitePubkPem";
    const SITE_OWNER = "owner";

    const SITE_PLUGIN_PLBLIC_KEY = "pluginPublicKey";
    const SITE_PASSPORT_ACCOUNT_SAFE_PLUGIN_ID = "passportAccountSafePluginId";

    const SITE_MOBILE_NUM = "maxMobileNum";//默认1，可修改
    const SITE_WEB_NUM = "maxWebNum";//默认永远1，不可修改

    const SITE_FILE_SIZE = "maxFileSize";//文件支持的大小

    const SITE_FRONT_PAGE = "frontPage";

    const SITE_HIDDEN_HOME_PAGE = "hiddenHomePage";

    const SITE_OPEN_WATERMARK = "openWaterMark";

    public static function getPubkAndPrikPem()
    {
        $pair = ZalyRsa::newRsaKeyPair(2048);

        return [
            self::SITE_ID_PUBK_PEM => $pair[ZalyRsa::$KeyPublicKey],
            self::SITE_ID_PRIK_PEM => $pair[ZalyRsa::$KeyPrivateKey]
        ];
    }


    public static $configKeys = [
        self::SITE_NAME,
        self::SITE_LOGO,


        self::SITE_ENABLE_REAL_NAME,
        self::SITE_ENABLE_INVITATION_CODE,
        self::SITE_LOGIN_PLUGIN_ID,

        self::SITE_ENABLE_ADD_FRIEND,
        self::SITE_ENABLE_TMP_CHAT,
        self::SITE_ENABLE_CREATE_GROUP,
        self::SITE_MAX_GROUP_MEMBERS,
        self::SITE_SUPPORT_PUSH_TYPE,
        self::SITE_ENABLE_ADD_FRIEND_IN_GROUP,


        self::SITE_ENABLE_SHARE_GROUP,
        self::SITE_ENABLE_SHARE_USR,


        self::SITE_OPEN_SSL,
        self::SITE_OPEN_WEB_EDITION,
        self::SITE_ENABLE_WEB_WIDGET,
        self::SITE_WS_ADDRESS,
//        self::SITE_WS_PORT,
        self::SITE_ZALY_PORT,

        self::SITE_MANAGERS,
        self::SITE_DEFAULT_FRIENDS,
        self::SITE_DEFAULT_GROUPS,


        self::SITE_ADDRESS_FOR_API,
        self::SITE_ADDRESS_FOR_IM,
        self::SITE_GROUP_QR_CODE_EXPIRE_TIME,
        self::SITE_MAX_CLIENTS_NUM,

        self::SITE_OWNER,

        self::SITE_PLUGIN_PLBLIC_KEY,

        self::SITE_MOBILE_NUM,
        self::SITE_WEB_NUM,
        self::SITE_FILE_SIZE,

        self::SITE_FRONT_PAGE,
        self::SITE_HIDDEN_HOME_PAGE,
        self::SITE_OPEN_WATERMARK,
    ];

    public static $numericKeys = [
        self::SITE_MAX_GROUP_MEMBERS,
        self::SITE_WS_PORT,
        self::SITE_ZALY_PORT,
        self::SITE_MOBILE_NUM,
        self::SITE_WEB_NUM,
        self::SITE_FILE_SIZE,
    ];

    public static $initSiteConfig = [
        self::SITE_NAME => "duckchat-site",
        self::SITE_LOGO => "",

        self::SITE_LOGIN_PLUGIN_ID => 0,

        self::SITE_ENABLE_CREATE_GROUP => 1,
        self::SITE_ENABLE_ADD_FRIEND_IN_GROUP => 1,
        self::SITE_GROUP_QR_CODE_EXPIRE_TIME => 0,
        self::SITE_MAX_GROUP_MEMBERS => 100,

        self::SITE_ENABLE_ADD_FRIEND => 1,
        self::SITE_ENABLE_TMP_CHAT => 1,
        self::SITE_ENABLE_INVITATION_CODE => 0,
        self::SITE_ENABLE_REAL_NAME => 0,


        self::SITE_OPEN_SSL => 0,
        self::SITE_OPEN_WEB_EDITION => 1,
        self::SITE_ENABLE_WEB_WIDGET => 0,
//        self::SITE_WS_HOST => "",
//        self::SITE_WS_PORT => 0,
//        self::SITE_ZALY_HOST => "",
//        self::SITE_ZALY_PORT => 0,

        self::SITE_ID_PUBK_PEM => "",
        self::SITE_ID_PRIK_PEM => "",

        self::SITE_SUPPORT_PUSH_TYPE => Zaly\Proto\Core\PushType::PushNotificationOnly,
        self::SITE_MAX_CLIENTS_NUM => 200,

        self::SITE_DEFAULT_FRIENDS => "",
        self::SITE_DEFAULT_GROUPS => "",

        self::SITE_ENABLE_SHARE_USR => 1,
        self::SITE_ENABLE_SHARE_GROUP => 1,

        self::SITE_PLUGIN_PLBLIC_KEY => "",
        self::SITE_OWNER => "",
        self::SITE_MANAGERS => "",

        self::SITE_MOBILE_NUM => 1,
        self::SITE_WEB_NUM => 1,
        self::SITE_FILE_SIZE => 10, //10M

        self::SITE_OPEN_WATERMARK => 0,
    ];

}
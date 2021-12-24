<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 31/08/2018
 * Time: 6:45 PM
 */

class ZalyText
{
    public static $textGroupNotExists = "text.group.notExists";
    public static $textGroupNotSpeaker = "text.group.notSpeaker";
    public static $textGroupAdminInvite = "text.group.admin.invite";

    //给发送者自己代发的消息，使用这种
    public static $texts = [
        "text.group.notExists" => ["the group has been dissolved", "此群已解散"],
        "text.group.notSpeaker" => ["only speakers and admin can speak,speakers are ", "当前只允许群管理以及发言者发言，发言人者："],
        "upload.file.size" => ["file exceeds maximum limit", "文件超过最大限制"],
        "text.open.web" => ["web is not allowed", "该站点没有开起web版本"],
        "text.group.admin.invite" => ["only allow the group admin or owner", "只允许群主或者管理员邀请好友入群"],
        'text.param.void' => ["mast not null", "不能为空"],
        'text.pwd.exceedNum' => ["Failed, exceed the daily password error limit", "操作失败，超过每日密码错误上限"],
        'text.loginName.MaxLengthLessThanMinLength' => ["The maximum length cannot be less than the minimum length", "用户名最大长度不能小于最小长度"],
        'text.pwd.MaxLengthLessThanMinLength' => ["The maximum length cannot be less than the minimum length", "密码最大长度不能小于最小长度"],
        'text.pwd.minLength' => ["The min mum length is 6", "密码最小长度为6"],
        'text.pwd.maxLength' => ["The max num  length is 32", "密码最大长度为32"],
        'text.pwd.type' => ["Password does not meet requirements", "密码不符合要求"],
        "text.length" => ["length", "长度"],
        "text.login" => ["Login", "登录"],
        "text.register" => ["Register", "注册"],
    ];

    public static $keyGroupCreate = "{key.group.create}";
    public static $keyGroupInvite = "{key.group.invite}";
    public static $keyGroupJoin = "{key.group.join}";
    public static $keyGroupNotMember = "{key.group.notMember}";
    public static $keyGroupDelete = "{key.group.delete}";

    public static $keyDefaultFriendsText = "{key.defaultFriend.text}";
    public static $keyDefaultGroupsText = "{key.defaultGroup.text}";

    public static $keySpeakerSet = "{key.speaker.set}";
    public static $keySpeakerCloseUser = "{key.speaker.closeUser}";
    public static $keySpeakerAsSpeaker = "{key.speaker.asSpeaker}";
    public static $keySpeakerClose = "{key.speaker.close}";
    public static $keySpeakerStatus = "{key.speaker.status}";

    public static $keyFriendAcceptFrom = "{key.friend.acceptFrom}";
    public static $keyFriendAcceptTo = "{key.friend.acceptTo}";

    //需要给对方未知的客户端代发多语言的，使用这里
    public static $templateKeys = [
        "key.group.create" => ["group created,invite your friends to join chat", "群组已创建成功,邀请你的好友加入群聊吧"],
        "key.group.invite" => [" invite ", " 邀请了 "],
        "key.group.join" => [" join this group", " 加入了群聊"],
        "key.group.notMember" => ["you are not group member", "你不是当前群组成员"],
        "key.group.delete" => ["the group has been dissolved.", "此群已解散"],

        "key.defaultFriend.text" => ["we are friends, just talk to me", "我们已经成为好友，开始聊天吧"],
        "key.defaultGroup.text" => ["new member", "新成员"],

        "key.speaker.set" => [" set ", " 设置了 "],
        "key.speaker.asSpeaker" => [" as speaker", " 为发言人"],
        "key.speaker.closeUser" => [" close ", " 关闭了 "],
        "key.speaker.status" => [" speaker status", " 发言人状态"],
        "key.speaker.close" => [" close speakers function ", " 关闭了发言者功能"],

        "key.friend.acceptFrom" => ["I accept your friend apply, let's talk", "我接受了你的好友申请, 现在找我聊天吧"],
        "key.friend.acceptTo" => ["we are friends, just talk to me", "我添加了你为好友，开始聊天吧"],
    ];

    public static function getText($textKey, $lang = Zaly\Proto\Core\UserClientLangType::UserClientLangZH)
    {
        if (isset(self::$texts[$textKey])) {
            return self::$texts[$textKey][$lang];
        }

        throw new Exception("unSupport zaly text key=" . $textKey);
    }


    public static function buildMessageNotice($noticeText, $lang = Zaly\Proto\Core\UserClientLangType::UserClientLangZH)
    {
        $contentMsg = new \Zaly\Proto\Core\NoticeMessage();
        $contentMsg->mergeFromJsonString($noticeText);

        $body = $contentMsg->getBody();
        $body = self::buildBody($body, $lang);

        $contentMsg->setBody($body);
        return $contentMsg;
    }

    public static function buildMessageText($text, $lang = Zaly\Proto\Core\UserClientLangType::UserClientLangZH)
    {
        $contentMsg = new \Zaly\Proto\Core\TextMessage();
        $contentMsg->mergeFromJsonString($text);

        $body = $contentMsg->getBody();
        $body = self::buildBody($body, $lang);

        $contentMsg->setBody($body);
        return $contentMsg;
    }

    private static function buildBody($body, $lang)
    {
        //build origin body
        $keys = self::getTemplateKey($body);

        if (!empty($keys)) {
            $values = [];
            foreach ($keys as $i => $key) {
                $keyToValue = self::$templateKeys[$key];
                if (!empty($keyToValue)) {
                    $values[] = $keyToValue[$lang];
                    $keys[$i] = "{" . $key . "}";
                } else {
                    unset($keys[$i]);
                }
            }
            $body = str_replace($keys, $values, $body);
        }

        return $body;
    }

    private static function getTemplateKey($str)
    {
        $result = array();
        preg_match_all("/(?<={)[^}]+/", $str, $result);
        return $result[0];
    }

}
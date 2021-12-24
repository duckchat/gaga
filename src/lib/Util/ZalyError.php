<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 31/08/2018
 * Time: 6:45 PM
 */

class ZalyError
{
    const ErrorGroupCreateForbid = "error.group.create.forbid";
    const ErrorGroupEmptyId = "error.group.emptyId";
    const ErrorGroupPermission = "error.group.permission";

    const ErrorGroupAdmin = "error.group.admin";
    const ErrorGroupMember = "error.group.member";
    const ErrorGroupMemberCount = "error.group.maxMemberCount";
    const ErrorGroupIsMember = "error.group.isMember";

    const ErrorMessageRecallNoPermission = "error.message.recallNoPermission";
    const ErrorMessageNotExist = "error.message.recallNotExist";
    const ErrorMessageRecallOvertime = "error.message.recallOvertime";

    private static $defaultErrors = ["error", "request error", "请求错误"];


    public static $errors = [
        "error.group.emptyId" => ["error.alert", "groupId is empty", "群Id为空"],
        "error.group.permission" => ["error.alert", "no permission for group", "无当前群组操作权限"],
        "error.group.create.forbid" => ["error.alert", "create group forbidden", "站点禁止创建群组"],

        "error.group.admin" => ["error.alert", "No permission to operate", "只允许群主或者管理员邀请好友入群"],
        "error.group.member" => ["error.alert", "No permission to operate", "不是群成员，无权限操作"],
        "error.group.maxMemberCount" => ["error.alert", "The group member is full", "群已满员"],
        "error.group.isMember" => ["error.group.isMember", "user is already group member", "当前用户已经是群成员"],

        "error.message.recallNotExist" => ["error.alter", "no permission to recall ", "无权限撤回消息"],
        "error.message.recallOvertime" => ["error.alter", "message recall overtime", "消息撤回超过2分钟"],

        "error.message.recallNoPermission" => ["error.alter", "no permission", "无权限操作"],
    ];

    public static function getErrCode($error)
    {
        if (isset(self::$errors[$error])) {
            return self::$errors[$error][0];
        }
        return self::$defaultErrors[0];
    }

    public static function getErrorInfo($error, $lang)
    {
        return self::getErrorInfo2($error, $lang);
    }

    public static function getErrorInfo2($error, $lang = Zaly\Proto\Core\UserClientLangType::UserClientLangZH)
    {
        if (isset(self::$errors[$error])) {
            return self::$errors[$error][$lang + 1];
        }
        return self::$defaultErrors[$lang + 1];
    }

}
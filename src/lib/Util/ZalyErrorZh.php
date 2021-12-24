<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 18/07/2018
 * Time: 8:45 AM
 */

class ZalyErrorZh extends ZalyErrorBase
{
    private $defaultError = "操作失败";

    public $errorInfo = [
        "error.user.needRegister" => "用户需要注册",
        "error.db.writable"   => "数据库不可写",
        "error.session.id"    =>  "登录过期，请重新登录",
        "error.userProfile"   => "用户数据解析错误",
        "error.siteLogin"     => "用户登录失败",
        "error.plugin.list"   => "获取列表失败",
        "error.group.name.length"       => "群组名称长度不符合要求",
        "error.group.create.permission" => "该站点不允许创建群组",
        "error.group.create"  => "创建群组失败",
        "error.group.exist"   => "群已经被解散",
        "error.group.owner"   => "只有群主可以操作",
        "error.group.admin"   => "没有权限操作",
        "error.group.delete"  => "删除群组失败",
        "error.group.maxMemberCount" => "群满员",
        "error.group.member"  => "不是群成员，不能拉人",
        "error.group.idExists" => "群id无效",
        "error.user.idExists"  => "用户id无效",
        "error.group.profile"  => "获取群信息失败",
        "error.group.remove.permission" => "没有权限操作",
        "error.group.remove.groupId" => "操作失败",
        "error.group.remove.userId" => "移除成员不能为空",
        "error.group.remove.memberType" => "不能移除自己或者群主",
        "error.group.remove" => "操作失败",
        "error.group.quitOwner" => "群主不能退群",
        "error.group.quit" => "退群失败",
        "error.group.update" => "更新失败",
        "error.friend.apply" => "申请失败",
        "error.friend.apply.friendExist" => "已经是好友",
        "error.friend.update" => "更新数据失败",
        "error.friend.delete" => "删除失败",
        "error.friend" => "不是好友",
        "error.file.download" => "下载失败",
        "error.group.invite"  => "邀请失败",

        "error.invalid.email" => "无效邮箱",
        "error.exist.email"  => "邮箱已经存在",
        "error.exist.loginName" => "用户名已经存在",
        "error.invalid.loginName" => "用户名格式不正确",

        "error.match.login"  => "账号密码不匹配",
        "error.exist.user"   => "账号不存在",
        "error.preSessionId" => "登录失败",
        "error.login.passportPassword" => "登录失败",

        "error.verify.token" => "验证码错误",
        "error.update.pwd" => "更新密码失败",
        "error.update.invitationCode" => "更新邀请码失败",

        "error.loginName.length" => "用户名长度不合法",
        "error.password.length" => "密码长度不合法",
        "error.nickname.length" => "昵称长度不合法",
        "error.site.pubkPem" => "站点公钥不合法",

        "error.session.clear" => "删除失败",

        "error.updatePassword.loginName" => "用户名不正确",

        "error.invitation.code" => "邀请码不正确",

    ];

    public function getErrorInfo($errorCode)
    {
        return isset($this->errorInfo[$errorCode]) ? $this->errorInfo[$errorCode] : $this->defaultError;
    }
}
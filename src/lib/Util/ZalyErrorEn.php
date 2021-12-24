<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 24/07/2018
 * Time: 2:39 PM
 */
class ZalyErrorEn extends  ZalyErrorBase
{
    private $defaultError = "operation failed";

    public $errorInfo = [
        "error.user.needRegister" => "User need register",
        "error.db.writable"   => "DB is readonly",
        "error.session.id"    =>  "Please login",
        "error.userProfile"   => "UserProfile parse failed",
        "error.siteLogin"     => "Login failed",
        "error.plugin.list"   => "Get plugin list failed",
        "error.group.name.length"       => "Group name length is illegal",
        "error.group.create.permission" => "Not allow create group",
        "error.group.create"  => "Create failed",
        "error.group.exist"   => "The group has been dissolved",
        "error.group.owner"   => "No permission to operate",
        "error.group.admin"   => "No permission to operate",
        "error.group.delete"  => "No permission to operate",
        "error.group.maxMemberCount" => "Full of members",
        "error.group.member"  => "No permission to operate",
        "error.group.idExists" => "Invalid group id",
        "error.user.idExists"  => "Invalid user id",
        "error.group.profile"  => "Get group profile gailed",
        "error.group.remove.permission" => "No permission to operate",
        "error.group.remove.groupId" => "Operation Failed",
        "error.group.remove.userId" => "Remove member cannot be empty",
        "error.group.remove.memberType" => "Can't remove yourself or the owner",
        "error.group.remove" => "Operation Failed",
        "error.group.quitOwner" => "Group owners cannot retreat",
        "error.group.quit" => "Fallback failure",
        "error.group.update" => "Update failed",
        "error.friend.apply" => "Apply Failed",
        "error.friend.apply.friendExist" => "Already a friend",
        "error.friend.update" => "Update failed",
        "error.friend.delete" => "Delete failed",
        "error.friend" => "not friend",
        "error.file.download" => "Downlod Failed",
        "error.group.invite"  => "Invitation failed",

        "error.invalid.email" => "Invalid mailbox",
        "error.exist.email"  => "The mailbox already exists",
        "error.exist.loginName" => "LoginName already exists",
        "error.site.pubkPem" => "sitePubkPem is not available",
        "error.invalid.loginName" => "Invalid loginName",
        "error.match.login"  => "Account password does not match",
        "error.exist.user"   => "Account does not exist",
        "error.preSessionId" => "Login failed",
        "error.login.passportPassword" => "Login failed",

        "error.verify.token" => "Verification code error",
        "error.update.pwd" => "Update password failed",
        "error.update.invitationCode" => "Update invitation failed",
        "error.loginName.length" => "LoginName length is illegal",
        "error.password.length" => "Password length is illegal",

        "error.session.clear" => "clear failed",

        "error.updatePassword.loginName" => "LoginName unavailable",

        "error.invitation.code" => "Invitation code is error ",

    ];

    public function getErrorInfo($errorCode)
    {
        return isset($this->errorInfo[$errorCode]) ? $this->errorInfo[$errorCode] : $this->defaultError;
    }
}
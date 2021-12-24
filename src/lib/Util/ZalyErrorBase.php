<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 24/07/2018
 * Time: 2:40 PM
 */

/**
 * @property zalyError ZalyError.php
 */
abstract class  ZalyErrorBase
{

    public $errorUserNeedRegister = "error.user.needRegister";
    public $errorDBWritable = "error.db.writable";
    public $errorSession = "error.session";
    public $errorUserProfile = "error.userProfile";
    public $errorSiteLogin = "error.siteLogin";
    public $errorPluginList = "error.plugin.list";

    public $errorGroupNameLength = "error.group.name.length";
    public $errorGroupCreatePermission = "error.group.create.permission";
    public $errorGroupCreate = "error.group.create";
    public $errorGroupExist = "error.group.exist";
    public $errorGroupOwner = "error.group.owner";
    public $errorGroupAdmin = "error.group.admin";
    public $errorGroupDelete = "error.group.delete";
    public $errorGroupMemberCount = "error.group.maxMemberCount";
    public $errorGroupMember = "error.group.member";
    public $errorGroupIdExists = "error.group.idExists";
    public $errorUserIdExists = "error.user.idExists";
    public $errorGroupProfile = "error.group.profile";
    public $errorGroupInvite = "error.group.invite";
    public $errorGroupRemoveGroupId = "error.group.remove.groupId";
    public $errorGroupRemoveUserId = "error.group.remove.userId";
    public $errorGroupRemoveMemberType = "error.group.remove.memberType";
    public $errorGroupRemove = "error.group.remove";

    public $errorGroupQuitOwner = "error.group.quitOwner";
    public $errorGroupQuit = "error.group.quit";
    public $errorGroupUpdate = "error.group.update";

    public $errorFriendApply = "error.friend.apply";
    public $errorFriendApplyFriendExists = "error.friend.apply.friendExist";
    public $errorFriendUpdate = "error.friend.update";
    public $errorFriendDelete = "error.friend.delete";
    public $errorFriend = "error.friend";
    public $errorFriendIs = "error.friend.is";

    public $errorFileDownload = "error.file.download";

    public $errorInvalidEmail = "error.invalid.email";
    public $errorExistEmail = "error.exist.email";
    public $errorExistLoginName = "error.exist.loginName";

    public $errorInvalidLoginName = "error.invalid.loginName";
    public $errorLoginNameLength = "error.loginName.length";
    public $errorPassowrdLength = "error.password.length";
    public $errorNicknameLength = "error.nickname.length";
    public $errorSitePubkPem = "error.site.pubkPem";

    public $errorMatchLogin = "error.match.login";
    public $errorExistUser = "error.exist.user";

    public $errorPreSessionId = "error.preSessionId";
    public $errorLoginPassportPassword = "error.login.passportPassword";
    public $errorUpdateInvitation = "error.update.invitationCode";

    public $errorVerifyToken = "error.verify.token";
    public $errorUpdatePwd = "error.update.pwd";

    public $errorSessionClear = "error.session.clear";

    public $errorInvitationCode = "error.invitation.code";


    public $errorUpdatePasswordLoginName = "error.updatePassword.loginName";

    abstract public function getErrorInfo($errorCode);
}
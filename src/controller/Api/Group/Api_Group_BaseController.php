<?php

/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 20/07/2018
 * Time: 4:18 PM
 */
class Api_Group_BaseController extends BaseController
{
    private $classNameForRequest;
    public $userId;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        parent::rpc($request, $transportData);
    }

    public function deleteGroupInfo($groupId)
    {
        $groupInfo = $this->ctx->SiteGroupTable->getGroupInfo($groupId);
        if ($groupInfo) {
            $flag = $this->ctx->SiteGroupTable->deleteGroup($groupId);
            if (!$flag) {
                $tag = __CLASS__ . '-' . __FUNCTION__;
                $this->ctx->Wpf_Logger->error($tag, " deleteGroup group id = " . $groupId . " userId =" . $this->userId);
                $errorCode = $this->zalyError->errorGroupDelete;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }
        }
    }

    ///是否是群主
    public function isGroupOwner($groupId)
    {
        $ownerType = \Zaly\Proto\Core\GroupMemberType::GroupMemberOwner;
        $owner = $this->ctx->SiteGroupUserTable->getGroupUser($groupId, $this->userId, $ownerType);
        if (!$owner) {
            $tag = __CLASS__ . '-' . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, " is not owner group id = " . $groupId . " userId =" . $this->userId);
            $errorCode = $this->zalyError->errorGroupOwner;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception($errorInfo);
        }
    }

    //是否是群管理员，群主
    public function isGroupAdmin($groupId)
    {
        $ownerType = \Zaly\Proto\Core\GroupMemberType::GroupMemberOwner;
        $adminType = \Zaly\Proto\Core\GroupMemberType::GroupMemberAdmin;
        $tag = __CLASS__ . '-' . __FUNCTION__;

        $user = $this->ctx->SiteGroupUserTable->getGroupAdmin($groupId, $this->userId, $adminType, $ownerType);
        if (!$user) {
            $this->ctx->Wpf_Logger->error($tag, " is not addmin user id = " . $this->userId);
            $errorCode = $this->zalyError->errorGroupAdmin;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception($errorInfo);
        }
        return $user;
    }

    //是否是群管理员
    public function isGroupAdminMember($groupId, $userId)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            //管理员，或者群主
            $ownerType = \Zaly\Proto\Core\GroupMemberType::GroupMemberOwner;
            $adminType = \Zaly\Proto\Core\GroupMemberType::GroupMemberAdmin;

            $user = $this->ctx->SiteGroupUserTable->getGroupAdmin($groupId, $userId, $adminType, $ownerType);
            if ($user) {
                return true;
            }

        } catch (Exception $e) {
            $this->logger->error($tag . " " . $this->action, $e);
        }
        return false;
    }

    //群信息
    public function getGroupInfo($groupId)
    {
        $groupInfo = $this->ctx->SiteGroupTable->getGroupInfo($groupId);
        if (!$groupInfo) {
            $tag = __CLASS__ . '-' . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, " errorGroupExist group id = " . $groupId);
            $exText = ZalyText::getText("text.group.notExists", $this->language);
            $this->returnErrorCodeRPC("error.group.notExists", $exText);
            return false;
        }
        return $groupInfo;
    }

    public function getGroupUserCount($groupId)
    {
        return $this->ctx->SiteGroupUserTable->getGroupUserCount($groupId);
    }

    public function getGroupUserList($groupId, $offset, $pageSize)
    {
        return $this->ctx->SiteGroupUserTable->getGroupUserList($groupId, $offset, $pageSize);
    }

    ////是否是群成员
    public function isGroupMember($groupId)
    {
        $groupMember = $this->ctx->SiteGroupUserTable->getGroupUser($groupId, $this->userId);
        if (!$groupMember) {
            $tag = __CLASS__ . '-' . __FUNCTION__;
            $this->ctx->Wpf_Logger->error($tag, " not groupMember group id = " . $groupId . " userId =" . $this->userId);
            $errorCode = $this->zalyError->errorGroupMember;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception($errorInfo);
        }
        return $groupMember;
    }


    public function getGroupList($page, $pageSize)
    {
        return $this->ctx->SiteGroupTable->getGroupList($this->userId, $page, $pageSize);
    }

    public function getGroupProfile($groupId)
    {
        return $this->ctx->SiteGroupTable->getGroupProfile($groupId, $this->userId);
    }


    public function getGroupCount()
    {
        return $this->ctx->SiteGroupTable->getGroupCount($this->userId);
    }

    protected function getPublicGroupProfile($group)
    {
        $descType = isset($group['descriptionType']) && $group['descriptionType'] == 1 ?
            \Zaly\Proto\Core\GroupDescriptionType::GroupDescriptionMarkdown :
            \Zaly\Proto\Core\GroupDescriptionType::GroupDescriptionText;

        $groupDescription = new \Zaly\Proto\Core\GroupDescription();
        $groupDescription->setBody($group['description']);
        $groupDescription->setType($descType);

        $groupProfile = new \Zaly\Proto\Core\PublicGroupProfile();
        $groupProfile->setId($group['groupId']);
        $groupProfile->setName($group['name']);
        $groupProfile->setNameInLatin($group['nameInLatin']);
        $groupProfile->setAvatar($group['avatar']);
        $groupProfile->setDescription($groupDescription);
        $groupProfile->setPermissionJoin($group['permissionJoin']);
        $groupProfile->setCanGuestReadMessage($group['canGuestReadMessage']);
        $groupProfile->setTimeCreate($group['timeCreate']);
        $groupProfile->setCanAddFriend($group["canAddFriend"]);

        $ownerUser = "";
        $adminUsers = [];
        $speakerUsers = [];

        $adminType = \Zaly\Proto\Core\GroupMemberType::GroupMemberAdmin;
        $ownerType = \Zaly\Proto\Core\GroupMemberType::GroupMemberOwner;

        $admins = $this->ctx->SiteGroupUserTable->getGroupAllAdmin($group['groupId'], $adminType, $ownerType);

        foreach ($admins as $key => $user) {
            $publicUserProfile = $this->getPublicUserProfile($user);
            if ($user['memberType'] == $ownerType) {
                $ownerUser = $publicUserProfile;
                $groupProfile->setOwner($ownerUser);
            } else if ($user["memberType"] == $adminType) {
                $adminUsers[] = $publicUserProfile;
            }
        }

        if (count($adminUsers)) {
            $groupProfile->setAdmins($adminUsers);
        }

        $speakers = !empty($group['speakers']) ? explode(",", $group['speakers']) : [];

        if (count($speakers)) {
            $speakers = $this->ctx->SiteGroupUserTable->getGroupUsers($group['groupId'], $speakers);
            if (count($speakers)) {
                foreach ($speakers as $key => $user) {
                    $publicUserProfile = $this->getPublicUserProfile($user);
                    $speakerUsers[] = $publicUserProfile;
                }
                $groupProfile->setSpeakers($speakerUsers);
            }
        }

        return $groupProfile;
    }


    protected function updateGroupAvatar($groupId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {//query old 9 groupMember to make group avatar
            $this->ctx->Wpf_Logger->info("Group-Avatar", "update groupId=" . $groupId);

            $groupMemberAvatars = $this->getOldest9GroupMemberAvatars($groupId);
            $newGroupAvatar = $this->ctx->File_Manager->buildGroupAvatar($groupMemberAvatars);

            $this->ctx->Wpf_Logger->info("Group-Avatar", "update avatarFileId=" . $newGroupAvatar);
            if ($newGroupAvatar) {
                $data = [
                    'avatar' => $newGroupAvatar
                ];
                $where = [
                    'groupId' => $groupId
                ];
                $this->ctx->SiteGroupTable->updateGroupInfo($where, $data);
            }
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->info($tag, $e);
        }
    }

    /**
     * @param $groupId
     * @return array
     */
    protected function getOldest9GroupMemberAvatars($groupId)
    {
        $avatars = [];

        $offset = 0;
        $startCount = 9;
        $loopNum = 0;
        while (true) {
            $loopNum++;

            $hasCount = count($avatars);
            $pageCount = $startCount - $hasCount;

            $groupMembers = $this->getGroupUserList($groupId, $offset, $pageCount);

            $offset += $pageCount;

            $this->ctx->Wpf_Logger->info("Group-Avatar", "hasCount2=" . count($avatars));

            if (empty($groupMembers) || count($groupMembers) == 0) {
                break;
            }

            foreach ($groupMembers as $groupMember) {
                $userAvatar = $groupMember['avatar'];
                //判断头像存在
                $isExists = $this->ctx->File_Manager->fileIsExists($userAvatar);
                if ($isExists) {
                    $avatars[] = $userAvatar;
                }
            }

            if ($loopNum >= 10 || count($avatars) >= 9) {
                break;
            }
        }
        return $avatars;
    }

}
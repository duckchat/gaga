<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 20/07/2018
 * Time: 10:19 AM
 */


class Api_Group_CreateController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupCreateRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupCreateResponse';
    private $permissionJoin = \Zaly\Proto\Core\GroupJoinPermissionType::GroupJoinPermissionMember;
    private $canGuestReadMsg = false;
    private $maxMembers = -1;
    private $groupNameLength = 20;
    private $startGroupCreateTime;
    private $createGroupTimeOut = 60; //1分钟超时
    private $groupIdLength = 10;
    private $pinyin;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $this->pinyin = new \Overtrue\Pinyin\Pinyin();
        try {
            $this->isCanCreateGroup();

            $groupName = trim($request->getGroupName());

            if (mb_strlen($groupName) > $this->groupNameLength || mb_strlen($groupName) < 1) {
                $errorCode = $this->zalyError->errorGroupNameLength;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }
            $this->startGroupCreateTime = time();

            // get group default avatar
            $groupOwnerAvatar = $this->getUserProfile($this->userId);

            $groupAvatar = $this->ctx->File_Manager->buildGroupAvatar([$groupOwnerAvatar]);

            //insert new group
            $groupProfile = $this->insertGroup($groupName, $groupAvatar);
            if (!$groupProfile) {
                throw new Exception("create group failed");
            }

            $response = $this->getApiGroupCreateResponse($groupProfile);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);

            $this->finish_request();

            // 创建成功了之后，代发消息，
            $groupId = $groupProfile['groupId'];

            $noticeText = ZalyText::$keyGroupCreate;

            $this->ctx->Message_Client->proxyGroupNoticeMessage($this->userId, $groupId, $noticeText);

        } catch (ZalyException $ze) {
            $this->ctx->Wpf_Logger->error($tag, $ze->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, $ex);
            $this->returnErrorRPC(new $this->classNameForResponse(), $ex);
        }
    }

    /**
     * 1.站点是否允许创建群主
     * 2.不是站点管理员
     *
     * @throws Exception
     */
    private function isCanCreateGroup()
    {
        $enableCreateGroup = $this->siteConfig[SiteConfig::SITE_ENABLE_CREATE_GROUP];
        $isSiteManager = $this->ctx->Site_Config->isManager($this->userId);
        if (!$enableCreateGroup && !$isSiteManager) {
            $error = ZalyError::ErrorGroupCreateForbid;
            $this->throwZalyException($error);
        }
    }

    private function getUserProfile($userId)
    {
        $userProfile = $this->ctx->SiteUserTable->getUserByUserId($userId);
        return $userProfile['avatar'];
    }

    private function insertGroup($groupName, $groupAvatar)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {

            if (time() - $this->startGroupCreateTime > $this->createGroupTimeOut) {
                $this->ctx->BaseTable->db->rollBack();
                $errorCode = $this->zalyError->errorGroupCreate;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                return false;
            }
            $groupId = $this->ctx->ZalyHelper->generateStrKey($this->groupIdLength);
            $groupId = strtolower($groupId);
            $nameInLatin = $this->pinyin->permalink($groupName, "");
            $groupProfile = [
                "groupId" => $groupId,
                "name" => $groupName,
                "nameInLatin" => $nameInLatin,
                "avatar" => $groupAvatar,
                "owner" => $this->userId,
                "description" => "",
                "speakers" => "",
                "maxMembers" => $this->maxMembers,
                "permissionJoin" => $this->permissionJoin,
                "canGuestReadMessage" => $this->canGuestReadMsg ? 1 : 0,
                "status" => 1,
                "timeCreate" => $this->ctx->ZalyHelper->getMsectime()
            ];
            $groupOwnerInfo = [
                'groupId' => $groupId,
                'userId' => $this->userId,
                'memberType' => \Zaly\Proto\Core\GroupMemberType::GroupMemberOwner,
                'timeJoin' => $this->ctx->ZalyHelper->getMsectime()
            ];
            $this->ctx->BaseTable->db->beginTransaction();
            $this->ctx->BaseTable->insertData($this->ctx->SiteGroupTable->table, $groupProfile, $this->ctx->SiteGroupTable->columns);
            $this->ctx->BaseTable->insertData($this->ctx->SiteGroupUserTable->table, $groupOwnerInfo, $this->ctx->SiteGroupUserTable->columns);
            $this->ctx->BaseTable->db->commit();
            return $groupProfile;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->ctx->BaseTable->db->rollBack();
//            $this->insertGroup($groupName, $this->groupIdLength++);
        }
    }

    private function getApiGroupCreateResponse($groupProfile)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {

            $publicProfile = $this->getPublicGroupProfile($groupProfile);
            $profile = new \Zaly\Proto\Core\AllGroupProfile();
            $profile->setProfile($publicProfile);

            $publicUserProfile = $this->getPublicUserProfile($this->userInfo);
            $profile->setOwner($publicUserProfile);

            $response = new \Zaly\Proto\Site\ApiGroupCreateResponse();
            $response->setProfile($profile);

            return $response;
        } catch (Exception $ex) {
            $errorCode = $this->zalyError->errorGroupProfile;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            throw new Exception($errorInfo);
        }
    }
}
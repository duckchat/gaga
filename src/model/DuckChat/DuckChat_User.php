<?php
/**
 * 提供duckchat外部接口同时，也提供内部接口
 * User: anguoyue
 * Date: 2018/11/9
 * Time: 3:39 PM
 */

class DuckChat_User
{

    private $ctx;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
    }

    /**
     * duckchat.user.profile
     * @param $pluginId
     * @param \Zaly\Proto\Plugin\DuckChatUserProfileRequest $request
     * @return \Zaly\Proto\Plugin\DuckChatUserProfileResponse
     * @throws Exception
     */
    public function getProfile($pluginId, $request)
    {

        $userId = $request->getUserId();
        $userProfile = $this->getUserProfile($userId);
        if ($userProfile == false) {
            throw new Exception("user is not exists");
        }
        $response = $this->getUserProfileResponse($userProfile);

        return $response;
    }


    private function getUserProfile($userId)
    {
        return $this->ctx->SiteUserTable->getUserByUserId($userId);
    }

    private function getUserProfileResponse($userProfile)
    {
        $allUserProfile = new \Zaly\Proto\Core\AllUserProfile();
        $publicUserProfile = $this->getPublicUserProfile($userProfile);
        $allUserProfile->setPublic($publicUserProfile);
        $allUserProfile->setTimeReg($userProfile['timeReg']);

        $response = new \Zaly\Proto\Plugin\DuckChatUserProfileResponse();
        $response->setProfile($allUserProfile);
        return $response;
    }

    private function getPublicUserProfile($userInfo)
    {
        try {
            $publicUserProfile = new \Zaly\Proto\Core\PublicUserProfile();
            $avatar = isset($userInfo['avatar']) ? $userInfo['avatar'] : "";
            $publicUserProfile->setAvatar($avatar);
            $publicUserProfile->setUserId($userInfo['userId']);
            $publicUserProfile->setLoginname($userInfo['loginName']);
            $publicUserProfile->setNickname($userInfo['nickname']);
            $publicUserProfile->setNicknameInLatin($userInfo['nicknameInLatin']);

            if (isset($userInfo['availableType'])) {
                $publicUserProfile->setAvailableType($userInfo['availableType']);
            } else {
                $publicUserProfile->setAvailableType(\Zaly\Proto\Core\UserAvailableType::UserAvailableNormal);
            }
            return $publicUserProfile;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error("get public user profile", $ex);
            $publicUserProfile = new \Zaly\Proto\Core\PublicUserProfile();
            return $publicUserProfile;
        }
    }

    /**
     * duckchat.user.relation
     * @param $pluginId
     * @param \Zaly\Proto\Plugin\DuckChatUserRelationRequest $request
     * @return \Zaly\Proto\Plugin\DuckChatUserRelationResponse
     */
    public function getRelation($pluginId, $request)
    {
        $userId = $request->getUserId();
        $oppositeUserId = $request->getOppositeUserId();
        $relation = $this->getUserRelation($userId, $oppositeUserId);
        $response = $this->getRelationResponse($relation);
        return $response;
    }

    private function getUserRelation($userId, $oppositeUserId)
    {
        $relationInfo = $this->ctx->SiteUserFriendTable->getRealtion($userId, $oppositeUserId);
        if ($relationInfo == false) {
            return \Zaly\Proto\Core\FriendRelationType::FriendRelationInvalid;
        }
        return $relationInfo['relation'];
    }

    private function getRelationResponse($relation)
    {
        $response = new \Zaly\Proto\Plugin\DuckChatUserRelationResponse();
        $response->setRelationType($relation);
        return $response;
    }

}
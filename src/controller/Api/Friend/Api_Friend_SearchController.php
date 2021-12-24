<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 02/08/2018
 * Time: 10:02 AM
 */

class Api_Friend_SearchController extends BaseController
{

    protected $action = "api.friend.search";
    private $classNameForRequest = '\Zaly\Proto\Site\ApiFriendSearchRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiFriendSearchResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiFriendSearchRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $userId = $this->userId;
        $keyWords = $request->getKeywords();
        $offset = $request->getOffset();
        $count = $request->getCount(); // default 20

        $this->ctx->Wpf_Logger->info("api.friend.search", "request=" . $request->serializeToJsonString());

        $response = new \Zaly\Proto\Site\ApiFriendSearchResponse();

        try {

            if (empty($keyWords)) {
                throw new Exception("search keywords is empty");
            }

            $friendProfile = new Zaly\Proto\Core\FriendUserProfile();

            if (ZalyHelper::isPhoneNumber($keyWords)) {
                //is phone number
                $publicProfile = $this->getUserProfileByPhoneId($keyWords);
            } else {
                // loginName
                $publicProfile = $this->getUserProfileByLoginName($keyWords);
            }

            if (!empty($publicProfile) && !empty($publicProfile->getUserId())) {

                if ($this->userId == $publicProfile->getUserId()) {
                    if ($this->language == Zaly\Proto\Core\UserClientLangType::UserClientLangEN) {
                        throw new Exception("unable to add yourself");
                    } else {
                        throw new Exception("不允许添加自己");
                    }
                }

                $friendProfile->setProfile($publicProfile);

                $friendInfo = $this->getUserFriendInfo($userId, $publicProfile->getUserId());

                if (!empty($friendInfo)) {
                    $friendProfile->setMute($friendInfo['mute']);
                    $friendProfile->setRelation($friendInfo['relation']);
                } else {
                    $friendProfile->setRelation(0);
                }
                $response->setFriends([$friendProfile]);
            }

            $response->setTotalCount(1);
            $this->setRpcError($this->defaultErrorCode, "");

        } catch (Exception $e) {
            $this->setRpcError("error.alert", $e->getMessage());
            $this->ctx->Wpf_Logger->error("error.alert", $e);
        }

        $this->rpcReturn($this->action, $response);
        return;
    }

    private function getUserProfileByPhoneId($phoneId)
    {
        $user = $this->ctx->SiteUserTable->getUserByPhoneId($phoneId);

        return $this->getPublicProfileFromUser($user);
    }


    private function getUserProfileByLoginName($loginName)
    {
        $loginNameLowercase = strtolower($loginName);
        $user = $this->ctx->SiteUserTable->getUserByLoginNameLowercase($loginNameLowercase);

        return $this->getPublicProfileFromUser($user);
    }

    private function getPublicProfileFromUser($userInfo)
    {
        if (!empty($userInfo)) {
            $publicUserProfile = new \Zaly\Proto\Core\PublicUserProfile();

            $publicUserProfile->setUserId($userInfo['userId']);
            $avatar = isset($userInfo['avatar']) ? $userInfo['avatar'] : "";
            $publicUserProfile->setAvatar($avatar);
            $publicUserProfile->setLoginname($userInfo['loginName']);
            $publicUserProfile->setNickname($userInfo['nickname']);
            $publicUserProfile->setNicknameInLatin($userInfo['nicknameInLatin']);

            if (isset($userInfo['availableType'])) {
                $publicUserProfile->setAvailableType($userInfo['availableType']);
            } else {
                $publicUserProfile->setAvailableType(\Zaly\Proto\Core\UserAvailableType::UserAvailableNormal);
            }

            return $publicUserProfile;
        }
        return false;
    }

    private function getUserFriendInfo($userId, $friendUserId)
    {
        $friendInfo = $this->ctx->SiteUserFriendTable->queryUserFriend($userId, $friendUserId);
        return $friendInfo;
    }

}
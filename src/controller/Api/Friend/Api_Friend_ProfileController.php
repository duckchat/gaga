<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 23/07/2018
 * Time: 4:20 PM
 */

class Api_Friend_ProfileController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiFriendProfileRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiFriendProfileResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiFriendProfileRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $response = new Zaly\Proto\Site\ApiFriendProfileResponse();
        try {
            $userId = $this->userId;
            $friendUserId = $request->getUserId();

            $friend = $this->getFriendProfile($userId, $friendUserId);

            if (!empty($friend)) {

                $publicProfile = new Zaly\Proto\Core\PublicUserProfile();
                $publicProfile->setUserId($friend['userId']);
                $publicProfile->setAvatar($friend['avatar']);
                $publicProfile->setLoginName($friend['loginName']);
                $aliasName = $friend['aliasName'];

                if ($aliasName) {
                    $publicProfile->setNicknameInLatin($friend['aliasNameInLatin']);
                    $publicProfile->setNickname($aliasName);
                } else {
                    $publicProfile->setNicknameInLatin($friend['nicknameInLatin']);
                    $publicProfile->setNickname($friend['nickname']);
                }
                $publicProfile->setRealNickname($friend['nickname']);

                if ($friend['availableType']) {
                    $publicProfile->setAvailableType($friend['availableType']);
                } else {
                    $publicProfile->setAvailableType(\Zaly\Proto\Core\UserAvailableType::UserAvailableNormal);
                }

                $friendProfile = new Zaly\Proto\Core\FriendUserProfile();
                $friendProfile->setProfile($publicProfile);
                if ($friend['mute']) {
                    $friendProfile->setMute($friend['mute']);
                }
                if (!empty($friend['relation'])) {
                    $friendProfile->setRelation($friend['relation']);
                }

                $friendProfile->setCustom($this->getFriendCustomProfile($friendUserId));

                $response->setProfile($friendProfile);

                $this->setRpcError($this->defaultErrorCode, "");
            } else {
                $this->setRpcError("error.nouser", "no current user");
            }


        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->setRpcError("error.alert", "");
        }

        $this->rpcReturn($transportData->getAction(), $response);
    }

    protected function getFriendProfile($userId, $friendId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteUserTable->getFriendProfile($userId, $friendId);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e->getMessage());
        }
        return [];
    }

    private function getFriendCustomProfile($friendId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $customs = [];

        try {
            $customProfiles = $this->ctx->SiteUserCustomTable->queryOpenCustomProfile($friendId);
            $customKeyNames = $this->ctx->SiteUserCustomTable->getColumnNames();
            if ($customProfiles) {
                foreach ($customProfiles as $customKey => $customValue) {
                    $userCustom = new Zaly\Proto\Core\CustomUserProfile();
                    $userCustom->setCustomKey($customKey);
                    $userCustom->setCustomName($customKeyNames[$customKey]);
                    $userCustom->setCustomValue($customValue);
                    $customs[] = $userCustom;
                }
            }
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }

        return $customs;
    }
}
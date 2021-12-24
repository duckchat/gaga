<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 03/08/2018
 * Time: 5:21 PM
 */

class Api_User_UpdateController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiUserUpdateRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiUserUpdateResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiUserUpdateRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;

        $response = new \Zaly\Proto\Site\ApiUserUpdateResponse();
        try {
            $userId = $this->userId;
            $values = $request->getValues();

            $this->updateUserProfile($userId, $values);

            $userProfile = $this->getUserProfileResponse($userId);
            $response->setProfile($userProfile);

            $this->returnSuccessRPC($response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, $ex);
            $this->returnErrorRPC($response, $ex);
        }

        return;
    }

    /**
     * @param string $userId
     * @param array $values
     * @return bool
     * @throws Exception
     */
    private function updateUserProfile($userId, $values)
    {
        $updateProfileData = [];
        $updateCustomData = [];
        foreach ($values as $v) {
            $type = $v->getType();
            switch ($type) {
                case \Zaly\Proto\Site\ApiUserUpdateType::ApiUserUpdateAvatar:
                    //update user avatar
                    $avatar = $v->getAvatar();
                    $updateProfileData['avatar'] = $avatar;
                    break;
                case \Zaly\Proto\Site\ApiUserUpdateType::ApiUserUpdateNickname:
                    //update user name
                    $nickName = $v->getNickname();

                    if (empty($nickName)) {
                        throw new Exception("nickname is null");
                    }

                    if (mb_strlen($nickName) > 16) {
                        $nickName = mb_substr($nickName, 0, 16);
                    }

                    $updateProfileData['nickname'] = $nickName;
                    $pinyin = new \Overtrue\Pinyin\Pinyin();
                    $updateProfileData['nicknameInLatin'] = $pinyin->permalink($nickName, "");
                    break;
                case \Zaly\Proto\Site\ApiUserUpdateType::ApiUserUpdateCustom:
                    $custom = $v->getCustom();
                    $customKey = trim($custom->getCustomKey());
                    $customValue = trim($custom->getCustomValue());
                    $updateCustomData[$customKey] = $customValue;
                    break;
                default:
                    throw new Exception("api.user.update by error updateType");
            }
        }

        return $this->updateUserProfiles($updateProfileData, $userId) || $this->updateUserCustoms($updateCustomData, $userId);
    }

    private function updateUserProfiles(array $updateData, $userId)
    {
        if (empty($updateData)) {
            return false;
        }
        return $this->ctx->SiteUserTable->updateUserData(["userId" => $userId], $updateData);
    }

    private function updateUserCustoms(array $customData, $userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            if (empty($customData)) {
                return false;
            }
            $where = [
                "userId" => $userId,
            ];
            $result = $this->ctx->SiteUserCustomTable->updateCustomProfile($customData, $where);

            if (!$result) {
                return $this->insertUserCustoms($customData, $userId);
            }

            return true;
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
            return $this->insertUserCustoms($customData, $userId);
        }
    }

    private function insertUserCustoms(array $customData, $userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $customData['userId'] = $userId;
            return $this->ctx->SiteUserCustomTable->insertCustomProfile($customData);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
        return false;
    }

    protected function getUserProfileResponse($userId)
    {
        $profile = $this->getUserSelfProfile($userId);

        if (!empty($profile)) {

            $publicProfile = new Zaly\Proto\Core\PublicUserProfile();
            $publicProfile->setUserId($profile['userId']);
            $publicProfile->setAvatar($profile['avatar']);
            $publicProfile->setLoginName($profile['loginName']);
            $publicProfile->setNickname($profile['nickname']);
            $publicProfile->setNicknameInLatin($profile['nicknameInLatin']);
            $publicProfile->setRealNickname($profile['nickname']);

            if ($profile['availableType']) {
                $publicProfile->setAvailableType($profile['availableType']);
            } else {
                $publicProfile->setAvailableType(\Zaly\Proto\Core\UserAvailableType::UserAvailableNormal);
            }

            $AllUserProfile = new \Zaly\Proto\Core\AllUserProfile();
            $AllUserProfile->setPublic($publicProfile);
            $AllUserProfile->setCustom($this->getUserCustomProfile($userId));
            $AllUserProfile->setTimeReg($profile['timeReg']);

            return $AllUserProfile;
        }

        return null;
    }

    protected function getUserSelfProfile($userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteUserTable->getUserByUserId($userId);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, e);
        }
        return [];
    }

    private function getUserCustomProfile($userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $customs = [];

        try {
            $customProfiles = $this->ctx->SiteUserCustomTable->queryAllCustomProfile($userId);
            $customNameArray = $this->ctx->SiteUserCustomTable->getColumnNames();

            if ($customNameArray) {
                foreach ($customNameArray as $customKey => $customName) {
                    $userCustom = new Zaly\Proto\Core\CustomUserProfile();
                    $userCustom->setCustomKey($customKey);
                    $userCustom->setCustomName($customName);
                    $customValue = $customProfiles[$customKey];
                    $userCustom->setCustomValue(isset($customValue) ? $customValue : "");

                    $customs[] = $userCustom;
                }
            }
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }

        return $customs;
    }

}
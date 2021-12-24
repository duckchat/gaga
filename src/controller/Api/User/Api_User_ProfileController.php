<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 23/07/2018
 * Time: 4:20 PM
 */

class Api_User_ProfileController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiUserProfileRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiUserProfileResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiUserProfileRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $response = new Zaly\Proto\Site\ApiUserProfileResponse();
        try {
            $userId = $this->userId;

            $profile = $this->getUserSelfProfile($userId);

            if (!empty($profile)) {

                $publicProfile = new Zaly\Proto\Core\PublicUserProfile();
                $publicProfile->setUserId($profile['userId']);
                $publicProfile->setAvatar($profile['avatar']);
                $publicProfile->setLoginName($profile['loginName']);
                $publicProfile->setRealNickname($profile['nickname']);
                $publicProfile->setNickname($profile['nickname']);
                $publicProfile->setNicknameInLatin($profile['nicknameInLatin']);

                if ($profile['availableType']) {
                    $publicProfile->setAvailableType($profile['availableType']);
                } else {
                    $publicProfile->setAvailableType(\Zaly\Proto\Core\UserAvailableType::UserAvailableNormal);
                }

                $AllUserProfile = new \Zaly\Proto\Core\AllUserProfile();
                $AllUserProfile->setPublic($publicProfile);
                $AllUserProfile->setTimeReg($profile['timeReg']);
                $AllUserProfile->setCustom($this->getUserCustomProfile($userId));

                $response->setProfile($AllUserProfile);

                $this->setRpcError($this->defaultErrorCode, "");
            } else {
                $this->setRpcError("error.nouser", "no current user");
            }

        } catch (Exception $ex) {
            $this->setRpcError("error.alert", "get user profile error");
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
        }

        $this->rpcReturn($transportData->getAction(), $response);
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
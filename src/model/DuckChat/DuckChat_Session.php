<?php
/**
 * 提供duckchat外部接口同时，也提供内部接口
 * User: anguoyue
 * Date: 2018/11/9
 * Time: 3:39 PM
 */

class DuckChat_Session
{

    private $ctx;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
    }

    /**
     * duckchat.session.profile
     * @param $pluginId
     * @param \Zaly\Proto\Plugin\DuckChatSessionProfileRequest $request
     * @return \Zaly\Proto\Plugin\DuckChatSessionProfileResponse
     * @throws Exception
     */
    public function getProfile($pluginId, $request)
    {
        $duckchatSessionId = $request->getEncryptedSessionId();

        $pluginProfile = $this->getPluginProfile($pluginId);

        if (empty($duckchatSessionId)) {
            throw new Exception("encrypted sessionId is empty");
        }

        $duckchatSessionId = ZalyBase64::base64url_decode($duckchatSessionId);

        $authKey = $pluginProfile['authKey'];

        if (empty($authKey)) {
            $authKey = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_PLUGIN_PLBLIC_KEY);
        }

        $sessionId = $this->ctx->ZalyAes->decrypt($duckchatSessionId, $authKey);

        if (empty($sessionId)) {
            throw new Exception("decrypt user sesssionId is empty");
        }

        //sessionId -> userId
        $sessionInfo = $this->ctx->SiteSessionTable->getSessionInfoBySessionId($sessionId);

        if (empty($sessionInfo)) {
            throw new Exception("check user sesssionId is empty sessionId=" . $sessionId . '，duckchat_sessionid=' . $duckchatSessionId);
        }

        $userId = $sessionInfo['userId'];
        $loginPluginId = $sessionInfo['loginPluginId'];
        $userProfile = $this->ctx->SiteUserTable->getUserByUserId($userId);

        $response = $this->buildRequestResponse($userProfile, $loginPluginId);

        return $response;
    }

    private function getPluginProfile($pluginId)
    {
        return $this->ctx->SitePluginTable->getPluginById($pluginId);
    }

    private function buildRequestResponse($userProfile, $loginPluginId)
    {
        $publicProfile = new \Zaly\Proto\Core\PublicUserProfile();
        $publicProfile->setUserId($userProfile['userId']);
        $publicProfile->setAvatar(isset($userProfile['avatar']) ? $userProfile['avatar'] : "");
        $publicProfile->setLoginName($userProfile['loginName']);
        $publicProfile->setNickname($userProfile['nickname']);
        $publicProfile->setNicknameInLatin($userProfile['nicknameInLatin']);
        $publicProfile->setLoginPluginId($loginPluginId);
        if (isset($userProfile['availableType'])) {
            $publicProfile->setAvailableType($userProfile['availableType']);
        } else {
            $publicProfile->setAvailableType(\Zaly\Proto\Core\UserAvailableType::UserAvailableNormal);
        }
        $profile = new Zaly\Proto\Core\AllUserProfile();
        $profile->setPublic($publicProfile);
        $profile->setTimeReg($userProfile['timeReg']);

        $response = new Zaly\Proto\Plugin\DuckChatSessionProfileResponse();
        $response->setProfile($profile);

        return $response;
    }

    /**
     * duckchat.session.clear
     * @param $pluginId
     * @param \Zaly\Proto\Plugin\DuckChatSessionClearRequest $request
     * @return bool
     * @throws Exception
     */
    public function clearSession($pluginId, $request)
    {

        $userId = $request->getUserId();

        if (!$userId) {
            throw new Exception("duckchat.session.id userid is not exits");
        }

        if ($this->clearSessionByUserId($userId)) {
            return new \Zaly\Proto\Plugin\DuckChatSessionClearResponse();
        }

        return false;
    }

    private function clearSessionByUserId($userId)
    {
        $flag = $this->ctx->SiteSessionTable->deleteSessionByUserId($userId);

        if ($flag > 0) {
            return true;
        }
        throw new Exception("delete session failed");
    }

}
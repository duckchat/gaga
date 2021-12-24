<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 03/08/2018
 * Time: 5:21 PM
 */

class Api_Friend_UpdateController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiFriendUpdateRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiFriendUpdateResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiFriendUpdateRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;

        $response = new \Zaly\Proto\Site\ApiFriendUpdateResponse();
        try {
            $userId = $this->userId;
            $friendId = $request->getUserId();
            $values = $request->getValues();
            $this->updateUserFriendInfo($friendId, $values);

            $friendProfile = $this->getFriendProfile($userId, $friendId);
            $response->setProfile($friendProfile);

            $this->setRpcError($this->defaultErrorCode, "");
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, $ex);
            $errorCode = $this->zalyError->errorFriendUpdate;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
        }
        $this->rpcReturn($transportData->getAction(), $response);
    }

    private function updateUserFriendInfo($userId, $values)
    {
        $updateData = [];
        foreach ($values as $v) {
            $type = $v->getType();
            switch ($type) {
                case \Zaly\Proto\Site\ApiFriendUpdateType::ApiFriendUpdateRemark:
                    $remarks = $v->getRemark();
                    if (empty($remarks)) {
                        //if remark is empty
                        $updateData['aliasName'] = "";
                        $updateData['aliasNameInLatin'] = "";
                    } else {
                        $remarks = trim($remarks);
                        $updateData['aliasName'] = $remarks;
                        $pinyin = new \Overtrue\Pinyin\Pinyin();
                        $updateData['aliasNameInLatin'] = $pinyin->permalink($remarks, "");
                    }
                    break;
                case \Zaly\Proto\Site\ApiFriendUpdateType::ApiFriendUpdateIsMute:
                    $mute = $v->getIsMute();
                    $updateData['mute'] = $mute ? 1 : 0;
                    break;
            }
        }
        $where = [
            "userId" => $this->userId,
            "friendId" => $userId,
        ];
        $this->ctx->SiteUserFriendTable->updateData($where, $updateData);
    }

    protected function getFriendProfile($userId, $friendUserId)
    {
        $friend = $this->getDBFriendProfile($userId, $friendUserId);

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

            return $friendProfile;
        }

        return null;
    }

    protected function getDBFriendProfile($userId, $friendId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteUserTable->getFriendProfile($userId, $friendId);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e->getMessage());
        }
        return [];
    }
}
<?php

/**
 * Class Api_Group_SetSpeakerController
 * @author SAM<an.guoyue254@gmail.com>
 */
class Api_Group_SetSpeakerController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupSetSpeakerRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupSetSpeakerResponse';


    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiGroupSetSpeakerRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $response = new Zaly\Proto\Site\ApiGroupSetSpeakerResponse();
        try {
            $userId = $this->userId;
            $groupId = $request->getGroupId();
            $setType = $request->getSetType();
            $speakers = $request->getSpeakerUserIds();

            $setSpeakers = [];
            foreach ($speakers as $key => $val) {
                $setSpeakers[] = $val;
            }

            $this->logger->error($this->action, "request=" . $request->serializeToString());

            if (empty($groupId)) {
                $this->throwZalyException(ZalyError::ErrorGroupEmptyId);
            }

            //group admin can set speaker
            if (!$this->isGroupAdmin($groupId)) {
                $this->throwZalyException(ZalyError::ErrorGroupPermission);
            }

            $groupInfo = $this->getGroupInfo($groupId);
            if ($groupInfo === false) {
                return;
            }

            $groupSpeakers = $groupInfo['speakers'];
            if (empty($groupSpeakers)) {
                $groupSpeakers = [];
            } else {
                $groupSpeakers = explode(",", $groupSpeakers);
            }

            switch ($setType) {
                case \Zaly\Proto\Site\SetSpeakerType::AddSpeaker:
                    $latestSpeakers = $this->addGroupSpeakers($groupId, $groupSpeakers, $setSpeakers);
                    if (!empty($latestSpeakers)) {
                        $response->setSpeakerUserIds($latestSpeakers);
                    }
                    break;
                case \Zaly\Proto\Site\SetSpeakerType::RemoveSpeaker:
                    $latestSpeakers = $this->removeGroupSpeakers($groupId, $groupSpeakers, $setSpeakers);

                    if (!empty($latestSpeakers)) {
                        $response->setSpeakerUserIds($latestSpeakers);
                    }
                    break;
                case \Zaly\Proto\Site\SetSpeakerType::CloseSpeaker:
                    $this->closeGroupSpeaker($groupId, $groupSpeakers);
                    break;
            }

            $this->proxyGroupNotice($groupId, $this->userId, $response->getSpeakerUserIds(), $setType);

            $this->returnSuccessRPC($response);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
            $this->returnErrorRPC(new $this->classNameForResponse(), $e);
        }
    }


    private function addGroupSpeakers($groupId, $groupSpeakers, $setSpeakers)
    {
        if (empty($groupSpeakers)) {
            $groupSpeakers = $setSpeakers;
        } else {
            $groupSpeakers = array_merge($groupSpeakers, $setSpeakers);
            $groupSpeakers = array_unique($groupSpeakers);
        }

        $result = $this->updateGroupSpeakers($groupId, $groupSpeakers);

        if (!$result) {
            throw new Exception("add group speakers fail");
        }

        return $groupSpeakers;
    }

    private function removeGroupSpeakers($groupId, $groupSpeakers, $setSpeakers)
    {
        if (empty($groupSpeakers)) {
            return [];
        }

        $groupSpeakers = array_diff($groupSpeakers, $setSpeakers);

        $result = $this->updateGroupSpeakers($groupId, $groupSpeakers);

        if (!$result) {
            throw new Exception("remove group speakers fail");
        }

        return $groupSpeakers;
    }

    private function closeGroupSpeaker($groupId, $groupSpeakers)
    {
        if (empty($groupSpeakers)) {
            return true;
        }

        $result = $this->updateGroupSpeakers($groupId);

        if (!$result) {
            throw new Exception("close group speakers fail");
        }

        return true;
    }

    private function updateGroupSpeakers($groupId, $speakers = [])
    {
        if (empty($speakers)) {
            $speakers = "";
        } else {
            $speakers = implode(",", $speakers);
        }
        $data = [
            'speakers' => $speakers,
        ];
        $where = [
            'groupId' => $groupId,
        ];

        return $this->ctx->SiteGroupTable->updateGroupInfo($where, $data);
    }

//    private function throwZalyException($errCode)
//    {
//        $errInfo = ZalyError::getErrorInfo2($errCode, $this->language);
//        throw new ZalyException($errCode, $errInfo);
//    }


    private function proxyGroupNotice($groupId, $groupAdminId, $speakeIds, $setType)
    {
        $noticeText = $this->buildGroupNotice($groupAdminId, $speakeIds, $setType);
        $this->ctx->Message_Client->proxyGroupNoticeMessage($groupAdminId, $groupId, $noticeText);
    }

    private function buildGroupNotice($groupAdminId, $speakerIds, $setType)
    {

        $nameBody = "";

        if (isset($groupAdminId)) {
            $name = $this->getUserName($groupAdminId);
            if ($name) {
                $nameBody .= $name;
            }
        }

        if ($setType == Zaly\Proto\Site\SetSpeakerType::RemoveSpeaker) {
            $nameBody .= ZalyText::$keySpeakerCloseUser;
        } elseif ($setType == Zaly\Proto\Site\SetSpeakerType::CloseSpeaker) {
            $nameBody .= ZalyText::$keySpeakerClose;
            return $nameBody;
        } else {//
            $nameBody .= ZalyText::$keySpeakerSet;
        }

        if (empty($speakerIds)) {
            return false;
        }

        foreach ($speakerIds as $num => $userId) {

            $name = $this->getUserName($userId);

            if ($name) {
                if ($num == 0) {
                    $nameBody .= $name;
                } else {
                    $nameBody .= "," . $name;
                }
            }

        }

        if ($setType == Zaly\Proto\Site\SetSpeakerType::RemoveSpeaker) {
            $nameBody .= ZalyText::$keySpeakerStatus;
        } else {//
            $nameBody .= ZalyText::$keySpeakerAsSpeaker;
        }

        return $nameBody;
    }

    /**
     * @param $userId
     * @return null
     */
    private function getUserName($userId)
    {
        $userInfo = $this->ctx->SiteUserTable->getUserByUserId($userId);

        if (!empty($userInfo)) {
            $userName = $userInfo['nickname'];

            if (empty($userName)) {
                $userName = $userInfo['loginName'];
            }

            return $userName;
        } else {
            return null;
        }

    }
}
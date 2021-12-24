<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 10/08/2018
 * Time: 4:02 PM
 */

class Push_Client
{
    private $logger;
    private $ctx;
    private $pushAction = "api.push.notification";

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
        $this->logger = $ctx->getLogger();
    }


    /**
     * @param $msgId 为了站点可以通过msgId获取push发送情况
     * @param int $roomType
     * @param int $msgType
     * @param $fromUserId
     * @param string $toId toUserId/toGroupId
     * @param $pushText
     */
    public function sendNotification($msgId, $roomType, $msgType, $fromUserId, $toId, $pushText)
    {
        $pushRequest = new \Zaly\Proto\Platform\ApiPushNotificationRequest();
        try {

            $siteConfig = $this->getSiteConfig();

            $pushType = $siteConfig[SiteConfig::SITE_SUPPORT_PUSH_TYPE];

            if (empty($pushType)) {
                $pushType = 0;
            }

            if (Zaly\Proto\Core\PushType::PushDisabled == $pushType) {
                $this->logger->info($this->pushAction, "site disable push function");
                return;
            }

            $pushHeader = new \Zaly\Proto\Platform\PushHeader();

            //sign time seconds
            $currentTimeSeconds = $this->ctx->ZalyHelper->getCurrentTimeSeconds();
            $sitePrivatekey = $siteConfig[SiteConfig::SITE_ID_PRIK_PEM];
            $timeSingBase64 = base64_encode($this->ctx->ZalyRsa->sign($currentTimeSeconds, $sitePrivatekey));

            $pushHeader->setTimestampSeconds($currentTimeSeconds);
            $pushHeader->setSignTimestamp($timeSingBase64);

            $pushHeader->setSitePubkPemId($siteConfig['siteId']);
            $pushHeader->setSiteName($siteConfig['name']);
            $pushHeader->setSiteAddress($siteConfig['address']);

            $pushRequest->setPushHeader($pushHeader);
            $pushBody = new \Zaly\Proto\Platform\PushBody();//body 1
//            $pushBody->
            $pushBody->setRoomType($roomType);//body 2
            $pushBody->setMsgType($msgType);
            $pushBody->setFromUserId($fromUserId);
            $userNickName = $this->ctx->SiteUserTable->getUserNickName($fromUserId);
            $pushBody->setFromUserName($userNickName);

            if (Zaly\Proto\Core\PushType::PushWithMessageContent == $pushType) {
                $pushBody->setPushContent($pushText);
            }

            if (\Zaly\Proto\Core\MessageRoomType::MessageRoomGroup == $roomType) {
                $pushBody->setRoomId($toId);
                $pushBody->setRoomName($this->getGroupName($toId));
            }
            $deviceIds = $this->getPushDeviceIdList($roomType, $fromUserId, $toId);
            $pushBody->setToDevicePubkPemIds($deviceIds);
            $pushBody->setMsgId($msgId);
            $pushRequest->setPushBody($pushBody);

//            $this->ctx->Wpf_Logger->info("api.push.notification", "request=" . $pushRequest->serializeToJsonString());

        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error("api.push.notification.payload", $e);
            return;
        }

        try {
            $pushURL = "http://open.akaxin.com:5208/?action=" . $this->pushAction . "&body_format=pb";
            $this->ctx->ZalyCurl->requestWithActionByPb($this->pushAction, $pushRequest, $pushURL, 'POST');
            $this->ctx->Wpf_Logger->info("api.push.notification.response", "roomType=" . $pushRequest->serializeToJsonString());
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error("api.push.notification.error", $e);
        }

    }

    private function getSiteConfig()
    {
        $config = $this->ctx->SiteConfigTable->selectSiteConfig();

        if (!empty($config)) {
            $siteId = $config[SiteConfig::SITE_ID];
            if (empty($siteId)) {
                if ($config[SiteConfig::SITE_ID_PUBK_PEM]) {
                    $siteId = sha1($config[SiteConfig::SITE_ID_PUBK_PEM]);
                    $config[SiteConfig::SITE_ID] = $siteId;
                }
            }
        }
        $this->ctx->Wpf_Logger->info("site-config", json_encode($config));
        return $config;
    }

    private function getGroupName($groupId)
    {
        $groupName = $this->ctx->SiteGroupTable->getGroupName($groupId);
        return $groupName;
    }

    /**
     * @param \Zaly\Proto\Platform\PushRoomType $roomType
     * @param $fromUserId
     * @param $toId
     * @return array
     */
    private function getPushDeviceIdList($roomType, $fromUserId, $toId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $deviceIdList = [];

        if (\Zaly\Proto\Core\MessageRoomType::MessageRoomU2 == $roomType) {
            //get user deviceIds without muted
            if (!$this->isUserFriendMute($toId, $fromUserId)) {
                $deviceIdList = $this->getUserDeviceIds($toId);
            }

        } else {

            try {//group
                $groupMembers = $this->ctx->SiteGroupUserTable->getGroupAllMembersId($toId);

                if (!empty($groupMembers)) {
                    foreach ($groupMembers as $groupMember) {

                        $toUserId = $groupMember['userId'];

                        if ($fromUserId == $toUserId) {
                            continue;
                        }

                        if ($this->isUserGroupMute($toId, $toUserId)) {
                            continue;
                        }

                        $pushDeviceIds = $this->getUserDeviceIds($toUserId);

                        if (!empty($pushDeviceIds)) {
                            $deviceIdList = array_merge($deviceIdList, $pushDeviceIds);
                        }
                    }
                }

//                $this->ctx->Wpf_Logger->info("api.push.notification", "group DeviceIds=" . json_encode($deviceIdList));
            } catch (Exception $e) {
                $this->ctx->Wpf_Logger->error($tag, $e);
            }

        }

        return $deviceIdList;
    }

    /**
     * @param $userId
     * @return array
     */
    private function getUserDeviceIds($userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $deviceIds = $this->ctx->SiteSessionTable->getUserLatestDeviceId($userId, Zaly\Proto\Core\UserClientType::UserClientMobileApp, 2);

            $deviceList = [];
            if (!empty($deviceIds)) {
                //mobile client
                foreach ($deviceIds as $deviceId) {
                    $deviceList[] = $deviceId['deviceId'];
                }
                return $deviceList;
            } else {
                //web client
                $deviceList[] = "da39a3ee5e6b4b0d3255bfef95601890afd80709";
            }
            return $deviceList;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
        return [];
    }

    private function isUserFriendMute($userId, $friendUserId)
    {
        $mute = $this->ctx->SiteUserFriendTable->queryUserFriendMute($userId, $friendUserId);

        if (isset($mute) && $mute == 1) {
            return true;
        }

        return false;
    }


    private function isUserGroupMute($groupId, $userId)
    {
        $isMute = $this->ctx->SiteGroupUserTable->getGroupUserMute($groupId, $userId);

        if (isset($isMute) && $isMute == 1) {
            return true;
        }

        return false;
    }

}
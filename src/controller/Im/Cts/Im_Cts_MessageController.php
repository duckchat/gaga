<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 21/07/2018
 * Time: 7:33 PM
 */

class Im_Cts_MessageController extends Im_BaseController
{

    //request ： 接收到的消息
    private $requestAction = "im.cts.Message";
    private $classNameForCtsRequest = 'Zaly\Proto\Site\ImCtsMessageRequest';


    private $isGroupRoom = false;
    private $toId;

    public function rpcRequestClassName()
    {
        return $this->classNameForCtsRequest;
    }

    /**
     * 己收到请求，并且校验完成session，准备处理具体逻辑
     * 当执行到doRealRpc() ,开始处理各自的具体业务逻辑
     *
     * @param \Zaly\Proto\Site\ImCtsMessageRequest $request
     * @param \Zaly\Proto\Core\TransportData $transportData
     * @return mixed
     * @throws Exception
     */
    public function doRequest(\Google\Protobuf\Internal\Message $request, Zaly\Proto\Core\TransportData $transportData)
    {

        $message = $request->getMessage();
        $msgId = $message->getMsgId();
        $fromUserId = $this->userId;
        $msgType = $message->getType();
        $msgRoomType = $message->getRoomType();
        $result = false;

        try {
            if (Zaly\Proto\Core\MessageRoomType::MessageRoomGroup == $msgRoomType) {
                $this->isGroupRoom = true;
                $this->toId = $message->getToGroupId();

                //if group exist isLawful
                $groupProfile = $this->checkGroupExisted($this->toId);
                if (empty($groupProfile)) {
                    //if group is not exist
                    $noticeText = ZalyText::getText(ZalyText::$textGroupNotExists);
                    $this->returnGroupNotLawfulMessage($msgId, $msgRoomType, $fromUserId, $this->toId, $noticeText);
                    return;
                } else {
                    //check
                    $speakers = $groupProfile['speakers'];

                    if (!empty($speakers)) {
                        $speakers = explode(",", $speakers);

                        if (!$this->isGroupAdmin($this->toId) && !in_array($this->userId, $speakers)) {
                            $noticeText = ZalyText::getText(ZalyText::$textGroupNotSpeaker);
                            $noticeText .= $this->getSpeakersName($speakers);
                            $this->returnGroupNotLawfulMessage($msgId, $msgRoomType, $fromUserId, $this->toId, $noticeText);
                            return;
                        }
                    }
                }

                // if lawful go on
                $isLawful = $this->checkIsGroupMember($fromUserId, $this->toId);
                if (!$isLawful) {
                    //if user is not group member
                    //                $noticeText = ZalyText::getText(ZalyText::$textGroupNotMember);
                    $noticeText = ZalyText::$keyGroupNotMember;
                    $this->returnGroupNotLawfulMessage($msgId, $msgRoomType, $fromUserId, $this->toId, $noticeText);
                    return;
                }

                $result = $this->ctx->Message_Client->sendGroupMessage($msgId, $fromUserId, $this->toId, $msgType, $message);

            } else if (Zaly\Proto\Core\MessageRoomType::MessageRoomU2 == $msgRoomType) {
                $this->isGroupRoom = false;
                $this->toId = $message->getToUserId();

                //check friend relation
                $isFriend = $this->getIsFriendRelation($fromUserId, $this->toId);

                if ($isFriend) {
                    $result = $this->ctx->Message_Client->sendU2Message($msgId, $this->toId, $fromUserId, $this->toId, $msgType, $message);
                } else {
                    $result = false;
                    $this->returnU2MessageIfNotFriend($msgId, $msgRoomType, $fromUserId, $this->toId);
                    return;
                }

            }
        } catch (ZalyException $ze) {
            $result = false;
            $this->logger->error($this->action, $ze->getMessage() . "->" . $ze->getErrInfo($this->language));
        }

        $this->returnMessage($msgId, $msgRoomType, $msgType, $message, $fromUserId, $this->toId, $result);

        return;
    }

    private function returnMessage($msgId, $msgRoomType, $msgType, $message, $fromUserId, $toUserId, $result)
    {
        //echo data
        $this->returnMessageStatus($this->sessionId, $msgId, $msgRoomType, $result);

        $this->finish_request();

        //send friend news
        $this->ctx->Message_News->tellClientNews($this->isGroupRoom, $this->toId);

        if ($result) {
            //send push to friend
            $pushText = $this->getPushText($msgType, $message);

            $this->ctx->Push_Client->sendNotification($msgId, $msgRoomType, $msgType, $fromUserId, $this->toId, $pushText);
        }
    }

    private function returnU2MessageIfNotFriend($msgId, $msgRoomType, $fromUserId, $toUserId)
    {
        $this->returnMessageStatus($this->sessionId, $msgId, $msgRoomType, false);

        $this->finish_request();

        //get user name
        $toUserName = $this->ctx->SiteUserTable->getUserNickName($toUserId);

        $title = $this->language == 1 ? "[通知]" : '[notice]';

        $text = $toUserName . ' 不是你的好友，点击发送 ';
        $stLength = mb_strlen($text);
        $height = '80';

        if ($stLength >= 11) {
            $text .= '<br/>';
            $height = '120';
        }

        $code = '<! DOCTYPE html ><html ><head ><meta charset = "UTF-8" ><meta charset = "UTF-8" ><meta name = "viewport" content = "width=device-width, initial-scale=1, maximum-scale=1" ></head ><body style = "background: #DFDFDF;margin:5px 0 0 0;padding:0" ><div style = "text-align: center;font-size: 14px" > <font color =#FFFFFF>' . $text . '</font><font color=#4C3BB1>好友申请</font></div></body></html>';
        $hrefUrl = 'duckchat://0.0.0.0/goto?page=addFriend&x=u-' . $toUserId;

        //代发一个web消息给from
        $this->ctx->Message_Client->proxyU2WebNoticeMessage($fromUserId, $toUserId, $fromUserId, $title, $code, $hrefUrl, $height, true);
    }

    //return if group is not lawful
    private function returnGroupNotLawfulMessage($msgId, $msgRoomType, $fromUserId, $groupId, $noticeText)
    {
        $this->returnMessageStatus($this->sessionId, $msgId, $msgRoomType, false);
        //finish request
        $this->finish_request();

        //proxy group message to u2
        $this->ctx->Message_Client->proxyGroupAsU2NoticeMessage($fromUserId, $fromUserId, $groupId, $noticeText, true);

    }


    //check group-message if lawful
    private function checkGroupExisted($groupId)
    {
        $groupProfile = $this->ctx->SiteGroupTable->getGroupInfo($groupId);
        return $groupProfile;
    }

    private function checkIsGroupMember($userId, $groupId)
    {
        $groupUser = $this->ctx->SiteGroupUserTable->getGroupUser($groupId, $userId);
        if ($groupUser) {
            return true;
        }
        return false;
    }


    // check u2-message if lawful
    private function getIsFriendRelation($userId, $friendUserId)
    {
        if ($userId == $friendUserId) {
            return false;
        }

        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $isFriend = $this->ctx->SiteUserFriendTable->isFriend($userId, $friendUserId);
            $this->ctx->Wpf_Logger->info($tag, "check message isFriend = " . $isFriend);
            return $isFriend;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
        return false;
    }

    private function isGroupAdmin($groupId)
    {
        $ownerType = \Zaly\Proto\Core\GroupMemberType::GroupMemberOwner;
        $adminType = \Zaly\Proto\Core\GroupMemberType::GroupMemberAdmin;
        $tag = __CLASS__ . '-' . __FUNCTION__;

        $user = $this->ctx->SiteGroupUserTable->getGroupAdmin($groupId, $this->userId, $adminType, $ownerType);
        if (empty($user)) {
            return false;
        }
        return true;
    }

    /**
     * @param $msgType
     * @param \Zaly\Proto\Core\Message $message
     * @return string
     */
    private function getPushText($msgType, $message)
    {
        switch ($msgType) {
            case \Zaly\Proto\Core\MessageType::MessageNotice:
                $notice = $message->getNotice();
                return $notice->getBody();
            case \Zaly\Proto\Core\MessageType::MessageText:
                $text = $message->getText();
                return $text->getBody();
        }
        return '';
    }

    /**
     * @param array $speakers
     * @return string
     */
    private function getSpeakersName(array $speakers)
    {
        $speakersName = "";
        if (!empty($speakers)) {
            $speakersInfo = $this->ctx->SiteUserTable->getUserByUserIds($speakers);

            foreach ($speakersInfo as $num => $speaker) {

                $nickname = $speaker['nickname'];
                if ($num == 0) {
                    $speakersName .= $nickname;
                } else {
                    $speakersName .= "," . $nickname;
                }

            }
        }

        return $speakersName;
    }
}

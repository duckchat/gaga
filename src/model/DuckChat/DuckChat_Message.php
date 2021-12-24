<?php
/**
 * 提供duckchat外部接口同时，也提供内部接口，内部消息接口
 * User: anguoyue
 * Date: 2018/11/9
 * Time: 3:39 PM
 */

class DuckChat_Message extends DuckChat_Base
{

    private $ctx;
    private $isGroupRoom = false;
    private $toId;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
    }

    /**
     * @param $pluginId
     * @param \Zaly\Proto\Plugin\DuckChatMessageSendRequest $request
     * @return void|\Zaly\Proto\Plugin\DuckChatMessageSendResponse
     * @throws Exception
     */
    public function send($pluginId, $request)
    {
        $message = $request->getMessage();
        $fromUserId = $message->getFromUserId();
        $msgRoomType = $message->getRoomType();
        $msgId = $message->getMsgId();

        if (empty($msgId)) {
            $msgId = $this->buildMsgId($msgRoomType, $fromUserId);
        }

        $msgType = $message->getType();
        $result = false;
        if (Zaly\Proto\Core\MessageRoomType::MessageRoomGroup == $msgRoomType) {
            $this->isGroupRoom = true;
            $this->toId = $message->getToGroupId();

            //if group exist isLawful
            $isLawful = $this->checkGroupExisted($this->toId);
            if (!$isLawful) {
                //if group is not exist
                $noticeText = "group chat is not exist";
                $this->returnGroupNotLawfulMessage($msgId, $msgRoomType, $fromUserId, $this->toId, $noticeText);
                return;
            }

            $result = $this->ctx->Message_Client->sendGroupMessage($msgId, $fromUserId, $this->toId, $msgType, $message);

        } else if (Zaly\Proto\Core\MessageRoomType::MessageRoomU2 == $msgRoomType) {
            $this->isGroupRoom = false;
            $this->toId = $message->getToUserId();
            $result = $this->ctx->Message_Client->sendU2Message($msgId, $this->toId, $fromUserId, $this->toId, $msgType, $message);
        }

        $this->returnMessage($msgId, $msgRoomType, $msgType, $message, $fromUserId, $this->toId, $result);
        return new \Zaly\Proto\Plugin\DuckChatMessageSendResponse();
    }


    private function returnMessage($msgId, $msgRoomType, $msgType, $message, $fromUserId, $toUserId, $result)
    {
        $this->finish_request();

        $this->ctx->Message_News->tellClientNews(false, $fromUserId);
        //send friend news
        $this->ctx->Message_News->tellClientNews($this->isGroupRoom, $this->toId);

        //send push to friend
        $pushText = $this->getPushText($msgType, $message);

        $this->ctx->Push_Client->sendNotification($msgId, $msgRoomType, $msgType, $fromUserId, $this->toId, $pushText);
    }

    //return if group is not lawful
    private function returnGroupNotLawfulMessage($msgId, $msgRoomType, $fromUserId, $groupId, $noticeText)
    {
        //finish request
        $this->finish_request();

        //proxy group message to u2
        $this->ctx->Message_Client->proxyGroupAsU2NoticeMessage($fromUserId, $fromUserId, $groupId, $noticeText);
        //send im.stc.news to client
        $this->ctx->Message_News->tellClientNews(false, $fromUserId);
    }

    //check group-message if lawful
    private function checkGroupExisted($groupId)
    {
        $groupProfile = $this->ctx->SiteGroupTable->getGroupInfo($groupId);
        if ($groupProfile) {
            return true;
        }
        return false;
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

}
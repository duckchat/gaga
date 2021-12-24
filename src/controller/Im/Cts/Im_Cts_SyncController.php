<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 21/07/2018
 * Time: 7:33 PM
 */

class Im_Cts_SyncController extends Im_BaseController
{
    // request : 客户端sync请求
    private $requestAction = "im.cts.sync";
    private $classNameForCtsSyncRequest = '\Zaly\Proto\Site\ImCtsSyncRequest';

    // response : 客户端sync请求
    private $responseAction = "im.stc.message";
    private $classNameForStcMessageRequest = '\Zaly\Proto\Client\ImStcMessageRequest';

    private $stsFinishAction = "im.stc.finish";
    private $classNameForStcFinishRequest = '\Zaly\Proto\Client\ImStcFinishRequest';

    private $syncMaxSize = 100;


    // 用户判断当前proto是否存在
    public function rpcRequestClassName()
    {
        return $this->classNameForCtsSyncRequest;
    }

    /**
     * 己收到请求，并且校验完成session，准备处理具体逻辑
     * 当执行到doRealRpc() ,开始处理各自的具体业务逻辑
     *
     * @param \Zaly\Proto\Site\ImCtsSyncRequest $request
     * @param \Zaly\Proto\Core\TransportData $transportData
     * @return mixed
     */
    public function doRequest(\Google\Protobuf\Internal\Message $request, Zaly\Proto\Core\TransportData $transportData)
    {
        $userId = $this->userId;
        $sessionId = $this->sessionId;
        $deviceId = $this->deviceId;

        $needUpdate = $request->getUpdatePointer();
        $u2Count = $request->getU2Count();
        $groupCount = $request->getGroupCount();

//        if ($needUpdate) {
//            $this->updateU2Pointer($userId, $deviceId, $u2Sync);
//            $this->updateGroupPointer($userId, $deviceId, $groupSyncArrMap);
//        }

        $list = [];
        $isEnd = true;

        $limitU2Count = isset($u2Count) ? $u2Count : $this->syncMaxSize;
        // sync u2 message
        $u2MessageList = $this->syncU2Message($userId, $deviceId, $limitU2Count);

        $this->syncLog("sync u2 msg count=" . count($u2MessageList));

        //return u2 message
        $u2List = $this->returnImStcMessage(false, $u2MessageList);

        if (!empty($u2List) && count($u2List) >= $limitU2Count) {
            $isEnd = false;
        }

        //need to sync count
        $limitGroupCount = isset($groupCount) ? $groupCount : $this->syncMaxSize;
        //sync group message details
        $groupMessageList = $this->syncGroupMessage($userId, $deviceId, $limitGroupCount);

        $this->syncLog("sync group msg count=" . count($u2MessageList));

        //return group message
        $groupList = $this->returnImStcMessage(true, $groupMessageList);

        //只有同步到了数据，但是数据又不够，认为还有消息
        if ($isEnd && !empty($list) && count($list) >= ($limitU2Count + $limitGroupCount)) {
            //同步到了，并且数量够了，
            $isEnd = false;
        }

        $response = new \Zaly\Proto\Client\ImStcMessageRequest();

        $list = array_merge($u2List, $groupList);
        if ($isEnd) {
            $endMessage = new \Zaly\Proto\Core\Message();
            $endMessage->setType(\Zaly\Proto\Core\MessageType::MessageEventSyncEnd);
            $list[] = $endMessage;
        }

        $response->setList($list);

        $this->setRpcError("success", "");
        $this->rpcReturn($this->responseAction, $response);
    }


    private function syncU2Message($userId, $deviceId, $limitCount)
    {
        $isFirst = false;
        //u2 pointer
        $currentPointer = $this->ctx->SiteU2MessageTable->queryU2Pointer($userId, $deviceId);

        if (empty($currentPointer)) {
            $isFirst = true;
            $currentPointer = $this->ctx->SiteU2MessageTable->queryMaxU2Pointer($userId);
        }

        if (empty($currentPointer)) {
            $currentPointer = 0;
        }

        $this->syncLog("sync u2 message pointer=" . $currentPointer);

        $u2MessageList = $this->ctx->SiteU2MessageTable->queryMessage($userId, $currentPointer, $limitCount);

        if ($isFirst && $currentPointer > 0) {
            //update pointer
            $this->ctx->SiteU2MessageTable->updatePointer($userId, $deviceId, "1", $currentPointer);
        }

        return $u2MessageList;
    }

    private function syncGroupMessage($userId, $deviceId, $limitCount)
    {
        $groupMessageList = [];

        $this->ctx->Wpf_Logger->info("GroupPointer", "sync group message limitCount=" . $limitCount);

        //查用户有多少个群
        $userGroups = $this->ctx->SiteGroupUserTable->getUserGroups($userId);

        if (!empty($userGroups)) {
            foreach ($userGroups as $groupIdMap) {
                $groupId = $groupIdMap["groupId"];

                $currentPointer = $this->ctx->SiteGroupMessageTable->queryPointer($groupId, $userId, $deviceId);

                if (empty($currentPointer)) {
                    $currentPointer = 0;
                }
                $this->ctx->Wpf_Logger->info("GroupPointer", "get group=" . $groupId . " pointer=" . $currentPointer);

                if ($currentPointer == 0) {
                    $currentPointer = $this->ctx->SiteGroupMessageTable->queryMaxPointerByUser($groupId, $userId);
                    $this->ctx->Wpf_Logger->info("GroupPointer", "get user max group pointer=" . $currentPointer);

                    if ($currentPointer > 0) {
                        $this->ctx->SiteGroupMessageTable->updatePointer($groupId, $userId, $deviceId, $currentPointer);
                    }
                }

                $groupMaxId = $this->ctx->SiteGroupMessageTable->queryMaxIdByGroup($groupId);
                $this->ctx->Wpf_Logger->info("GroupPointer", "get group=" . $groupId . " max pointer=" . $groupMaxId);

                if ($currentPointer > $groupMaxId) {
                    // =0 : group members when first created
                    // =0 : its a new member,set max groupId to current user(so we need to set the pointer when join group)
                    // > $groupMaxId : its a error pointer ,need to use $groupMaxId instead of error pointer
                    $currentPointer = $groupMaxId;
                    $this->ctx->SiteGroupMessageTable->updatePointer($groupId, $userId, $deviceId, $currentPointer);
                    $this->ctx->Wpf_Logger->info("GroupPointer", "group pointer > groupMaxId update " . $groupMaxId . " -> " . $currentPointer);
                }

                while (true) {
                    $leftCount = $limitCount - count($groupMessageList);

                    if ($leftCount <= 0) {
                        break;
                    }

                    $queryMessageList = $this->ctx->SiteGroupMessageTable->queryMessage($groupId, $currentPointer, $leftCount);
                    $this->ctx->Wpf_Logger->info("group message", json_encode($queryMessageList));

                    if (!empty($queryMessageList)) {
                        $groupMessageList = array_merge($groupMessageList, $queryMessageList);
                    }

                    //当前群没数据了，break
                    if (empty($queryMessageList) || count($queryMessageList) < $leftCount) {
                        break;
                    }
                }

                if (!empty($groupMessagList) && count($groupMessagList) >= $limitCount) {
                    break;
                }

            }
        }

        $tag = __CLASS__ . "->" . __FUNCTION__;
        $this->ctx->Wpf_Logger->info($tag, "userId=" . $userId . " sync group message count=" . count($groupMessageList));

        return $groupMessageList;
    }

    /**
     * return message to client
     *
     * @param bool $isGroup
     * @param array $messageList
     * @return array
     */
    private function returnImStcMessage($isGroup, array $messageList)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        if (empty($messageList)) {
            return [];
        }

        $list = [];
        try {
            foreach ($messageList as $u2OrGroupMessage) {
                $message = new \Zaly\Proto\Core\Message();
                $message->setMsgId($u2OrGroupMessage["msgId"]);
                $message->setPointer($u2OrGroupMessage["id"]);
                $message->setFromUserId($u2OrGroupMessage["fromUserId"]);
                $message->setType($u2OrGroupMessage["msgType"]);

                if ($isGroup) {
                    $message->setToGroupId($u2OrGroupMessage["groupId"]);
                    $message->setRoomType(Zaly\Proto\Core\MessageRoomType::MessageRoomGroup);
                } else {
                    //if u2 message ,maybe contains group message
                    $u2RoomType = $u2OrGroupMessage['roomType'];
                    if ($u2RoomType == Zaly\Proto\Core\MessageRoomType::MessageRoomGroup) {
                        $message->setToGroupId($u2OrGroupMessage["toUserId"]);
                        $message->setRoomType(Zaly\Proto\Core\MessageRoomType::MessageRoomGroup);
                        $message->setTreatPointerAsU2Pointer(true);
                    } else {
                        $message->setToUserId($u2OrGroupMessage["toUserId"]);
                        $message->setRoomType(Zaly\Proto\Core\MessageRoomType::MessageRoomU2);
                    }
                }

                $content = $u2OrGroupMessage["content"];

                switch ($message->getType()) {
                    case \Zaly\Proto\Core\MessageType::MessageNotice:
                        $contentMsg = ZalyText::buildMessageNotice($content, $this->language);
                        $message->setNotice($contentMsg);
                        break;
                    case \Zaly\Proto\Core\MessageType::MessageText:
                        $contentMsg = ZalyText::buildMessageText($content, $this->language);
                        $message->setText($contentMsg);
                        break;
                    case \Zaly\Proto\Core\MessageType::MessageImage:
                        $contentMsg = new \Zaly\Proto\Core\ImageMessage();
                        $contentMsg->mergeFromJsonString($content);
                        $message->setImage($contentMsg);
                        break;
                    case \Zaly\Proto\Core\MessageType::MessageAudio:
                        $contentMsg = new \Zaly\Proto\Core\AudioMessage();
                        $contentMsg->mergeFromJsonString($content);
                        $message->setAudio($contentMsg);
                        break;
                    case \Zaly\Proto\Core\MessageType::MessageWeb:
                        $contentMsg = new \Zaly\Proto\Core\WebMessage();
                        $contentMsg->mergeFromJsonString($content);
                        $message->setWeb($contentMsg);
                        break;
                    case \Zaly\Proto\Core\MessageType::MessageWebNotice:
                        $contentMsg = new \Zaly\Proto\Core\WebNoticeMessage();
                        $contentMsg->mergeFromJsonString($content);
                        $message->setWebNotice($contentMsg);
                        break;
                    case \Zaly\Proto\Core\MessageType::MessageEventFriendRequest:
                        $this->ctx->Wpf_Logger->error("im.stc.message", "sync MessageEventFriendRequest");
                        break;
                    case Zaly\Proto\Core\MessageType::MessageDocument:
                        $documentMsg = new Zaly\Proto\Core\DocumentMessage();
                        $documentMsg->mergeFromJsonString($content);
                        $message->setDocument($documentMsg);
                        break;
                    case Zaly\Proto\Core\MessageType::MessageVideo:
                        $vedioMsg = new Zaly\Proto\Core\VideoMessage();
                        $vedioMsg->mergeFromJsonString($content);
                        $message->setVideo($vedioMsg);
                        break;
                    case Zaly\Proto\Core\MessageType::MessageRecall:
                        $recallMsg = new Zaly\Proto\Core\RecallMessage();
                        $recallMsg->mergeFromJsonString($content);
                        $message->setRecall($recallMsg);
                        break;
                    default:
                        $this->ctx->Wpf_Logger->error("im.stc.message", "sync message with error msgType");
                }
                $message->setTimeServer($u2OrGroupMessage["msgTime"]);
                $list[] = $message;
            }

        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e->getTraceAsString());
        }

        return $list;
    }

    private function syncLog($content)
    {
        try {
            $this->logger->info($this->action . ".log",
                "userId=" . $this->userId . " deviceId=" . $this->deviceId . " " . $content);
        } catch (Exception $e) {
            $this->logger->error("im.cts.sync.log", $e);
        }
    }
}


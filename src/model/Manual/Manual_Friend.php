<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/11/2018
 * Time: 5:48 PM
 */

interface Friend {
    /**
     * @param $userId
     * @param $applyUserId
     * @return mixed
     */
    public  function addFriend($userId, $applyUserId, $greetings);
}

class Manual_Friend extends Manual_Common implements Friend
{
    public function addFriend($userId, $applyUserId, $greetings)
    {
        if(!$userId || !$applyUserId) {
            return false;
        }
        //查询 version
        $relation1 = $this->ctx->SiteUserFriendTable->isFollow($userId, $applyUserId);

        if ($relation1 != 1) {
            $success = $this->ctx->SiteUserFriendTable->saveUserFriend($userId, $applyUserId);

            if ($success) {
                //更新 version
            } else {
                return false;
            }
        }

        $relation2 = $this->ctx->SiteUserFriendTable->isFollow($applyUserId, $userId);

        if ($relation2 != 1) {
            //查询version
            $success = $this->ctx->SiteUserFriendTable->saveUserFriend($applyUserId, $userId);

            if ($success) {
                //更新version
            } else {
                return false;
            }

        }

        $this->proxyNewFriendMessage($userId, $applyUserId, $greetings);
    }

    private function proxyNewFriendMessage($fromUserId, $toUserId, $greetings)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            if (empty($greetings)) {
                $greetings = $text = ZalyText::$keyFriendAcceptTo;
            }
            $this->ctx->Message_Client->proxyU2TextMessage($fromUserId, $toUserId, $fromUserId, $greetings);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
    }
}
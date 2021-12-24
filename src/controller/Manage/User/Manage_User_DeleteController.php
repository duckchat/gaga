<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_User_DeleteController extends Manage_CommonController
{

    public function doRequest()
    {
        $deleteUserId = $_POST['deleteUserId'];

        $this->ctx->Wpf_Logger->info("-------", "delete userId=" . $deleteUserId);

        $response = [
            'errCode' => "error",
        ];

        try {

            $isManager = $this->ctx->Site_Config->isManager($this->userId);

            if (!$isManager) {
                $exMsg = $this->language == 1 ? "非管理员无权操作" : "no permission for you";
                throw new Exception($exMsg);
            }

            $isOwner = $this->ctx->Site_Config->isSiteOwner($deleteUserId);

            if ($isOwner) {
                $exMsg = $this->language == 1 ? "无权删除站长" : "can't remove site owner";
                throw new Exception($exMsg);
            }


            $isManager = $this->ctx->Site_Config->isManager($deleteUserId);
            if ($isManager) {

                $currentIsOwner = $this->ctx->Site_Config->isSiteOwner($this->userId);

                if (!$currentIsOwner) {
                    $exMsg = $this->language == 1 ? "无权删除管理员" : "can't remove site manager";
                    throw new Exception($exMsg);
                }
            }

            $this->removeDefault($deleteUserId);

            if ($this->deleteUser($deleteUserId)) {
                $response['errCode'] = "success";
            }
        } catch (Exception $e) {
            $response['errInfo'] = $e->getMessage();
            $this->ctx->Wpf_Logger->error("manage.user.delete.error", $e);
        }

        echo json_encode($response);
        return;
    }

    private function removeDefault($deleteUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            //remove managers
            $this->ctx->Site_Config->removeDefaultFriend($deleteUserId);
            $this->ctx->Site_Config->removeSiteManager($deleteUserId);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }

    }

    /**
     * 1.delete user session
     * 2.delete user profile
     * 3.delete user message
     * 4.delete user pointer
     * @param $userId
     * @return bool
     */
    private function deleteUser($userId)
    {
        //delete user session
        $result = $this->deleteSession($userId);

        //delete user profile
        if ($result) {
            $result = $this->deleteProfile($userId);
        }

        if ($result) {
            $this->deleteMessage($userId);
        }

        //delete user message & pointer
        return true;
    }

    private function deleteSession($userId)
    {
        return $this->ctx->SiteSessionTable->deleteSessionByUserId($userId);
    }

    private function deleteProfile($userId)
    {
        return $this->ctx->SiteUserTable->deleteUserProfile($userId);
    }

    private function deleteMessage($userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $result = $this->ctx->SiteU2MessageTable->deleteMessage($userId);
            if ($result) {
                $this->ctx->SiteU2MessageTable->deleteMessagePointer($userId);
            }
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
    }


}
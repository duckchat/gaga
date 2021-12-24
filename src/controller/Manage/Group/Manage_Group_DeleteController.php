<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_Group_DeleteController extends Manage_CommonController
{

    public function doRequest()
    {
        $groupId = $_POST['groupId'];

        $response = [
            'errCode' => "error",
        ];

        try {

            $this->removeDefault($groupId);

            if ($this->deleteGroup($groupId)) {
                $response['errCode'] = "success";
            }

        } catch (Exception $e) {
            $response['errInfo'] = $e->getMessage();
            $this->ctx->Wpf_Logger->error("manage.group.delete.error", $e);
        }

        echo json_encode($response);
        return;
    }

    private function removeDefault($groupId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $this->ctx->Site_Config->removeDefaultGroup($groupId);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
    }

    private function deleteGroup($groupId)
    {
        //删除群成员(必须先删除这里)
        $result = $this->ctx->SiteGroupUserTable->deleteGroupMembers($groupId);

        if ($result) {
            //删除资料
            $result = $this->ctx->SiteGroupTable->deleteGroup($groupId);
        }

        //删除群消息 && 群消息游标
        $this->deleteGroupMessage($groupId);
        return $result;
    }

    private function deleteGroupMessage($groupId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $result = $this->ctx->SiteGroupMessageTable->deleteGroupMessage($groupId);
            if ($result) {
                $result = $this->ctx->SiteGroupMessageTable->deleteGroupMessagePointer($groupId);
            }
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
    }
}
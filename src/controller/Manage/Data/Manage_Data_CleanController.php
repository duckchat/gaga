<?php
/**
 * 实现Servelt中的方法
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_Data_CleanController extends Manage_ServletController
{

    protected function doGet()
    {
        $page = $_GET['page'];
        $params = [
            "lang" => $this->language,
        ];

        if (empty($page)) {
            $params['title'] = $this->getLanguageText("数据清理", "Clean Data");
            echo $this->display("manage_data_clean", $params);
            return;
        }

        if ($page == "u2Message") {
            $params['title'] = $this->getLanguageText("清理二人聊天消息", "Clean U2 Message");
            $params['type'] = "u2Message";
            echo $this->display("manage_data_cleanMessage", $params);
        } elseif ($page == "groupMessage") {
            $params['title'] = $this->getLanguageText("清理群组聊天消息", "Clean Group Message");
            $params['type'] = "groupMessage";
            echo $this->display("manage_data_cleanMessage", $params);
        }

        return;
    }

    protected function doPost()
    {

        $type = $_POST["type"];
        $beforeTime = $_POST["beforeTime"];

        $result = [
            "errCode" => "error",
        ];

        if (empty($beforeTime)) {
            $result["errInfo"] = $this->getLanguageText("待删除日期错误", "date time error");
            echo json_encode($result);
            return;
        }

        $res = false;
        if ($type == "u2Message") {
            //删除二人消息
            $res = $this->deleteU2Message($beforeTime);
        } elseif ($type == "groupMessage") {
            //删除群组消息
            $res = $this->deleteGroupMessage($beforeTime);
        }

        if ($res) {
            $result["errCode"] = "success";
        }

        echo json_encode($result);
        return;
    }

    private function deleteU2Message($msgTime)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteU2MessageTable->deleteMessageByTime($msgTime);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
        return false;
    }

    private function deleteGroupMessage($msgTime)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteGroupMessageTable->deleteMessageByTime($msgTime);
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
        return false;
    }

}
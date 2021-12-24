<?php
/**
 * 自定义key信息
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 06/11/2018
 * Time: 11:49 AM
 */

class Manage_Custom_UserUpdateController extends Manage_ServletController
{

    protected function doGet()
    {
        $params = [
            "lang" => $this->language,
            "title" => $this->language == 1 ? "用户资料配置" : "User Profile Custom",
        ];

        $costomKey = trim($_GET['customKey']);

        $userCustomInfo = $this->ctx->SiteUserCustomTable->getCustomByKey($costomKey);

        $params["userCustomInfo"] = $userCustomInfo;
        echo $this->display("manage_custom_userProfile", $params);
        return;
    }

    protected function doPost()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $result = [
            "errCode" => "error",
        ];

        try {
            $customKey = trim($_POST["customKey"]);
            $key = trim($_POST["updateKey"]);
            $value = trim($_POST["updateValue"]);
            if (empty($customKey)) {
                $text = $this->language == 1 ? "customKey 为空" : "no customKey find to update";
                throw new Exception($text);
            }
            if ("customKey" == $key) {
                $text = $this->language == 1 ? "禁止修改Key" : "forbid to operate";
                throw new Exception($text);
            }
            
            if ($this->updateUserCustom($customKey, $key, $value)) {
                $result["errCode"] = "success";
            }
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }

        echo json_encode($result);
        return;
    }

    private function updateUserCustom($customKey, $updatKey, $updateValue)
    {
        $data = [
            $updatKey => $updateValue,
        ];

        $where = [
            "customKey" => $customKey
        ];

        return $this->ctx->SiteUserCustomTable->updateUserCustomInfo($data, $where);
    }

}


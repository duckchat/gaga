<?php
/**
 * 删除自定义的用户key
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 06/11/2018
 * Time: 11:49 AM
 */

class Manage_Custom_UserDeleteController extends Manage_ServletController
{
    private $defaultKeys = [
        "phoneId",
        "email",
    ];

    protected function doGet()
    {
        return;
    }

    protected function doPost()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $result = [
            "errCode" => "error",
        ];

        try {
            $customKey = trim($_POST['customKey']);
            if (in_array($customKey, $this->defaultKeys)) {
                $txt = $this->language == 1 ? "默认字段不可删除" : "No Permission";
                throw new Exception($txt);
            }

            if ($this->deleteCustomKey($customKey)) {
                $result['errCode'] = "success";
            }

        } catch (Exception $e) {
            $this->logger->error($tag, $e);
            $result['errInfo'] = $e->getMessage();
        }

        echo json_encode($result);
        return;
    }

    private function deleteCustomKey($customKey)
    {
        return $this->ctx->SiteUserCustomTable->deleteUserCustomInfo($customKey);
    }

}


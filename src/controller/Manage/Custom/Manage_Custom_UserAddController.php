<?php
/**
 * 增加用户自定义字段
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 06/11/2018
 * Time: 11:49 AM
 */

class Manage_Custom_UserAddController extends Manage_ServletController
{

    protected function doGet()
    {
        $params = [
            "lang" => $this->language,
            "title" => $this->language == 1 ? "添加用户字段" : "Add User Field",
        ];

        echo $this->display("manage_custom_userAdd", $params);
        return;
    }

    protected function doPost()
    {
        $result = [
            'errCode' => "error",
        ];

        $customName = trim($_POST['keyName']);

        if (empty($customName)) {
            throw new Exception($this->language == 1 ? "字段名称不能为空" : "key name can't be empty");
        }

        $keySort = trim($_POST['keySort']);
        if (empty($keySort)) {
            $keySort = 20;
        }

        if (!is_numeric($keySort)) {
            throw new Exception($this->language == 1 ? "排序字段请输入数字" : "keySort is not numeric");
        }

        $status = trim($_POST['status']);

        if (!is_numeric($status)) {
            $status = Zaly\Proto\Core\UserCustomStatus::UserCustomNormal;
        }

        $isOpen = trim($_POST['isOpen']);
        $isRequired = trim($_POST['isRequired']);
        $keyConstraint = trim($_POST['keyConstraint']);

        $pinyin = new \Overtrue\Pinyin\Pinyin();
        $customKey = $pinyin->permalink($customName, "");
        $customs = [
            'customKey' => $customKey,
            'keyName' => $customName,
            'keyIcon' => trim($_POST['keyIcon']),
            'keySort' => $keySort,
            'status' => $status,
            'isOpen' => $isOpen ? 1 : 0,
            'isRequired' => $isRequired ? 1 : 0,
            'keyConstraint' => $keyConstraint ? "index" : "",
        ];

        if ($this->addUserCustomKey($customs)) {
            $result['errCode'] = "success";
        }

        echo json_encode($result);
        return;
    }

    private function addUserCustomKey(array $customArr)
    {
        return $this->ctx->SiteUserCustomTable->insertUserCustomInfo($customArr);
    }

}


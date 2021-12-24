<?php
/**
 * 自定义资料页面
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 06/11/2018
 * Time: 11:49 AM
 */

class Manage_Custom_UserController extends Manage_ServletController
{

    protected function doGet()
    {

        $params = [
            "lang" => $this->language,
            "title" => $this->language == 1 ? "用户资料配置" : "User Profile Custom",
        ];

        $userCustoms = $this->ctx->SiteUserCustomTable->getAllColumnInfos();

        $params["userCustoms"] = $userCustoms;
        echo $this->display("manage_custom_user", $params);
        return;
    }

    protected function doPost()
    {
        // TODO: Implement doPost() method.
        // 后期做分页，加载自定义中的字段
    }

}


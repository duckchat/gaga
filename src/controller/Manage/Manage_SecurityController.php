<?php
/**
 * Created by PhpStorm.
 * Author: zhangjun
 * Date: 06/11/2018
 * Time: 10:43 AM
 */

class Manage_SecurityController extends Manage_CommonController
{

    public function doRequest()
    {
        $params = ["lang" => $this->language];
        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();
        $passwordErrorNumConfig = $loginConfig[LoginConfig::PASSWORD_ERROR_NUM];
        $passwordErrorNum = isset($passwordErrorNumConfig['configValue']) ? $passwordErrorNumConfig['configValue'] : "5";
        $params['passwordErrorNum'] = $passwordErrorNum;

        $params['openWaterMark'] = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_OPEN_WATERMARK);

        echo $this->display("manage_security_index", $params);

        return;
    }
}
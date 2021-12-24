<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 07/11/2018
 * Time: 11:49 AM
 */

class Manage_Security_QuickController extends Manage_CommonController
{
    public function doRequest()
    {

        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();
        $pwdContainCharacterTypeConfig = $loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTER_TYPE];
        $pwdContainCharacterType = isset($pwdContainCharacterTypeConfig['configValue']) ? $pwdContainCharacterTypeConfig['configValue'] : "pwd_default" ;
        $params['pwdContainCharacterType'] = $pwdContainCharacterType;
        $params['lang'] = $this->language;
        echo $this->display("manage_security_quick", $params);
    }
}
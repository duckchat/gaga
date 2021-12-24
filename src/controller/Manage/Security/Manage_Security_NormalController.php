<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 07/11/2018
 * Time: 11:50 AM
 */

class Manage_Security_NormalController extends Manage_CommonController
{
    public function doRequest()
    {

        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();

        $loginNameMinLengthConfig = isset($loginConfig[LoginConfig::LOGINNAME_MINLENGTH]) ? $loginConfig[LoginConfig::LOGINNAME_MINLENGTH] : "";
        $loginNameMinLength = isset($loginNameMinLengthConfig['configValue']) ? $loginNameMinLengthConfig['configValue'] : 5 ;
        $params['loginNameMinLength'] = $loginNameMinLength;

        $loginNameMaxLengthConfig = isset($loginConfig[LoginConfig::LOGINNAME_MAXLENGTH]) ? $loginConfig[LoginConfig::LOGINNAME_MAXLENGTH] : "";
        $loginNameMaxLength= isset($loginNameMaxLengthConfig['configValue']) ? $loginNameMaxLengthConfig['configValue'] : 32 ;
        $params['loginNameMaxLength'] = $loginNameMaxLength;

        $pwdMinLengthConfig = isset( $loginConfig[LoginConfig::PASSWORD_MINLENGTH] )?  $loginConfig[LoginConfig::PASSWORD_MINLENGTH] : "";
        $pwdMinLength = isset($pwdMinLengthConfig['configValue']) ? $pwdMinLengthConfig['configValue'] : 6 ;
        $params['passwordMinLength'] = $pwdMinLength;

        $pwdMaxLengthConfig = isset($loginConfig[LoginConfig::PASSWORD_MAXLENGTH]) ? $loginConfig[LoginConfig::PASSWORD_MAXLENGTH] : "";
        $pwdMaxLength = isset($pwdMaxLengthConfig['configValue']) ? $pwdMaxLengthConfig['configValue'] : 32 ;
        $params['passwordMaxLength'] = $pwdMaxLength;

        $pwdContainCharactersConfig = isset($loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTERS]) ? $loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTERS] : "";
        $pwdContainCharacters = isset($pwdContainCharactersConfig['configValue']) ? $pwdContainCharactersConfig['configValue'] : "" ;
        $params['passwordContainCharacters'] = $pwdContainCharacters;

        $params['lang'] = $this->language;

        echo $this->display("manage_security_normal", $params);
    }
}
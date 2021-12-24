<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 06/11/2018
 * Time: 10:52 AM
 */

class Manage_Security_UpdateController extends Manage_CommonController
{
    private $pwdMinLength = 6;
    private $pwdMaxLength = 32;

    private $loginNameMinLength = 1;
    private $loginNameMaxLength = 24;

    public function doRequest()
    {

        $result = [
            'errCode' => "error.alert",
            'errInfo' => "",
        ];

        try {
            $key = isset($_POST['key']) ? $_POST['key'] : "";
            $pwdType = isset($_POST['pwd_type']) ? $_POST['pwd_type']: "";
            if($pwdType) {
                $pwdMinLength = 6;
                $pwdMaxLength = 32;
                $pwdErrorNum = 5;
                $pwdContainCharaters = "letter,number";
                switch ($pwdType) {
                    case "pwd_convenience":
                        $pwdContainCharaters = "";
                        $pwdErrorNum = 10;
                        break;
                    case "pwd_security":
                        $pwdContainCharaters = "letter,number,special_characters";
                        $pwdErrorNum = 3;
                        $pwdMinLength = 8;
                        break;
                }
                $resPwdMin = $this->ctx->Site_Custom->updateLoginConfig(LoginConfig::PASSWORD_MINLENGTH, $pwdMinLength, "", $this->userId);
                $resPwdMax = $this->ctx->Site_Custom->updateLoginConfig(LoginConfig::PASSWORD_MAXLENGTH, $pwdMaxLength, "", $this->userId);
                $resPwdErrorNum = $this->ctx->Site_Custom->updateLoginConfig(LoginConfig::PASSWORD_ERROR_NUM, $pwdErrorNum, "", $this->userId);
                $resPwdContainCharaters = $this->ctx->Site_Custom->updateLoginConfig(LoginConfig::PASSWORD_CONTAIN_CHARACTERS, $pwdContainCharaters, "", $this->userId);
                $resPwdContainCharatersType = $this->ctx->Site_Custom->updateLoginConfig(LoginConfig::PASSWORD_CONTAIN_CHARACTER_TYPE, $pwdType, "", $this->userId);

                if($resPwdMin && $resPwdMax && $resPwdErrorNum && $resPwdContainCharaters && $resPwdContainCharatersType) {
                    $result["errCode"] = "success";
                }
                echo json_encode($result);
                return;
            }
            $value = "";

            switch ($key) {
                case LoginConfig::LOGINNAME_MINLENGTH:
                    $loginNameMinLength = $_POST['value'];
                    $value = $loginNameMinLength;
                    $this->checkLoginNameLength("min");
                    break;
                case LoginConfig::LOGINNAME_MAXLENGTH:
                    $loginNameMaxLength = $_POST['value'];
                    $value = $loginNameMaxLength;
                    $this->checkLoginNameLength("max");
                    break;
                case LoginConfig::PASSWORD_MINLENGTH:
                    $pwdMinLength = $_POST['value'];
                    $value = $pwdMinLength;
                    $this->checkPwdLength("min");
                    break;
                case LoginConfig::PASSWORD_MAXLENGTH:
                    $pwdMaxLength = $_POST['value'];
                    $value = $pwdMaxLength;
                    $this->checkPwdLength("max");
                    break;
                case LoginConfig::PASSWORD_ERROR_NUM:
                    $value = (int)$_POST['value'];
                    break;
                case LoginConfig::PASSWORD_CONTAIN_CHARACTERS:
                    $value = trim( $_POST['value'], ",");
                    break;
            }
            $res = $this->ctx->Site_Custom->updateLoginConfig($key, $value, "", $this->userId);
            if ($res) {
                $result["errCode"] = "success";
            }

        } catch (Throwable $e) {
            $this->logger->error("manage.security.update", $e);
            $result["errInfo"] = $e->getMessage();
        }

        echo json_encode($result);
        return;
    }

    private function checkLoginNameLength($type)
    {
        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();

        if($type == "min") {
            $loginNameMinLength = $_POST['value'];
            $loginNameMaxLengthConfig = $loginConfig[LoginConfig::LOGINNAME_MAXLENGTH];
            $loginNameMaxLength = isset($loginNameMaxLengthConfig['configValue']) ? $loginNameMaxLengthConfig['configValue'] : "32" ;
        }else {
            $loginNameMaxLength = $_POST['value'];
            $loginNameMinLengthConfig = $loginConfig[LoginConfig::LOGINNAME_MINLENGTH];
            $loginNameMinLength = isset($loginNameMinLengthConfig['configValue']) ? $loginNameMinLengthConfig['configValue'] : "6" ;
        }
//
//        if($loginNameMinLength < $this->loginNameMinLength) {
//            $info = ZalyText::getText('text.loginName.minLength', $this->language);
//            throw new Exception($info);
//        }
        if($loginNameMaxLength < $loginNameMinLength) {
            $info = ZalyText::getText('text.loginName.MaxLengthLessThanMinLength', $this->language);
            throw new Exception($info);
        }
    }

    private function checkPwdLength($type)
    {
        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();
        if($type == "min") {
            $pwdMinLength = $_POST['value'];
            $pwdMaxLengthConfig = $loginConfig[LoginConfig::PASSWORD_MAXLENGTH];
            $pwdMaxLength= isset($pwdMaxLengthConfig['configValue']) ? $pwdMaxLengthConfig['configValue'] : "32" ;
            if($pwdMinLength < $this->pwdMinLength) {
                $info = ZalyText::getText('text.pwd.minLength', $this->language);
                throw new Exception($info);
            }

        }else {
            $pwdMaxLength = $_POST['value'];
            $pwdMinLengthConfig = $loginConfig[LoginConfig::PASSWORD_MINLENGTH];
            $pwdMinLength = isset($pwdMinLengthConfig['configValue']) ? $pwdMinLengthConfig['configValue'] : "6" ;

            if ($pwdMaxLength > $this->pwdMaxLength) {
                $info = ZalyText::getText('text.pwd.maxLength', $this->language);
                throw new Exception($info);
            }
        }

        if($pwdMaxLength<$pwdMinLength) {
            $info = ZalyText::getText('text.pwd.MaxLengthLessThanMinLength', $this->language);
            throw new Exception($info);
        }

    }
}
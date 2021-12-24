<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 28/10/2018
 * Time: 11:18 AM
 */

class Api_Passport_PasswordBase extends BaseController
{
    private  $classNameForRequest;
    protected  $maxErrorNum = 5;
    protected  $loginNameMinLength=1;
    protected  $loginNameMaxLength=24;
    protected  $pwdMinLength=6;
    protected  $pwdMaxLength=32;
    protected  $pwdContainCharacters = "letter,number";
    protected  $passwordResetRequired = "";
    protected  $passwordRestWay = "email ";

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        parent::rpc($request, $transportData);
    }

    public function checkPasswordErrorNum($userId)
    {
        $operateDate = date("Y-m-d", time());
        $count = $this->ctx->PassportPasswordCountLogTable->getCountLogByUserId($userId, $operateDate);

        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();
        $passwordErrorNumConfig = isset($loginConfig[LoginConfig::PASSWORD_ERROR_NUM]) ? $loginConfig[LoginConfig::PASSWORD_ERROR_NUM] : "";
        $passwordErrorNum = isset($passwordErrorNumConfig["configValue"]) ? $passwordErrorNumConfig["configValue"] : $this->maxErrorNum;

        if($count>=$passwordErrorNum && $count !== false) {
            $errorInfo = ZalyText::getText("text.pwd.exceedNum", $this->language);
            $this->setRpcError("error.alert", $errorInfo);
            throw new Exception("loginName password is not match");
        }
    }

    protected function getCustomLoginConfig()
    {
        $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();

        $loginNameMinLengthConfig = isset($loginConfig[LoginConfig::LOGINNAME_MINLENGTH]) ? $loginConfig[LoginConfig::LOGINNAME_MINLENGTH] : "";
        $this->loginNameMinLength = isset($loginNameMinLengthConfig["configValue"]) ? $loginNameMinLengthConfig["configValue"] : $this->loginNameMinLength;

        $loginNameMaxLengthConfig = isset($loginConfig[LoginConfig::LOGINNAME_MAXLENGTH]) ? $loginConfig[LoginConfig::LOGINNAME_MAXLENGTH] : "";
        $this->loginNameMaxLength = isset($loginNameMaxLengthConfig["configValue"]) ? $loginNameMaxLengthConfig["configValue"] : $this->loginNameMaxLength;

        $pwdMinLengthConfig = isset($loginConfig[LoginConfig::PASSWORD_MINLENGTH]) ? $loginConfig[LoginConfig::PASSWORD_MINLENGTH] : "";
        $this->pwdMinLength = isset($pwdMinLengthConfig["configValue"]) ? $pwdMinLengthConfig["configValue"] : $this->pwdMinLength;


        $pwdMaxLengthConfig = isset($loginConfig[LoginConfig::PASSWORD_MAXLENGTH]) ? $loginConfig[LoginConfig::PASSWORD_MAXLENGTH] : "";
        $this->pwdMaxLength = isset($pwdMaxLengthConfig["configValue"]) ? $pwdMaxLengthConfig["configValue"] : $this->pwdMaxLength;


        $pwdContainCharactersConfig = isset($loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTERS]) ? $loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTERS] : "";
        $this->pwdContainCharacters = isset($pwdContainCharactersConfig["configValue"]) ? $pwdContainCharactersConfig["configValue"] : "";


        $passwordResetRequiredConfig = isset($loginConfig[LoginConfig::PASSWORD_RESET_REQUIRED]) ? $loginConfig[LoginConfig::PASSWORD_RESET_REQUIRED] : "";
        $this->passwordResetRequired = isset($passwordResetRequiredConfig["configValue"]) ? $passwordResetRequiredConfig["configValue"] : "";

        $passwordResetWayConfig = isset($loginConfig[LoginConfig::PASSWORD_RESET_WAY]) ? $loginConfig[LoginConfig::PASSWORD_RESET_WAY] : "";
        $this->passwordRestWay = isset($passwordResetWayConfig["configValue"]) ? $passwordResetWayConfig["configValue"] : $this->passwordRestWay;
    }

    public function  insertPassportPasswordLog($user, $type)
    {

        $opreateDate = date("Y-m-d", time());
        $opreateTime = ZalyHelper::getMsectime();
        $userId = $user['userId'];
        $loginName = $user['loginName'];
        $countLogData = [
            "userId" => $userId,
            "num" => 1,
            "operateDate" => $opreateDate,
            "operateTime" => $opreateTime,
        ];
        $updateCountData = [
            "userId" => $userId,
            "operateDate" => $opreateDate,
        ];
        $logData = [
            "userId"      => $userId,
            "loginName"   => $loginName,
            "operateDate" => $opreateDate,
            "operation"   => $type,
            "ip"          => ZalyHelper::getIp(),
            "operateTime" => ZalyHelper::getMsectime(),
        ];

        $this->ctx->PassportPasswordCountLogTable->insertCountLogData($countLogData, $updateCountData, $logData);
    }

}
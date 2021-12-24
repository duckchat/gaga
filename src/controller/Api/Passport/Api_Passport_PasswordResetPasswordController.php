<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 24/08/2018
 * Time: 11:46 AM
 */

class Api_Passport_PasswordResetPasswordController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPassportPasswordResetPasswordRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPassportPasswordResetPasswordResponse';
    private $tokenExipreTime = 600000;///10分钟

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPassportPasswordResetPasswordRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try{
            $token = $request->getToken();
            $password = $request->getPassword();
            $loginName = $request->getLoginName();
            $this->checkPassword($password);
            $this->checkToken($loginName, $token);
            $this->updatePasswordByLoginName($loginName, $password);
            $this->setRpcError($this->defaultErrorCode, "");
        }catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
        }
        $this->rpcReturn($transportData->getAction(),  new $this->classNameForResponse());
    }


    private function checkPassword($password)
    {
        $this->getCustomLoginConfig();
        if(!$password || (strlen($password) > $this->pwdMaxLength) || (strlen($password) < $this->pwdMinLength)) {
            $errorCode = $this->zalyError->errorPassowrdLength;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError("error.alert", $errorInfo);
            throw new Exception($errorInfo);
        }

        $flag = ZalyHelper::verifyChars($password, $this->pwdContainCharacters);
        if(!$flag) {
            $errorInfo = ZalyText::getText("text.pwd.type", $this->language);
            $this->setRpcError("error.alert", $errorInfo);
            throw new Exception($errorInfo);
        }
    }
    private function  checkToken($loginName, $token)
    {
        $codeInfo = $this->ctx->PassportPasswordTokenTable->getCodeInfoByLoginName($loginName);
        $time = ZalyHelper::getMsectime();
        $tokenTime  = $codeInfo['timeReg'];
        $timeExpire =  $time - $tokenTime;

        if(!$codeInfo || $token !== $codeInfo['token'] || ($timeExpire > $this->tokenExipreTime)) {
            $errorCode = $this->zalyError->errorVerifyToken;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("token is not exist");
        }
        $this->ctx->PassportPasswordTokenTable->delCodeInfoByLoginName($loginName);
    }

    private function updatePasswordByLoginName($loginName, $password)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try{
           $where = [
               "loginName" => $loginName
           ];
           $data = [
               "password" => password_hash($password, PASSWORD_BCRYPT)
           ];
           $this->ctx->PassportPasswordTable->updateUserData($where, $data);
       }catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $errorCode = $this->zalyError->errorUpdatePwd;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("update password failed ");
       }
    }
}
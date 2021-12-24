<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 10/09/2018
 * Time: 10:46 AM
 */


class Api_Passport_PasswordModifyPasswordController extends Api_Passport_PasswordBase
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPassportPasswordModifyPasswordRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPassportPasswordModifyPasswordResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPassportPasswordModifyPasswordRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try{
            $newPassword = $request->getNewPassword();
            $password = $request->getPassword();
            $loginName = $request->getLoginName();
            $this->checkPassword($newPassword);
            $this->checkOldPassword($loginName, $password);
            $this->updatePasswordByLoginName($loginName, $newPassword);
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

    private function  checkOldPassword($loginName, $password)
    {
        $userInfo = $this->ctx->PassportPasswordTable->getUserByLoginName($loginName);
        if($userInfo == false) {
            $errorCode = $this->zalyError->errorExistUser;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("user is not exists");
        }

        $this->checkPasswordErrorNum($userInfo['userId']);

        if(!password_verify($password, $userInfo['password'])) {
            $this->insertPassportPasswordLog($userInfo, 2);
            $errorCode = $this->zalyError->errorMatchLogin;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("loginName password is not match");
        }
        $operateDate = date("Y-m-d", time());
        $this->ctx->PassportPasswordCountLogTable->deleteCountLogDataByUserId($userInfo['userId'], $operateDate);
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
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/08/2018
 * Time: 2:46 PM
 */

class Api_Passport_PasswordRegController extends Api_Passport_PasswordBase
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPassportPasswordRegRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPassportPasswordRegResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPassportPasswordRegRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            header('Access-Control-Allow-Origin: *');
            $loginName = $request->getLoginName();
            $email = $request->getEmail();
            $password = $request->getPassword();
            $sitePubkPem = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ID_PUBK_PEM);

            $invitationCode = $request->getInvitationCode();

            $this->getCustomLoginConfig();

            $loginName = trim($loginName);
            if (!$loginName || mb_strlen($loginName) > $this->loginNameMaxLength || mb_strlen($loginName) < $this->loginNameMinLength) {
                $errorCode = $this->zalyError->errorLoginNameLength;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                throw new Exception($errorInfo);
            }

            if (!$password || (strlen($password) > $this->pwdMaxLength) || (strlen($password) < $this->pwdMinLength)) {
                $errorCode = $this->zalyError->errorPassowrdLength;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                throw new Exception($errorInfo);
            }

            $flag = ZalyHelper::verifyChars($password, $this->pwdContainCharacters);
            if (!$flag) {
                $errorInfo = ZalyText::getText("text.pwd.type", $this->language);
                throw new Exception($errorInfo);
            }


            $nickname = $request->getNickname();
            if (empty($nickname)) {
                $nickname = $loginName;
            }

            if (!$sitePubkPem || strlen($sitePubkPem) < 0) {
                $errorCode = $this->zalyError->errorSitePubkPem;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                throw new Exception($errorInfo);
            }

            if ($this->passwordResetRequired == 1 && mb_strlen(trim($email)) < 1) {
                $tip = ZalyText::getText("text.param.void", $this->language);
                $errorInfo = $this->passwordRestWay . " " . $tip;
                $this->setRpcError("error.alert", $errorInfo);
                throw new Exception("$errorInfo  is  not exists");
            }

            $this->checkLoginName($loginName);
            $preSessionId = $this->registerUserForPassport($loginName, $email, $password, $nickname, $invitationCode, $sitePubkPem);
            $response = new \Zaly\Proto\Site\ApiPassportPasswordRegResponse();
            $response->setPreSessionId($preSessionId);
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            $this->setRpcError("error.alert", $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    private function checkLoginName($loginName)
    {
        $user = $this->ctx->PassportPasswordTable->getUserByLoginName($loginName);
        if ($user) {
            $errorCode = $this->zalyError->errorExistLoginName;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            throw new Exception($errorInfo);
        }
    }

    private function registerUserForPassport($loginName, $email, $password, $nickname, $invitationCode, $sitePubkPem)
    {
        try {
            $tag = __CLASS__ . '-' . __FUNCTION__;

            $this->ctx->BaseTable->db->beginTransaction();
            $userId = ZalyHelper::generateStrId();
            $userInfo = [
                "userId" => $userId,
                "loginName" => $loginName,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_BCRYPT),
                "nickname" => $nickname,
                "invitationCode" => $invitationCode,
                "timeReg" => ZalyHelper::getMsectime()
            ];
            $this->ctx->PassportPasswordTable->insertUserInfo($userInfo);
            $preSessionId = ZalyHelper::generateStrId();

            $preSessionInfo = [
                "userId" => $userId,
                "preSessionId" => $preSessionId,
                "sitePubkPem" => base64_encode($sitePubkPem)
            ];
            $this->ctx->PassportPasswordPreSessionTable->insertPreSessionData($preSessionInfo);

            $this->ctx->BaseTable->db->commit();
            return $preSessionId;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            $this->ctx->BaseTable->db->rollback();
            throw new Exception($ex);
        }
    }

}
<?php

/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/08/2018
 * Time: 4:37 PM
 */
class Api_Passport_PasswordLoginController extends Api_Passport_PasswordBase
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPassportPasswordLoginRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPassportPasswordLoginResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPassportPasswordLoginRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        header('Access-Control-Allow-Origin: *');
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $loginName = $request->getLoginName();
            $password = $request->getPassword();
            $sitePubkPem =  $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ID_PUBK_PEM);
            $user = $this->verifyUserInfo($loginName, $password);
            $preSessionId = $this->generatePreSessionId($user, $sitePubkPem);

            $response = new \Zaly\Proto\Site\ApiPassportPasswordLoginResponse();
            $response->setPreSessionId($preSessionId);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    private function verifyUserInfo($loginName, $password)
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;
        $user = $this->ctx->PassportPasswordTable->getUserByLoginName($loginName);

        if (!$user) {
            $errorCode = $this->zalyError->errorExistUser;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("loginName is not exist");
        }

        $this->checkPasswordErrorNum($user['userId']);

        if (!password_verify($password, $user['password'])) {
            $this->insertPassportPasswordLog($user, 1);
            $errorCode = $this->zalyError->errorMatchLogin;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("loginName password is not match");
        }
        $operateDate = date("Y-m-d", time());
        $this->ctx->PassportPasswordCountLogTable->deleteCountLogDataByUserId($user['userId'], $operateDate);
        return $user;
    }


    private function generatePreSessionId($user, $sitePubkPem)
    {
        $preSessionId = ZalyHelper::generateStrId();

        try {
            $preSessionInfo = [
                "userId" => $user['userId'],
                "preSessionId" => $preSessionId,
                "sitePubkPem" => base64_encode($sitePubkPem)
            ];

            $this->ctx->PassportPasswordPreSessionTable->insertPreSessionData($preSessionInfo);
        } catch (Exception $ex) {
            $preSessionInfo = [
                "preSessionId" => $preSessionId,
                "sitePubkPem" => base64_encode($sitePubkPem)
            ];
            $where = [
                "userId" => $user['userId']
            ];
            $this->ctx->PassportPasswordPreSessionTable->updatePreSessionData($where, $preSessionInfo);
        }
        return $preSessionId;
    }
}
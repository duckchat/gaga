<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 24/08/2018
 * Time: 10:42 AM
 */

class Api_Passport_PasswordFindPasswordController extends  BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPassportPasswordFindPasswordRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPassportPasswordFindPasswordResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPassportPasswordFindPasswordRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__.'_'.__FUNCTION__;
        try{
            $loginName = $request->getLoginName();
            $this->sendEmail($loginName);
            $this->setRpcError($this->defaultErrorCode, "");
        }catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
        }
        $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
    }

    private function sendEmail($loginName)
    {
        $tag = __CLASS__.'_'.__FUNCTION__;
        $user = $this->ctx->PassportPasswordTable->getUserByLoginName($loginName);
        $this->ctx->Wpf_Logger->info($tag , json_encode($user));

        if(!$user) {
            $errorCode = $this->zalyError->errorExistUser;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            throw new Exception("loginName is not exist");
        }
        $toEmail = $user['email'];
        $token = ZalyHelper::generateStrKey(6);
        $sendSiteNames = $this->ctx->SiteConfigTable->selectSiteConfig(SiteConfig::SITE_NAME);
        $sendSiteName  = $sendSiteNames[SiteConfig::SITE_NAME];
        try{
            $codeInfo = [
                "loginName" => $user['loginName'],
                "token"     => $token,
                "timeReg"   => ZalyHelper::getMsectime(),
            ];
            $this->ctx->PassportPasswordTokenTable->insertCodeInfo($codeInfo);
            $this->ctx->ZalyMail->sendEmail($toEmail, $token, $sendSiteName);
        }catch (Exception $ex) {
            $where = [
                "loginName" => $user['loginName']
            ];
            $data = [
                "token"   => $token,
                "timeReg" => ZalyHelper::getMsectime(),
            ];
            $this->ctx->PassportPasswordTokenTable->updateCodeInfo($where, $data);
            $this->ctx->ZalyMail->sendEmail($toEmail, $token, $sendSiteName);
        }
    }
}
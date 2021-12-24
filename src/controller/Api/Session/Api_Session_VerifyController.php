<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/08/2018
 * Time: 7:17 PM
 */


class Api_Session_VerifyController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Platform\ApiSessionVerifyRequest';
    private $classNameForResponse = '\Zaly\Proto\Platform\ApiSessionVerifyResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpcResponseClassName()
    {
        return $this->classNameForResponse;
    }

    /**
     * @param \Zaly\Proto\Platform\ApiSessionVerifyRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
//        try {
//            $preSessionId = $request->getPreSessionId();
//            $preSessionId = trim($preSessionId);
//            $errorCode = $this->zalyError->errorPreSessionId;
//            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
//
//            if (!$preSessionId) {
//                $this->setRpcError($errorCode, $errorInfo);
//                throw new Exception("401 ");
//            }
//
//            $verifyResult = $this->ctx->Site_SessionVerify->doApiVerify($preSessionId);
//
//            $loginProfile = $verifyResult["loginProfile"];
//            $sitePubkPem = $verifyResult["sitePubkPem"];
//
//            if (!$loginProfile || !$sitePubkPem) {
//                throw new Exception("session verify with error loginProfile or sitePubkPem.");
//            }
//
//            $response = $this->buildApiSessionVerifyResponse($sitePubkPem, $loginProfile);
//            $this->returnSuccessRPC($response);
//        } catch (Exception $ex) {
//            $this->ctx->Wpf_Logger->info($tag, $ex->getMessage() . "\n" . $ex->getTraceAsString());
//            $this->returnErrorRPC(new $this->classNameForResponse(), $ex);
//        }
        return;
    }

    private function buildApiSessionVerifyResponse($sitePubkPem, $pluginUserProfile)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        try {
            //16位的随机字符串
            $randomKey = ZalyHelper::generateStrKey(16);
            $secretKey = $this->ctx->ZalyRsa->encrypt($randomKey, $sitePubkPem);
            $aesStr = $this->ctx->ZalyAes->encrypt(serialize($pluginUserProfile), $randomKey);
            $response = new \Zaly\Proto\Platform\ApiSessionVerifyResponse();
            $response->setKey($secretKey);
            $response->setEncryptedProfile($aesStr);
            return $response;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->info($tag, " error_msg=" . $ex);
            throw new Exception("get response failed");
        }
    }

}
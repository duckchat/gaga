<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 07/09/2018
 * Time: 12:56 PM
 */

class Api_Passport_PasswordUpdateInvitationCodeController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPassportPasswordUpdateInvitationCodeRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPassportPasswordUpdateInvitationCodeResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPassportPasswordUpdateInvitationCodeRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try{
            $invitationCode = $request->getInvitationCode();
            $preSessionId = $request->getPreSessionId();
            $sitePubkPem =  $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ID_PUBK_PEM);

            if(strlen($invitationCode) < 0 ) {
                $errorCode = $this->zalyError->errorUpdateInvitation;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception("invitationCode  is  not exists");
            }

            $preSessionId = $this->updateUserInfo($preSessionId, $invitationCode, $sitePubkPem);
            $response = $this->getResponse($preSessionId);
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(),  $response);
        }catch (Exception $ex) {
            $errorCode = $this->zalyError->errorUpdateInvitation;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }

    }

    public function updateUserInfo($preSessionId, $invitationCode, $sitePubkPem)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;

        try{
            $userInfo = $this->ctx->PassportPasswordPreSessionTable->getInfoByPreSessionId($preSessionId);

            if($userInfo == false) {
                throw new Exception("preSessionId for update is null");
            }
            $userId = $userInfo['userId'];

            $userInfo = $this->ctx->PassportPasswordTable->getUserByUserId($userId);

            $this->ctx->BaseTable->db->beginTransaction();

            if($userInfo['invitationCode'] !== $invitationCode) {
                $updateData = [
                    "invitationCode" => $invitationCode,
                ];
                $where = ["userId" => $userId];

                $this->ctx->PassportPasswordTable->updateUserData($where, $updateData);
            }

            $newPreSessionId = ZalyHelper::generateStrId();

            $preSessionInfo = [
                "userId" => $userId,
                "preSessionId" => $newPreSessionId,
                "sitePubkPem" => base64_encode($sitePubkPem)
            ];

            $updatePreSessionWhere = [
                "userId" =>  $userId,
            ];
            $this->ctx->PassportPasswordPreSessionTable->updatePreSessionData($updatePreSessionWhere, $preSessionInfo);

            $this->ctx->BaseTable->db->commit();
            return $newPreSessionId;
        }catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->ctx->BaseTable->db->rollback();
            throw new Exception($ex->getMessage());
        }
    }

    public function getResponse($preSessionId)
    {
        $response = new \Zaly\Proto\Site\ApiPassportPasswordUpdateInvitationCodeResponse();
        $response->setPreSessionId($preSessionId);
        return $response;
    }
}
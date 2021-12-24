<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 08/09/2018
 * Time: 10:25 AM
 */

class MiniProgram_Passport_AccountController extends MiniProgram_BaseController
{
    private $passporAccountPluginId = 105;
    private $errorCode = "";
    private $sessionClear  = "duckchat.session.clear";
    private $resetPassword = "api.passport.passwordModifyPassword";
    protected  $pwdMinLength=6;
    protected  $pwdMaxLength=32;
    protected  $pwdContainCharacters = "letter,number";

    public function getMiniProgramId()
    {
        return $this->passporAccountPluginId;
    }

    public function requestException($ex)
    {
        $this->showPermissionPage();
    }

    public function preRequest()
    {
    }

    public function doRequest()
    {

        header('Access-Control-Allow-Origin: *');

        $tag = __CLASS__.'-'.__FUNCTION__;
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        try{
            if($method == "post") {

                $loginName = $_POST['loginName'];
                $siteLoginName = $this->loginName;
                if($siteLoginName != $loginName) {
                    $errorCode = $this->zalyError->errorUpdatePasswordLoginName;
                    $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                    echo json_encode(["errCode" => $errorInfo]);
                    return;
                }

                $this->modifyPassportPassword($loginName);
                echo json_encode(["errCode" => "success"]);
                return;
            } else {
                $loginConfig = $this->ctx->Site_Custom->getLoginAllConfig();

                $pwdMinLengthConfig = isset($loginConfig[LoginConfig::PASSWORD_MINLENGTH]) ? $loginConfig[LoginConfig::PASSWORD_MINLENGTH] : "";
                $pwdMinLength = isset($pwdMinLengthConfig["configValue"]) ? $pwdMinLengthConfig["configValue"] : $this->pwdMinLength;

                $pwdMaxLengthConfig = isset($loginConfig[LoginConfig::PASSWORD_MAXLENGTH]) ? $loginConfig[LoginConfig::PASSWORD_MAXLENGTH] : "";
                $pwdMaxLength = isset($pwdMaxLengthConfig["configValue"]) ? $pwdMaxLengthConfig["configValue"] : $this->pwdMaxLength;

                $pwdContainCharactersConfig = isset($loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTERS]) ? $loginConfig[LoginConfig::PASSWORD_CONTAIN_CHARACTERS] : "";
                $pwdContainCharacters = isset($pwdContainCharactersConfig["configValue"]) ? $pwdContainCharactersConfig["configValue"] : "";


                $this->ctx->Wpf_Logger->error($tag, "duckchat.session.clear userUd == ".$this->userId);
                echo $this->display("miniProgram_passport_account", [
                        'passporAccountPluginId' => $this->passporAccountPluginId,
                        'passwordMinLength' => $pwdMinLength,
                        'passwordMaxLength' => $pwdMaxLength,
                        'passwordContainCharacters' => $pwdContainCharacters
                    ]
                );
                return;
            }
        }catch (Exception $ex) {
            $errorCode = $this->errorCode ? $this->errorCode : "修改失败";
            echo json_encode(["errCode" => $errorCode]);
            return;
        }
    }

    private function modifyPassportPassword($loginName)
    {
        $tag = __CLASS__ . "-". __FUNCTION__;

        try{
            $sessionClearRequest = new \Zaly\Proto\Plugin\DuckChatSessionClearRequest();
            $sessionClearRequest->setUserId($this->userId);
            $this->requestDuckChatInnerApi($this->passporAccountPluginId, $this->sessionClear, $sessionClearRequest);
        }catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, $ex->getMessage());
            throw new Exception($ex->getMessage());
        }
    }
}
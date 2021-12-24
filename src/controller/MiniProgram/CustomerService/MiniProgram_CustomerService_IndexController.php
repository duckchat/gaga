<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 21/11/2018
 * Time: 11:09 AM
 */

class MiniProgram_CustomerService_IndexController extends MiniProgram_BaseController
{

    private $gifMiniProgramId = 107;
    private $title = "客服小程序";
    private $defaultGreeting = "您好，很高兴为您服务。";
    private $defaultChatTitle = "客服系统";
    private $defaultSign = "";

    public function getMiniProgramId()
    {
        return $this->gifMiniProgramId;
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
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $tag = __CLASS__ . "-" . __FUNCTION__;

        if($method == 'get') {
            $settingConfig = $this->getCustomerServiceSetting();
            $settingConfig['lang'] = $this->language;

            $url = ZalyHelper::getRequestAddressPath().'/?action=page.customerService.index&signature='.$settingConfig[ MiniProgram_CustomerService_ConfigController::SIGN_VERIFY_KEY];
            $customerServiceCode = <<<CODE
<div style="width:380px;position: fixed; top:0; bottom:0;right:0px;z-index:9000;">
    <iframe src="$url" frameborder="no" height="100%" width="100%">
</div>
CODE;
            $settingConfig['code'] = $customerServiceCode;

            echo $this->display("miniProgram_customerService_index", $settingConfig);
        } else {
            $operation = $_POST['operation'];
            $key = $_POST['key'];
            $value = $_POST['value'];

            switch ($operation) {
                case "update":
                    $flag = $this->updateCustomerServiceSetting($key, $value);
                    echo $flag ? json_encode(['errCode' => 'success']):  json_encode(['errCode' => 'fail']);
                    break;
            }
        }
    }

    protected function getCustomerServiceSetting()
    {
        $settingConfig = [
            MiniProgram_CustomerService_ConfigController::ENABLE_CUSTOMER_SERVICE => "",
            MiniProgram_CustomerService_ConfigController::CHAT_TITLE => $this->defaultChatTitle,
            MiniProgram_CustomerService_ConfigController::GREETING => $this->defaultGreeting,
            MiniProgram_CustomerService_ConfigController::SIGN_VERIFY_KEY => $this->defaultSign,
        ];
        $setting = $this->ctx->SiteCustomerServiceSettingTable->getCustomerServiceSettingLists();
        if($setting) {
            $settingCustomerServiceConfig = array_column($setting, 'serviceValue', 'serviceKey');
            $settingConfig = array_merge($settingConfig, $settingCustomerServiceConfig);
        }
        return $settingConfig;
    }

    protected function updateCustomerServiceSetting($key, $value)
    {
        $flag = false;
        try{
            $info = [
                'serviceKey' => $key,
                'serviceValue' => $value,
            ];
            $flag = $this->ctx->SiteCustomerServiceSettingTable->insertCustomerServiceSettingData($info);
        }catch (Exception $error) {
            $where = [
                'serviceKey' => $key,
            ];
            $data = [
                'serviceValue' => $value,
            ];
            $flag = $this->ctx->SiteCustomerServiceSettingTable->updateCustomerServiceData($where, $data);
        }
        try{
            if($key == MiniProgram_CustomerService_ConfigController::ENABLE_CUSTOMER_SERVICE) {
                $signVerifyKey = isset( $_POST['signVerifyKey']) ?  $_POST['signVerifyKey'] : "";
                if(!$signVerifyKey) {
                    $signVerifyKey = md5(ZalyHelper::generateStrKey());
                    $info = [
                        'serviceKey'   => MiniProgram_CustomerService_ConfigController::SIGN_VERIFY_KEY,
                        'serviceValue' => $signVerifyKey,
                    ];
                    $flag = $this->ctx->SiteCustomerServiceSettingTable->insertCustomerServiceSettingData($info);
                }
            }
        }catch (Exception $ex) {

        }
        return $flag;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/11/2018
 * Time: 10:26 AM
 */

class Page_CustomerService_IndexController extends CustomerServiceController
{
    protected  $thirdLoginKey = 'DuckChat_CustomerService';
    private $defaltGreeting = "您好，很高兴为您服务。";
    private $defaltChatTitle = "客服系统";

    public function index()
    {
        header('Access-Control-Allow-Origin: *');
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        $setting = $this->getCustomerServiceSetting();

        $greetings = isset($setting[MiniProgram_CustomerService_ConfigController::GREETING]) ?$setting[MiniProgram_CustomerService_ConfigController::GREETING] : $this->defaltGreeting;
        if($method == "post") {
            $operation = $_POST['operation'];
            switch ($operation) {
                case "create":
                    $this->createCustomerAccount();
                    break;
                case "login":
                    $this->getLoginGetPreSessionId();
                    break;
                case "addFriend":
                    $this->addCustomerServiceForFriend($greetings);
                    break;
            }
            return;
        }

        try{
            $this->getUserIdByServiceCookie();
        }catch (Exception $ex) {
        }
        $params['enableCustomerService'] = 1;
        if(!isset($setting[MiniProgram_CustomerService_ConfigController::ENABLE_CUSTOMER_SERVICE]) || $setting[MiniProgram_CustomerService_ConfigController::ENABLE_CUSTOMER_SERVICE] != 1) {
            $params['enableCustomerService'] = 0;
        }
        $params['signatureError'] = 0;
        $signature = isset($_GET['signature']) ? $_GET['signature'] : "";
        if(isset($setting[MiniProgram_CustomerService_ConfigController::SIGN_VERIFY_KEY]) && $setting[MiniProgram_CustomerService_ConfigController::SIGN_VERIFY_KEY] != $signature ) {
            $params['enableCustomerService'] = 0;
            $params['signatureError'] = 1;
        }
        $params['chatTitle'] = isset($setting[MiniProgram_CustomerService_ConfigController::CHAT_TITLE]) ?$setting[MiniProgram_CustomerService_ConfigController::CHAT_TITLE] : $this->defaltChatTitle;
        $params['thirdLoginKey'] = $this->thirdLoginKey;
        echo $this->display("customerService_index", $params);
    }

    //create customer account
    protected function createCustomerAccount()
    {
        try{
            $userId = ZalyHelper::generateStrId();
            $loginName =  ZalyHelper::generateStrId();
            $userInfo  = [
                'userId'    => $userId,
                'loginName' => $loginName,
                'password'  => password_hash($userId, PASSWORD_BCRYPT),
                'timeReg'   => ZalyHelper::getMsectime(),
            ];
            $this->ctx->PassportCustomerServiceTable->insertUserInfo($userInfo);
            $preSessionId = $this->getPreSessionId($userId);
            if($preSessionId) {
                echo json_encode(['errorCode' => 'success', 'preSessionId' => $preSessionId, 'loginName' => $loginName]);
                return;
            }

            echo json_encode(['errorCode' => 'failed']);
        }catch (Exception $ex) {
            echo json_encode(['errorCode' => 'failed']);
        }
    }

    protected function getLoginGetPreSessionId()
    {
        $tag = __CLASS__.'->'.__FUNCTION__;

        try{
            $this->getUserIdByServiceCookie();
            if(!$this->userId) {
                return $this->createCustomerAccount();
            }
            echo json_encode(['errorCode' => 'success', 'userId' => $this->userId, 'sessionId' => $this->sessionId]);
            return;
        }catch (Exception $ex) {
            $this->ctx->getLogger()->error($tag, $ex);
            echo json_encode(['errorCode' => 'failed']);
        }
    }
    //generat preSessionId
    protected function getPreSessionId($userId)
    {
        $tag = __CLASS__.'->'.__FUNCTION__;
        try{
            $this->ctx->PassportCustomerServicePreSessionTable->delInfoByUserId($userId);

            $info = [
                "userId" => $userId,
                "preSessionId" => ZalyHelper::generateStrId(),
            ];
            $this->ctx->PassportCustomerServicePreSessionTable->insertPreSessionData($info);
            return $info['preSessionId'];
        }catch (Exception $ex) {
            $this->ctx->getLogger()->error($tag, $ex);
            return '';
        }
    }

    public function addCustomerServiceForFriend($greetings)
    {
        $customerServiceId = $this->getCustomerServiceId();
        $customerId = $_POST['customerId'];
        $tag = __CLASS__.'->'.__FUNCTION__;
        try{
            if($customerServiceId) {
                $this->ctx->Manual_Friend->addFriend($customerId, $customerServiceId,  $greetings);
            }
            echo json_encode(['customerServiceId' => $customerServiceId]);
            return;
        }catch (Exception $ex) {
            $this->ctx->getLogger()->error($tag, $ex);
        }
        echo json_encode(['customerServiceId' => ""]);
    }

    protected function getCustomerServiceSetting()
    {
        $settingConfig = [
            MiniProgram_CustomerService_ConfigController::ENABLE_CUSTOMER_SERVICE => "",
            MiniProgram_CustomerService_ConfigController::GREETING => $this->defaltGreeting,
        ];
        $setting = $this->ctx->SiteCustomerServiceSettingTable->getCustomerServiceSettingLists();
        if($setting) {
            $settingCustomerServiceConfig = array_column($setting, 'serviceValue', 'serviceKey');
            $settingConfig = array_merge($settingConfig, $settingCustomerServiceConfig);
        }
        return $settingConfig;
    }

    public function getCustomerServiceId()
    {
        $results = $this->ctx->SiteCustomerServiceTable->getCustomerService();
        $serviceId = "";
        if($results) {
            $serviceInfo = $results[0];
            $where = [
                'userId' => $serviceInfo['userId'],
            ];
            $data  = [
               'serviceTime' => ZalyHelper::getMsectime()
            ];
            $this->ctx->SiteCustomerServiceTable->updateCustomerServiceData($where, $data);
            $serviceId = $serviceInfo['userId'];
        }
        return $serviceId;
    }
}
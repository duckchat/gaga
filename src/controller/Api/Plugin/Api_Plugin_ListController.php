<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 11:23 AM
 */

class Api_Plugin_ListController extends \BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPluginListRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPluginListResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPluginListRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $pluginUsageType = (int)$request->getUsageType();

            if ($pluginUsageType === false) {
                $errorCode = $this->zalyError->errorPluginList;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }

            $permissionTypes = [
                Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
                Zaly\Proto\Core\PluginPermissionType::PluginPermissionGroupMaster,
            ];

            $isManager = $this->ctx->Site_Config->isManager($this->userId);
            if ($isManager) {
                $permissionTypes[] = Zaly\Proto\Core\PluginPermissionType::PluginPermissionAdminOnly;
            }

            switch ($pluginUsageType) {
                case Zaly\Proto\Core\PluginUsageType::PluginUsageNone:
                case Zaly\Proto\Core\PluginUsageType::PluginUsageIndex:
                case Zaly\Proto\Core\PluginUsageType::PluginUsageU2Message:
                case Zaly\Proto\Core\PluginUsageType::PluginUsageTmpMessage:
                case Zaly\Proto\Core\PluginUsageType::PluginUsageGroupMessage:
                case Zaly\Proto\Core\PluginUsageType::PluginUsageAccountSafe:
                    break;
                case Zaly\Proto\Core\PluginUsageType::PluginUsageLogin:
                default:
                    throw new Exception("mini program usageType error");
            }

            $pluginList = $this->getPluginListFromDB($pluginUsageType, $permissionTypes);

            $this->logger->info($this->action, "plugin count:" . count($pluginList));

            $pluginPublicKey = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_PLUGIN_PLBLIC_KEY);
            $response = $this->buildApiPluginListResponse($this->sessionId, $pluginList, $pluginPublicKey);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            $this->setRpcError("error.alert", $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }

        return;
    }

    /**
     * 从数据库获取
     * @param $usageType
     * @param $permissionTypes
     * @return array
     */
    private function getPluginListFromDB($usageType, $permissionTypes)
    {
        return $this->ctx->SitePluginTable->getPluginList($usageType, $permissionTypes);
    }

    /**
     * 获取plugin list
     * @param $sessionId
     * @param $pluginList
     * @param $pluginPublicKey
     * @return \Zaly\Proto\Site\ApiPluginListResponse
     */
    private function buildApiPluginListResponse($sessionId, $pluginList, $pluginPublicKey)
    {
        $response = new \Zaly\Proto\Site\ApiPluginListResponse();
        $list = [];
        foreach ($pluginList as $key => $plugin) {
            $pluginProfile = new \Zaly\Proto\Core\PluginProfile();

            $pluginProfile->setId($plugin['pluginId']);
            $pluginProfile->setName($plugin['name']);
            $pluginProfile->setLogo($plugin['logo']);

            $pluginProfile->setUsageTypes([$plugin['usageType']]);
            if ($plugin['sort']) {
                $pluginProfile->setOrder($plugin['sort']);
            } else {
                $pluginProfile->setOrder(100);
            }

            $pluginProfile->setLandingPageUrl($plugin['landingPageUrl']);
            $pluginProfile->setAdminPageUrl($plugin['management']);

            if ($plugin['landingPageWithProxy'] == 1) {//1:true 0:false
                $pluginProfile->setLandingPageWithProxy(true);
            }

            if ($plugin['loadingType']) {
                $pluginProfile->setLoadingType($plugin['loadingType']);
            } else {
                $pluginProfile->setLoadingType(\Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage);
            }

            $pluginAuthKey = $plugin['authKey'];

            if (empty($pluginAuthKey)) {
                $pluginAuthKey = $pluginPublicKey;
            }

            $encryptedSessionId = $this->ctx->ZalyAes->encrypt($sessionId, $pluginAuthKey);

            $base64SessionId = ZalyBase64::base64url_encode($encryptedSessionId);

            $pluginProfile->setUserSessionId($base64SessionId);

            $list[] = $pluginProfile;
        }
        $response->setList($list);
        return $response;
    }
}
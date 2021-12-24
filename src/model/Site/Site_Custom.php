<?php
/**
 *
 * 站点自定义
 *  - 登陆自定义
 *  - 其他自定义
 *
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 4:44 PM
 */

class Site_Custom
{

    private $ctx;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
    }


    public function getLoginAllConfig()
    {
        return $this->ctx->SiteLoginCustomTable->getAllCustomConfig();
    }

    public function getLoginConfig($configKey)
    {
        return $this->ctx->SiteLoginCustomTable->getCustomConfigValue($configKey);
    }

    public function updateLoginConfig($configKey, $configValue, $configValueEN = "", $updateUserId = "")
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $result = $this->ctx->SiteLoginCustomTable->updateConfig($configKey, $configValue, $configValueEN, $updateUserId);
            if (!$result) {
                return $this->ctx->SiteLoginCustomTable->insertConfig($configKey, $configValue, $configValueEN);
            }

            return $result;
        } catch (Exception $e) {
            $this->ctx->getLogger()->error($tag, $e);
        }

        return false;
    }

}
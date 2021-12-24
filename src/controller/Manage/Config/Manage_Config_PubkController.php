<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:58 AM
 */

class Manage_Config_PubkController extends Manage_CommonController
{
    /**
     * 站点管理
     */
    public function doRequest()
    {
        $response = [];
        try {
            $params['lang'] = $this->language;

            $params['pubkPem'] = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ID_PUBK_PEM);

            echo $this->display("manage_config_sitePublicKey", $params);
            return;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error("manage.config.update", $e);
            $response["errCode"] = false;
            $response["errInfo"] = $e->getMessage();
        }

        echo json_encode($response);
        return;
    }


    private function updateSiteConfig($configKey, $configValue)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $result = $this->ctx->SiteConfigTable->updateSiteConfig($configKey, $configValue);
            $this->ctx->Wpf_Logger->info("manage.config.update", "key=" . $configKey . " configValue=" . $configValue);
            return $result;

        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
        return false;
    }

}
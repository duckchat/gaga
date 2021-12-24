<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:58 AM
 */

class Manage_ConfigController extends Manage_CommonController
{
    /**
     * 站点管理
     */
    public function doRequest()
    {
        $config = $this->ctx->Site_Config->getAllConfig();
        $config[SiteConfig::SITE_ID_PRIK_PEM] = "";
        $config['lang'] = $this->language;

        $maxMobileNum = $config[SiteConfig::SITE_MOBILE_NUM];

        if (empty($maxMobileNum)) {
            $config[SiteConfig::SITE_MOBILE_NUM] = 1;
        }

        $maxWebNum = $config[SiteConfig::SITE_WEB_NUM];

        if (empty($maxWebNum)) {
            $config[SiteConfig::SITE_WEB_NUM] = 1;
        }

        $maxFileSize = $config[SiteConfig::SITE_FILE_SIZE];

        if (empty($maxFileSize)) {
            $config[SiteConfig::SITE_FILE_SIZE] = 10;
        }

        echo $this->display("manage_config_index", $config);
        return;
    }

}
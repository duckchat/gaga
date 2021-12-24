<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 06/11/2018
 * Time: 11:46 AM
 */

class Manage_CustomController extends Manage_CommonController
{

    /**
     * 处理正式的请求逻辑，比如跳转界面，post获取信息等
     */
    protected function doRequest()
    {
        $params['lang'] = $this->language;

        $frontPage = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_FRONT_PAGE);
        $hiddenHomePage = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_HIDDEN_HOME_PAGE);

        $params["frontPage"] = isset($frontPage) ? $frontPage : 0;
        $params["hiddenHomePage"] = isset($hiddenHomePage) ? $hiddenHomePage : 0;

        echo $this->display("manage_custom_index", $params);

        return;
    }
}
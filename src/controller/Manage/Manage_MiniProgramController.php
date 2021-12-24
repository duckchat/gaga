<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 3:49 PM
 */

class Manage_MiniProgramController extends Manage_CommonController
{

    public function doRequest()
    {
        $params = ["lang" => $this->language];

        $pluginPublicKey = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_PLUGIN_PLBLIC_KEY);

        $params['miniProgramPublicKey'] = $pluginPublicKey;

        echo $this->display("manage_miniProgram_index", $params);
        return;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/08/2018
 * Time: 5:24 PM
 */

class Page_SiteConfigController extends  HttpBaseController
{
    public function doIndex()
    {
        $this->index();
    }

    public function index()
    {
//        header('Access-Control-Allow-Origin: *');
//        header("Content-Type:application/javascript; charset=utf-8");
//        $configData['enableInvitationCode'] = $this->getSiteConfigFromDB(SiteConfig::SITE_ENABLE_INVITATION_CODE);
//        $configData['enableRealName'] = $this->getSiteConfigFromDB(SiteConfig::SITE_ENABLE_REAL_NAME);
//        $configData = json_encode($configData);
//        $callBack = $_GET['callback'];
//        $configDataJson = json_encode($configData);
//        echo "$callBack($configDataJson)";
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 21/11/2018
 * Time: 11:14 AM
 */

class Upgrade_From10103To10104 extends Upgrade_Version
{

    protected function doUpgrade()
    {
        return $this->updatePlugin();
    }

    protected function upgrade_DB_sqlite()
    {
        $this->dropSiteCustomItemTable();
        $result = $this->executeSqliteScript();
        return $result;
    }

    protected function upgrade_DB_mysql()
    {
        $this->dropSiteCustomItemTable();
        $result = $this->executeMysqlScript();
        return $result;
    }

    private function updatePlugin()
    {
        $data =  [
            'pluginId' => 107,
            'name' => "客服小程序",
            'logo' => "",
            'sort' => 107,
            'landingPageUrl' => "index.php?action=miniProgram.customerService.index",
            'landingPageWithProxy' => 1, //1 表示走site代理
            'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageNone,
            'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
            'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAdminOnly,
            'authKey' => "",
            "management" => "index.php?action=miniProgram.customerService.manage"
        ];
        try{
            $this->ctx->SitePluginTable->insertMiniProgram($data);
        }catch (Exception $ex) {
            $tag = __CLASS__.'->'.__FUNCTION__;
            $this->ctx->getLogger()->error($tag, $ex);
        }
        return true;
    }

    private function dropSiteCustomItemTable()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $this->dropDBTable("siteCustomItem");
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
    }

}
<?php
/**
 * Describe :upgrade 1.0.11(10011) to 1.0.12(10012)
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/11
 * Time: 6:58 PM
 */

class Upgrade_From10011To10012 extends Upgrade_Version
{

    protected function doUpgrade()
    {
        return true;
    }

    protected function upgrade_DB_mysql()
    {
        $this->executeMysqlScript();

        $sql = "alter table sitePlugin ADD COLUMN management TEXT;";

        $prepare = $this->ctx->db->prepare($sql);

        $flag = $prepare->execute();

        $errCode = $prepare->errorCode();

        if (($flag && $errCode == "00000") || "42S21" == $errCode) {
            return true;
        }

        throw new Exception("mysql upgrade 1.0.11 to 1.0.12 error=" . var_export($prepare->errorInfo(), true));
    }

    protected function upgrade_DB_Sqlite()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        $this->dropDBTable('sitePlugin_temp_10011');

        $sql = "alter table sitePlugin rename to sitePlugin_temp_10011";
        $result = $this->ctx->db->exec($sql);
        $this->logger->error($tag, "rename table sitePlugin to sitePlugin_temp_10011 result=" . $result);

        $this->executeSqliteScript();
        $this->logger->error($tag, "upgrade sqlite,execute sqlite script");

        //migrate data to new table
        $insertSql = "insert into sitePlugin(id,pluginId,name,logo,sort,landingPageUrl,landingPageWithProxy,usageType,loadingType,permissionType,authKey,addTime) 
          select id,pluginId,name,logo,sort,landingPageUrl,landingPageWithProxy,usageType,loadingType,permissionType,authKey,addTime from sitePlugin_temp_10011";

        $prepare = $this->ctx->db->prepare($insertSql);

        $flag = $prepare->execute();
        $errCode = $prepare->errorCode();

        if ($flag && $errCode == "00000") {
            $this->dropDBTable('sitePlugin_temp_10011');
            return true;
        }

        throw new Exception("sqlite upgrade 1.0.11 to 1.0.12 error=" . var_export($prepare->errorInfo(), true));
    }
}
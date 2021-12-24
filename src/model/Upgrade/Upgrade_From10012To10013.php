<?php
/**
 * Describe :upgrade 1.0.12(10012) to 1.0.13(10013)
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/11
 * Time: 6:58 PM
 */

class Upgrade_From10012To10013 extends Upgrade_Version
{

    protected function doUpgrade()
    {
        $this->upgradeSitePluginFor10013();

        return $this->addEnableAddFriendInGroupConfig();
    }

    protected function upgrade_DB_mysql()
    {
        $this->executeMysqlScript();

        //add siteGroup canAddFriend column
        $sql = "alter table siteGroup add column canAddFriend BOOLEAN default true";
        $prepare = $this->ctx->db->prepare($sql);
        $flag = $prepare->execute();

        $dbErrCode = $prepare->errorCode();
        $flag = (($flag && "00000" == $dbErrCode) || "42S21" == $dbErrCode);

        $flag = $flag && $this->upgradePassportPasswordTable("mysql");

        if ($flag) {
            return true;
        }

        throw new Exception("mysql upgrade 1.0.12 to 1.0.13 error=" . var_export($prepare->errorInfo(), true));
    }

    protected function upgrade_DB_Sqlite()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        //upgrade siteGroup table
        $flag = $this->addColumnCanAddFriendToSiteGroupForSqlite();
        //upgrade passportPassword table
        $flag = $flag && $this->upgradePassportPasswordTable("sqlite");

        if ($flag) {
            return true;
        }

        return false;
    }

    private function addColumnCanAddFriendToSiteGroupForSqlite()
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;

        $this->dropDBTable("siteGroup_temp_10012");

        //rename table
        $sql = "alter table siteGroup rename to siteGroup_temp_10012";
        $result = $this->ctx->db->exec($sql);
        $this->logger->error($tag, "rename table siteGroup to siteGroup_temp_10012 result=" . $result);

        //execute all table
        $this->executeSqliteScript();

        //migrate data to new table
        $sql = "insert into 
                  siteGroup(id,groupId,name,nameInLatin,owner,avatar,description,descriptionType,permissionJoin,canGuestReadMessage,canAddFriend,speakers,maxMembers,status,isWidget,timeCreate) 
                select 
                  id,groupId,name,nameInLatin,owner,avatar,description,descriptionType,permissionJoin,canGuestReadMessage,1 as canAddFriend,speakers,maxMembers,status,isWidget,timeCreate
                from siteGroup_temp_10012";
        $prepare = $this->ctx->db->prepare($sql);
        $flag = $prepare->execute();

        if ($flag && $prepare->errorCode() == "00000") {
            $this->dropDBTable("siteGroup_temp_10012");
            return true;
        }

        throw new Exception("sqlite upgrade 1.0.12 to 1.0.13 error=" . var_export($prepare->errorInfo(), true));
    }

    private function upgradePassportPasswordTable($dbType)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        $this->dropDBTable('passportPassword_temp_10012');

        //rename table
        $sql = "alter table passportPassword rename to passportPassword_temp_10012";
        $result = $this->ctx->db->exec($sql);
        $this->logger->error($tag, "rename table passportPassword to passportPassword_temp_10012 result=" . $result);

        if ("mysql" == $dbType) {
            $this->executeMysqlScript();
        } else {
            //execute all table
            $this->executeSqliteScript();
        }

        //migrate data to new table
        $sql = "insert into
                  passportPassword(id ,userId ,loginName ,nickname ,password ,email ,invitationCode ,timeReg) 
                select 
                  id ,userId ,loginName ,nickname ,password,email ,invitationCode ,timeReg
                from passportPassword_temp_10012";
        $prepare = $this->ctx->db->prepare($sql);
        $flag = $prepare->execute();

        if ($flag && $prepare->errorCode() == "00000") {
            $this->dropDBTable('passportPassword_temp_10012');
            return true;
        }

        throw new Exception("sqlite upgrade 1.0.12 to 1.0.13 error=" . var_export($prepare->errorInfo(), true));
    }

    private function upgradeSitePluginFor10013()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;


        $data = [
            'pluginId' => 105,
            'name' => "账户密码管理",
            'logo' => "",
            'sort' => 105,
            'landingPageUrl' => "index.php?action=miniProgram.passport.account",
            'landingPageWithProxy' => 1,
            'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageAccountSafe,
            'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
            'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
            'authKey' => "",
            'management' => "",
        ];

        try {
            $where = [
                "pluginId" => 105,
            ];
            $this->ctx->SitePluginTable->updateProfile($data, $where);
        } catch (Exception $e) {
            $this->logger->error($tag, "ignore insert 105:" . $e->getMessage());
        }


        try {
            $data["pluginId"] = 105;
            $this->ctx->SitePluginTable->insertMiniProgram($data);
        } catch (Exception $e) {
            $this->logger->error($tag, "ignore update 105:" . $e->getMessage());
        }

        //update miniProgram management
        try {
            $data2 = [
                'management' => "index.php?action=miniProgram.admin.passwordLogin",
            ];
            $where2 = [
                "pluginId" => 102,
            ];
            $this->ctx->SitePluginTable->updateProfile($data2, $where2);
        } catch (Exception $e) {
            $this->logger->error($tag, "update 102 :" . $e->getMessage());
        }
    }

    private function addEnableAddFriendInGroupConfig()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        $sql = "insert into siteConfig(configKey,configValue) values('enableAddFriendInGroup',1)";
        $prepare = $this->ctx->db->prepare($sql);

        $flag = $prepare->execute();

        if (($flag && $prepare->errorCode() == "00000") || $prepare->errorCode() == "23000") {
            return true;
        }

        throw new Exception(var_export($prepare->errorInfo(), true));
    }
}
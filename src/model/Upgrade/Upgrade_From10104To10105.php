<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 26/11/2018
 * Time: 3:46 PM
 */

class Upgrade_From10104To10105 extends Upgrade_Version
{

    protected function doUpgrade()
    {
        return $this->updatePlugin();
    }

    protected function upgrade_DB_sqlite()
    {
        $result = $this->executeSqliteScript();
        return $result;
    }

    protected function upgrade_DB_mysql()
    {
        $result = $this->executeMysqlScript();
        return $result;
    }

    private function updatePlugin()
    {

        return true;
    }

    private function dropSiteCustomItemTable()
    {

    }

}
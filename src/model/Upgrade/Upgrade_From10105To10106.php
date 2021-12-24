<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 27/11/2018
 * Time: 5:05 PM
 */

class Upgrade_From10105To10106 extends Upgrade_Version
{

    protected function doUpgrade()
    {
        return $this->updatePlugin();
    }

    protected function upgrade_DB_sqlite()
    {
        return true;
    }

    protected function upgrade_DB_mysql()
    {

        return true;
    }

    private function updatePlugin()
    {

        return true;
    }

    private function dropSiteCustomItemTable()
    {

    }

}
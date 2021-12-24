<?php
/**
 * Describe :upgrade 1.1.0(10100) to 1.1.1(10101)
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/11
 * Time: 6:58 PM
 */

class Upgrade_From10100To10101 extends Upgrade_Version
{

    protected function doUpgrade()
    {
        return true;
    }

    protected function upgrade_DB_mysql()
    {
        return $this->executeMysqlScript();
    }

    protected function upgrade_DB_Sqlite()
    {
        return $this->executeSqliteScript();
    }
}
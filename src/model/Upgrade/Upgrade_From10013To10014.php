<?php
/**
 * Describe :upgrade 1.0.13(10013) to 1.0.14(10014)
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/11
 * Time: 6:58 PM
 */

class Upgrade_From10013To10014 extends Upgrade_Version
{

    protected function doUpgrade()
    {
        $phpErrorLog = "php_errors_" . ZalyHelper::generateStrKey(16) . '.log';
        $config = [
            "errorLog" => $phpErrorLog,
        ];
        return $this->updateConfigPhp($config);
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
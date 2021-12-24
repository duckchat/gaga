<?php
/**
 * Created by PhpStorm.
 * User: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/10
 * Time: 2:54 PM
 */

class Upgrade_Client
{

    public static function doUpgrade($oldVersionCode, $newVersionCode)
    {
        $oldVersionCode = trim($oldVersionCode);
        $newVersionCode = trim($newVersionCode);
        $upgradeClassName = "Upgrade_From" . $oldVersionCode . "To" . $newVersionCode;
        error_log("site upgrade from 【" . $oldVersionCode . "】 to 【" . $newVersionCode) . "】";
        $phpPath = WPF_ROOT_DIR . "/model/Upgrade/" . $upgradeClassName . ".php";
        include($phpPath);
        $upgrade = new $upgradeClassName(new BaseCtx());
        return $upgrade->upgrade();
    }

}
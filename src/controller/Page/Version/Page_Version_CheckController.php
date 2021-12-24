<?php
/**
 * check current version need to upgrade
 * User: anguoyue
 * Date: 13/10/2018
 * Time: 3:54 PM
 */

class Page_Version_CheckController extends Page_VersionController
{

    public function doRequest()
    {

        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'GET') {

            $params["passwordFileName"] = "src/upgrade.php文件中的password字段";
            //tell client if need upgrade
            $params["needUpgrade"] = $this->needUpgrade;

            //显示界面
            echo $this->display("upgrade_upgrade", $params);

        } elseif ($method == 'POST') {
            //检测当前版本是否已经升级完
            $upgradeInfo = $this->getUpgradeVersion();

            unset($upgradeInfo["password"]);

            $this->logger->error("page.version.check", var_export($upgradeInfo, true));

            echo json_encode($upgradeInfo);
        }

        return;
    }

}
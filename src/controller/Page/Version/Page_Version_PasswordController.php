<?php
/**
 * check current version need to upgrade
 * User: anguoyue
 * Date: 13/10/2018
 * Time: 3:54 PM
 */

class Page_Version_PasswordController extends Page_VersionController
{

    function doRequest()
    {
        $password = $_POST["password"];

        //get old VersionCode
        $oldVersionCode = ZalyConfig::getConfig(ZalyConfig::$configSiteVersionCodeKey);

        if (!is_numeric($oldVersionCode)) {
            $oldVersionCode = 10011;
        }

        $serverPassword = $this->getUpgradePassword();
        $serverPassword = trim($serverPassword);
        $this->logger->error("page.version.password", "clientPwd=" . $password . " serverPwd=" . $serverPassword);


        if ($password == $serverPassword) {
            setcookie("upgradePassword", sha1($serverPassword), time() + 1800); //半个小时

            $newVersionCode = ZalyConfig::getSampleConfig(ZalyConfig::$configSiteVersionCodeKey);
            $this->logger->error("page.version.password", "oldVersion=" . $oldVersionCode . " to newVersion=" . $newVersionCode);

            $versions = [];

            foreach ($this->versions as $code => $name) {
                if ($oldVersionCode <= $code) {
                    $versions[$code] = $name;
                }
            }

            $result = [
                "errCode" => "success",
                "versions" => $versions,
            ];

            echo json_encode($result);
        } else {
            $error = [
                "errCode" => "error",
                "errInfo" => "升级口令错误"
            ];
            echo json_encode($error);
        }

        return;
    }

}
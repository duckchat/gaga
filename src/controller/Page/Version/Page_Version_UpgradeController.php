<?php
/**
 * check current version need to upgrade
 * User: anguoyue
 * Date: 13/10/2018
 * Time: 3:54 PM
 */

class Page_Version_UpgradeController extends Page_VersionController
{
    private $versionCode;
    private $versionName;

    private $upgradeErrCode = "error";
    private $upgradeErrInfo;

    function doRequest()
    {
        try {
            //校验upgradePassword
            if (!$this->checkUpgradePassword()) {
                throw new Exception("error upgrade password");
            }
            $currentCode = $_POST["versionCode"];
            if (empty($currentCode)) {
                $currentCode = 10011;
            }

            $result = false;

            if ($currentCode <= 10011) {
                $this->versionCode = 10012;
                $this->versionName = "1.0.12";
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10012) {
                $this->versionCode = 10013;
                $this->versionName = "1.0.13";
                $this->checkoutPreviousUpgrade($currentCode, "1.0.12");
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10013) {
                $this->versionCode = 10014;
                $this->versionName = "1.0.14";
                $this->checkoutPreviousUpgrade($currentCode, "1.0.13");
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode >= 10014 && $currentCode < 10100) {
                $this->versionCode = 10100;
                $this->versionName = "1.1.0";
                $this->checkoutPreviousUpgrade($currentCode, "1.0.14");
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10100) {
                $this->versionCode = 10101;
                $this->versionName = "1.1.1";
                $this->checkoutPreviousUpgrade($currentCode, "1.1.1");
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10101) {
                $this->versionCode = 10102;
                $this->versionName = "1.1.2";
                $this->checkoutPreviousUpgrade($currentCode, "1.1.2");
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10102) {
                $this->versionCode = 10103;
                $this->versionName = "1.1.3";
                $this->checkoutPreviousUpgrade($currentCode, "1.1.3");
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10103) {
                $this->versionCode = 10104;
                $this->versionName = "1.1.4";
                $this->checkoutPreviousUpgrade($currentCode, "1.1.4");
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10104) {
                $this->versionCode = 10105;
                $this->versionName = "1.1.5";
                $this->checkoutPreviousUpgrade($currentCode, "1.1.5");
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10105) {
                $this->versionCode = 10106;
                $this->versionName = "1.1.6";
                $this->checkoutPreviousUpgrade($currentCode, "1.1.6");
                $result = true;
            } elseif ($currentCode == 10106) {
                $this->versionCode = 10107;
                $this->versionName = "1.1.7";
                $this->checkoutPreviousUpgrade($currentCode, $this->versionName);
                $result = true;
                // change upgrade password
                $this->updatePassword();
            } else {
                throw new Exception("unsupport site version code = " . $currentCode);
            }

            if ($result) {
                $this->upgradeErrCode = "success";
            }

            //update cache if exists
            if (function_exists("opcache_reset")) {
                opcache_reset();
            }

            $this->setUpgradeVersion($this->versionCode, $this->versionName, $this->upgradeErrCode, $this->upgradeErrInfo);

            if ($result) {
                $this->updateSiteConfigAsUpgrade($this->versionCode, $this->versionName);
            }

        } catch (Exception $e) {
            $this->logger->error("page.version.upgrade", $e);
            $this->setUpgradeVersion($this->versionCode, $this->versionName, "error", $e->getMessage() . " " . $e->getTraceAsString());
        }

        return;
    }


    private function checkUpgradePassword()
    {
        $upgradePassword = $_COOKIE['upgradePassword'];

        $serverPassword = $this->getUpgradePassword();
        $serverPassword = trim($serverPassword);

        if ($upgradePassword != sha1($serverPassword)) {
            throw new Exception("upgrade gaga-server by error password");
        }

        return true;
    }

    private function checkoutPreviousUpgrade($currentCode, $currentVersionName)
    {
        $upgradeResult = $this->getUpgradeVersion();
        $versionCode = $upgradeResult["versionCode"];
        $upgradeCode = $upgradeResult["upgradeErrCode"];
        if ($currentCode <= $versionCode && "success" == $upgradeCode) {
            $this->initUpgradeInfo($currentCode, $currentVersionName);
            return true;
        }

        $siteVersionCode = ZalyConfig::getConfig(ZalyConfig::$configSiteVersionCodeKey);

        if (!is_numeric($siteVersionCode)) {
            $siteVersionCode = 10011;
        }

        $siteVersionCode = max($siteVersionCode, 10011);

        if ($currentCode <= $siteVersionCode) {
            $this->initUpgradeInfo($currentCode, $currentVersionName);
            return true;
        }

        throw new Exception($currentCode . " upgrade error ,as last upgrade failed");
    }

    private function initUpgradeInfo($versionCode, $versionName)
    {
        $initInfo = [
//            "versionCode" => $versionCode,
//            "versionName" => $versionName,
            "upgradeErrCode" => "",
            "upgradeErrInfo" => "",
        ];
        $this->updateUpgradeInfo($initInfo);
    }
}
<?php
/**
 * check current version need to upgrade
 * User: SAM<an.guoyue254@gmail.com>
 * Date: 13/10/2018
 * Time: 3:54 PM
 */

abstract class Page_VersionController extends UpgradeController
{
    protected $needUpgrade = false;
    protected $upgradeFilePath = WPF_ROOT_DIR . "/upgrade.php";
    protected $versions = [
        10011 => "1.0.11",
        10012 => "1.0.12",
        10013 => "1.0.13",
        10014 => "1.0.14",
        10100 => "1.1.0",
        10101 => "1.1.1",
        10102 => "1.1.2",
        10103 => "1.1.3",
        10104 => "1.1.4",
        10105 => "1.1.5",
        10106 => "1.1.6",
        10107 => '1.1.7',
    ];

    abstract function doRequest();

    public function index()
    {

        //set is latest version
        $currentVersionCode = ZalyConfig::getConfig(ZalyConfig::$configSiteVersionCodeKey);
        if (!is_numeric($currentVersionCode)) {
            $currentVersionCode = 10011;
        }
        $latestVersionCode = ZalyConfig::getSampleConfig(ZalyConfig::$configSiteVersionCodeKey);
        //check if need upgrade
        if ($currentVersionCode < $latestVersionCode) {
            $this->needUpgrade = true;
        } else {

            $method = $_SERVER['REQUEST_METHOD'];
            if ($method == 'GET') {
                $upgradeUrl = './index.php';
                header("Location:" . $upgradeUrl);
                exit;
            }
        }

        $this->initUpgradeVersion();
        $this->doRequest();
    }

    protected function initUpgradeVersion()
    {
        $siteVersion = [
            "versionCode" => 10011, //默认第一个版本，从1.0.11版本开始支持升级
            "versionName" => "",
            "password" => ZalyHelper::generateNumberKey(),//升级密钥
            "upgradeErrCode" => "",
            "upgradeErrInfo" => "",
        ];

        if (!file_exists($this->upgradeFilePath)) {
            $contents = var_export($siteVersion, true);
            file_put_contents($this->upgradeFilePath, "<?php\n return {$contents};\n ");
            $this->resetOpcache();
        } else {

            $password = $this->getUpgradePassword();

            if (empty($password)) {
                //update new password
                $this->updatePassword();
            }

        }

    }

    //update  src/upgrade.php file
    private function updateUpgradeFile(array $upgradeInfo)
    {
        if (empty($upgradeInfo)) {
            return false;
        }
        $fileName = dirname(__FILE__) . "/../../upgrade.php";
        $contents = var_export($upgradeInfo, true);
        file_put_contents($fileName, "<?php\n return {$contents};\n ");

        $this->resetOpcache();
    }

    protected function updateUpgradeInfo($newInfo)
    {
        $oldInfo = $this->getUpgradeVersion();
        $newInfo = array_merge($oldInfo, $newInfo);
        $this->updateUpgradeFile($newInfo);
    }

    protected function updatePassword()
    {
        $upgradeInfo = $this->getUpgradeVersion();
        $upgradeInfo['password'] = ZalyHelper::generateNumberKey();

        $this->updateUpgradeFile($upgradeInfo);
    }

    protected function getUpgradeVersion()
    {
        $fileName = dirname(__FILE__) . "/../../upgrade.php";
        if (!file_exists($fileName)) {
            $this->initUpgradeVersion();
        }
        $versionArrays = require($fileName);

        return $versionArrays;
    }

    protected function getUpgradePassword()
    {
        $versionInfos = $this->getUpgradeVersion();
        $password = $versionInfos['password'];
        return $password;
    }

    /**
     * @param $versionCode
     * @param $versionName
     * @param $upgradeErrCode success/doing/error
     * @param $upgradeErrInfo
     */
    protected function setUpgradeVersion($versionCode, $versionName, $upgradeErrCode, $upgradeErrInfo)
    {
        $siteVersion = [
            "versionCode" => $versionCode,
            "versionName" => $versionName,
            "password" => $this->getUpgradePassword(),//升级口令文件名称
            "upgradeErrCode" => $upgradeErrCode,
            "upgradeErrInfo" => $upgradeErrInfo,
        ];

        $fileName = dirname(__FILE__) . "/../../upgrade.php";
        $contents = var_export($siteVersion, true);
        file_put_contents($fileName, "<?php\n return {$contents};\n ");

        $this->resetOpcache();
    }

    protected function setUpgradeErrInfo($upgradeErrCode, $upgradeErrInfo)
    {
        $currentVersion = $this->getUpgradeVersion();

        $siteVersion = [
            "versionCode" => $currentVersion["versionCode"],
            "versionName" => $currentVersion['versionName'],
            "password" => $this->getUpgradePassword(),//升级口令文件名称
            "upgradeErrCode" => $upgradeErrCode,
            "upgradeErrInfo" => $upgradeErrInfo,
        ];

        $fileName = dirname(__FILE__) . "/../../upgrade.php";
        $contents = var_export($siteVersion, true);
        file_put_contents($fileName, "<?php\n return {$contents};\n ");

        $this->resetOpcache();
    }

    protected function executeMysqlScript()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $mysqlScriptPath = dirname(__DIR__) . "/../model/database-sql/site_mysql.sql";

        $_sqlContent = file_get_contents($mysqlScriptPath);//写自己的.sql文件
        $_sqlArr = explode(';', $_sqlContent);
        $_sqlArr = array_filter($_sqlArr);

        try {
            $this->ctx->db->beginTransaction();
            foreach ($_sqlArr as $sql) {
                $this->ctx->db->exec($sql);
            }
            $this->ctx->db->commit();
        } catch (Throwable $e) {
            $this->ctx->db->rollBack();
            $this->logger->error($tag, $e);
            throw $e;
        }

    }

    protected function executeSqliteScript()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $mysqlScriptPath = dirname(__DIR__) . "/../model/database-sql/site_sqlite.sql";
        $_sqlContent = file_get_contents($mysqlScriptPath);//写自己的.sql文件
        $_sqlArr = explode(';', $_sqlContent);
        $_sqlArr = array_filter($_sqlArr);

        try {
            $this->ctx->db->beginTransaction();
            foreach ($_sqlArr as $sql) {
                $this->ctx->db->exec($sql);
            }
            $this->ctx->db->commit();
        } catch (Exception $e) {
            $this->ctx->db->rollBack();
            $this->ctx->logger->error($tag, $e);
            throw $e;
        }

    }

    //升级config.php,只升级 siteVersionCode & siteVersionName
    protected function updateSiteConfigAsUpgrade($newVersionCode, $newVersionName)
    {
        $siteConfig = ZalyConfig::getAllConfig();
        $siteConfig["siteVersionCode"] = $newVersionCode;
        $siteConfig["siteVersionName"] = $newVersionName;
        ZalyConfig::updateConfigFile($siteConfig);
    }

    //升级config.php，升级$config中所有数据
    protected function updateSiteConfig($config)
    {
        if (!is_array($config)) {
            return false;
        }
        $siteConfig = ZalyConfig::getAllConfig();
        $siteConfig = array_merge($siteConfig, $config);
        ZalyConfig::updateConfigFile($siteConfig);
    }

    protected function updateSiteConfigKey($keys)
    {
        if (!is_array($keys)) {
            return false;
        }
        $this->resetOpcache();
        $siteConfig = ZalyConfig::getAllConfig();
        foreach ($keys as $oKey => $nKey) {
            foreach ($siteConfig as $oldKey => $val) {
                if ($oldKey == $oKey || strpos($oldKey, $oKey) !== false) {
                    $repKey = str_replace($oKey, $nKey, $oldKey);
                    $siteConfig[$repKey] = $val;
                    unset($siteConfig[$oldKey]);
                }
            }
        }
        ZalyConfig::updateConfigFile($siteConfig);
    }

    protected function dropDBTable($tableName)
    {
        $sql = "drop table $tableName";
        $this->ctx->db->exec($sql);
    }

    private function resetOpcache()
    {
        if (function_exists("opcache_reset")) {
            opcache_reset();
        }
    }

}
<?php
/**
 * upgrade abstract class
 * User: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/10
 * Time: 2:54 PM
 */

abstract class Upgrade_Version
{
    protected $logger;
    protected $ctx;

    public function __construct(BaseCtx $context)
    {
        $this->logger = $context->getLogger();
        $this->ctx = $context;
    }

    protected abstract function doUpgrade();

    protected abstract function upgrade_DB_mysql();

    protected abstract function upgrade_DB_Sqlite();

    public function upgrade()
    {
        $result = false;
        //upgrade config ,cache ,config file,config key
        $result = $this->doUpgrade();

        //upgrade database
        if ($result) {
            $result = $this->doDBUpgrade();
        }
        return $result;
    }

    private function doDBUpgrade()
    {
        $res = false;
        $dbType = $this->ctx->dbType;
        if ($dbType == "mysql") {
            $res = $this->upgrade_DB_mysql();
        } else {
            $res = $this->upgrade_DB_Sqlite();
        }
        return $res;
    }

    protected function executeMysqlScript()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $mysqlScriptPath = dirname(__DIR__) . "/database-sql/site_mysql.sql";

        $_sqlContent = file_get_contents($mysqlScriptPath);//写自己的.sql文件
        $_sqlArr = explode(';', $_sqlContent);
        $_sqlArr = array_filter($_sqlArr);

        try {
            $this->ctx->db->beginTransaction();
            foreach ($_sqlArr as $sql) {
                $this->ctx->db->exec($sql);
            }
            $this->ctx->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->ctx->db->rollBack();
            $this->logger->error($tag, $e);
            throw $e;
        }
        return false;
    }

    protected function executeSqliteScript()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $mysqlScriptPath = dirname(__DIR__) . "/database-sql/site_sqlite.sql";
        $_sqlContent = file_get_contents($mysqlScriptPath);//写自己的.sql文件
        $_sqlArr = explode(';', $_sqlContent);
        $_sqlArr = array_filter($_sqlArr);

        try {
            $this->ctx->db->beginTransaction();
            foreach ($_sqlArr as $sql) {
                $this->ctx->db->exec($sql);
            }
            $this->ctx->db->commit();

            return true;
        } catch (Exception $e) {
            $this->ctx->db->rollBack();
            $this->ctx->logger->error($tag, $e);
            throw $e;
        }

        return false;
    }

    //升级config.php，升级$config中所有数据
    protected function updateConfigPhp($newConfig)
    {
        if (!is_array($newConfig)) {
            return false;
        }
        $oldConfig = ZalyConfig::getAllConfig();
        $newConfig = array_merge($oldConfig, $newConfig);
        return ZalyConfig::updateConfigFile($newConfig);
    }

    /**
     * update config.php key to newKey
     * eg:
     *  "test_curl" => "testUrl"
     *  "session_verify_" => "sessionVerify",
     * @param $keys
     * @return bool
     */
    protected function updateConfigPhpKey($keys)
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
        return ZalyConfig::updateConfigFile($siteConfig);
    }

    protected function dropDBTable($tableName)
    {
        $sql = "drop table $tableName";
        return $this->ctx->db->exec($sql);
    }

    protected function resetOpcache()
    {
        if (function_exists("opcache_reset")) {
            opcache_reset();
        }
    }

    /**
     * @param $tag
     * @param Exception $e
     * @throws Exception
     */
    protected function printAndThrowException($tag, $e)
    {
        $this->logger->error($tag, $e->getMessage() . "\n" . $e->getTraceAsString());
        throw $e;
    }

}
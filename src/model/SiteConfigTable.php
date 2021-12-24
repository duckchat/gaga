<?php

/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 16/07/2018
 * Time: 5:14 PM
 */
class SiteConfigTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $table = "siteConfig";
    private $columns = [
        "id",
        "configKey",
        "configValue"
    ];

    public function init()
    {
        $this->logger = $this->ctx->getLogger();
        $this->columns = implode(",", $this->columns);
    }


    public function insertSiteConfig($configKey, $configValue)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $sqlStr = "('" . $configKey . "','" . $configValue . "')";
        $sql = "insert into 
                        siteConfig(configKey, configValue) 
                    values 
                        $sqlStr;";
        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $result = $prepare->execute();

            return $result;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$configKey, $configValue], $this->getCurrentTimeMills());
        }
    }


    public function updateSiteConfig($configKey, $configValue)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {

            $sql = "update $this->table set configValue=:configValue where configKey=:configKey;";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":configValue", $configValue);
            $prepare->bindValue(":configKey", $configKey);
            $result = $prepare->execute();
            $count = $prepare->rowCount();
            if ($result && $count > 0) {
                return true;
            }
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$configKey, $configValue], $this->getCurrentTimeMills());
        }

        return false;
    }

    /**
     * @param bool $configKey
     * @return array
     */
    public function selectSiteConfig($configKey = false)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        try {
            $startTime = microtime(true);
            if ($configKey === false) {
                $sql = "select $this->columns from $this->table;";
            } elseif (is_string($configKey)) {
                $sql = "select $this->columns from $this->table where configKey=:configKey;";
            } elseif (is_array($configKey)) {
                $configKeyStr = implode("','", $configKey);
                $sql = "select $this->columns from $this->table where configKey in ('$configKeyStr');";
            }
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            if ($configKey !== false && is_string($configKey)) {
                $prepare->bindValue("configKey", $configKey);
            }

            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $output = [];
            if ($results) {
                foreach ($results as $result) {
                    $output[$result['configKey']] = $result['configValue'];
                }
            }
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, "", $startTime);
            return $output;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg ==" . $ex->getMessage());
            return [];
        }
    }

    public function deleteSiteConfig($configKey)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $sql = "delete from $this->table where configKey=:configKey;";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":configKey", $configKey);
        $result = $prepare->execute();

        return $this->handlerResult($result, $prepare, $tag);
    }
}
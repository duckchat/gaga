<?php
/**
 *
 * 登陆自定义
 *
 * User: anguoyue
 * Date: 18/10/2018
 * Time: 12:02 PM
 */

class SiteLoginCustomTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;

    private $table = "siteLoginCustom";

    private $columns = [
        "id",
        "configKey",
        "configValue",    //默认语言
        "configValueEN",
        "updateUserId",
        "updateTime",
    ];

    private $queryColumns;

    public function init()
    {
        $this->logger = $this->ctx->getLogger();
        $this->queryColumns = implode(",", $this->columns);
    }


    public function insertConfig($configKey, $configValue, $configValueEN = "")
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        $currentTime = ZalyHelper::getMsectime();
        $sql = "insert into 
                        $this->table(configKey, configValue,configValueEN,updateTime) 
                    values 
                        (:configKey ,:configValue , :configValueEN ,:updateTime);";
        try {

            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":configKey", $configKey);
            $prepare->bindValue(":configValue", $configValue);
            $prepare->bindValue(":configValueEN", $configValueEN);
            $prepare->bindValue(":updateTime", $currentTime);

            $result = $prepare->execute();

            return $this->handlerResult($result, $prepare, $tag);
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$configKey, $configValue, $configValueEN], $this->getCurrentTimeMills());
        }
    }


    public function updateConfig($configKey, $configValue, $configValueEN = "", $updateUserId = "")
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $currentTime = $this->getCurrentTimeMills();
        try {

            $sql = "update $this->table 
                    set 
                      configValue=:configValue,
                      configValueEN=:configValueEN,
                      updateUserId=:updateUserId,
                      updateTime=:updateTime
                    where configKey=:configKey;";

            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":configKey", $configKey);
            $prepare->bindValue(":configValue", $configValue);
            $prepare->bindValue(":configValueEN", $configValueEN);
            $prepare->bindValue(":updateUserId", $updateUserId);
            $prepare->bindValue(":updateTime", $currentTime);

            $result = $prepare->execute();

            return $this->handlerUpdateResult($result, $prepare, $tag);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$configKey, $configValue], $currentTime);
        }

        return false;
    }

    public function getAllCustomConfig()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $currentTime = $this->getCurrentTimeMills();
        $sql = "select $this->queryColumns from $this->table";

        try {
            $prepare = $this->ctx->db->prepare($sql);
            $prepare->execute();
            $results = $prepare->fetchAll(PDO::FETCH_ASSOC);

//            $this->logger->error("========", var_export($results, true));
            $output = [];
            if ($results) {
                foreach ($results as $value) {
                    $output[$value["configKey"]] = $value;
                }
            }

            return $output;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [], $currentTime);
        }

    }

    public function getCustomConfig($configKey)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $currentTime = $this->getCurrentTimeMills();
        $sql = "select $this->queryColumns from $this->table where configKey=:configKey;";

        try {
            $prepare = $this->ctx->db->prepare($sql);

            $prepare->bindValue(":configKey", $configKey);

            $prepare->execute();
            return $prepare->fetch(PDO::FETCH_ASSOC);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [], $currentTime);
        }
    }

}
<?php
/**
 * Custom Items for Site
 * User: anguoyue
 * Date: 2018/11/7
 * Time: 7:45 PM
 */

class SiteCustomTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $table = "siteCustom";
    /**
     * keyType:
     *  1:login
     *
     */
    private $columns = [
        "id",
        "customKey",
        "keyName",
        "keyIcon",
        "keyDesc",
        "keyType",
        "keySort",
        "keyConstraint",
        "isRequired",
        "isOpen",
        "status",
        "dataType",
        "dataVerify",
        "addTime",
    ];

    private $queryColumns;

    public function init()
    {
        $this->logger = $this->ctx->getLogger();
        $this->queryColumns = implode(",", $this->columns);
    }


    public function insertUserCustomInfo(array $keyData)
    {
        $keyData['keyType'] = Zaly\Proto\Core\CustomType::CustomTypeUser;
        $keyData['addTime'] = $this->getCurrentTimeMills();
        return $this->insertData($this->table, $keyData, $this->columns);
    }

    public function insertCustomInfo(array $customData)
    {
        return $this->insertData($this->table, $customData, $this->columns);
    }

    public function deleteCustomInfo($keyType, $customKey)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $sql = "delete from $this->table where keyType=:keyTYpe and customKey=:customKey;";

        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":keyTYpe", $keyType);
        $prepare->bindValue(":customKey", $customKey);

        $result = $prepare->execute();

        return $this->handlerResult($result, $prepare, $tag);
    }

    public function updateCustomInfo($data, $where)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    public function queryCustomKeys($keyType, $status = false, $isOpen = false, $tag = false)
    {
        $startTime = $this->getCurrentTimeMills();

        if (!$tag) {
            $tag = __CLASS__ . '->' . __FUNCTION__;
        }

        $sql = "select customKey from $this->table where keyType=:keyType ";

        if ($status !== false) {
            $sql .= " and status=:status ";
        }

        if ($isOpen !== false) {
            $sql .= " and isOpen=:isOpen ";
        }

        $sql .= " order by keySort ASC;";

        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":keyType", $keyType);
            if ($status !== false) {
                $prepare->bindValue(":status", $status, PDO::PARAM_INT);
            }

            if ($isOpen !== false) {
                $prepare->bindValue(":isOpen", $isOpen, PDO::PARAM_INT);
            }

            $prepare->execute();
            $result = $prepare->fetchAll(PDO::FETCH_COLUMN);

            return $result;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$status], $startTime);
        }

    }

    public function queryCustomByKey($keyType, $customKey)
    {
        $startTime = $this->getCurrentTimeMills();

        $tag = __CLASS__ . '->' . __FUNCTION__;

        $sql = "select $this->queryColumns from $this->table where keyType=:keyType and customKey=:customKey;";

        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":keyType", $keyType);
            $prepare->bindValue(":customKey", $customKey);

            $prepare->execute();
            $result = $prepare->fetch(PDO::FETCH_ASSOC);
            return $result;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$customKey, $keyType], $startTime);
        }

    }

    public function queryCustomInfo($keyType, $status, $tag = false)
    {
        $startTime = $this->getCurrentTimeMills();

        if (!$tag) {
            $tag = __CLASS__ . '->' . __FUNCTION__;
        }

        $sql = "select $this->queryColumns from $this->table where keyType=:keyType ";

        if ($status && $status >= 0) {
            $sql .= " and status=:status ";
        }

        $sql .= " order by keySort ASC;";

        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":keyType", $keyType);
            if ($status && $status >= 0) {
                $prepare->bindValue(":status", $status, PDO::PARAM_INT);
            }

            $prepare->execute();
            $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$status], $startTime);
        }

    }
}
<?php
/**
 * user custom items
 * User: anguoyue
 * Date: 2018/11/7
 * Time: 7:36 PM
 */

class SiteUserCustomTable extends BaseTable
{

    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $table = "siteUserCustom";
    private $customKeyType = Zaly\Proto\Core\CustomType::CustomTypeUser;

    private $defaultColumns = [
        "id",
        "userId",
        "phoneId",
        "email",
        "addTime",
    ];

    public function init()
    {
        $this->logger = $this->ctx->getLogger();
    }

    /**
     * @param $customArray
     * [
     *  "phone" => "18811782523",
     *  "name" => "SAM",
     *  "age" => 20,
     * ]
     * @return mixed
     * @throws Exception
     */
    public function insertCustomProfile($customArray)
    {
        $columns = $this->getAllColumns();
        $columns = array_merge($columns, $this->defaultColumns);
        $customArray['addTime'] = ZalyHelper::getMsectime();
        return $this->insertData($this->table, $customArray, $columns);
    }

    public function updateCustomProfile($data, $where)
    {
        $columns = $this->getAllColumns();
        $columns = array_merge($columns, $this->defaultColumns);
        return $this->updateInfo($this->table, $where, $data, $columns);
    }

    //get user custom profile which show to others
    public function queryOpenCustomProfile($userId)
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;
        $columns = $this->getOpenColumns();
        if(!$columns) {
            return false;
        }
        return $this->queryCustom($columns, $userId, $tag);
    }

    //get user all custom profile
    public function queryAllCustomProfile($userId)
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;
        $columns = $this->getAllColumns();
        if(!$columns) {
            return false;
        }
        return $this->queryCustom($columns, $userId, $tag);
    }

    private function queryCustom(array $queryColumns, $userId, $tag = false)
    {
        $startTime = $this->getCurrentTimeMills();

        if (!$tag) {
            $tag = __CLASS__ . "->" . __FUNCTION__;
        }

        try {
            $queryColumns = implode(",", $queryColumns);
            $sql = "select $queryColumns from $this->table where userId=:userId;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue("userId", $userId);
            $prepare->execute();

            $result = $prepare->fetch(PDO::FETCH_ASSOC);
            return $result;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$userId], $startTime);
        }

    }


    //**************************** bind siteCustom ****************************/

    public function insertUserCustomInfo(array $customInfo)
    {
        //迁移数据库
        if ($this->rebuildTable(true, $customInfo)) {
            return $this->ctx->SiteCustomTable->insertUserCustomInfo($customInfo);
        }

        return false;
    }

    public function deleteUserCustomInfo($customKey)
    {
        $customInfo = ["customKey" => $customKey];
        if ($this->rebuildTable(false, $customInfo)) {
            return $this->ctx->SiteCustomTable->deleteCustomInfo($this->customKeyType, $customKey);
        }
        return false;
    }

    private function rebuildTable($isAdd, $customInfo)
    {
        $dbType = $this->ctx->dbType;

        if ("mysql" == $dbType) {
            return $this->rebuildUserCustomTableForMysql($isAdd, $customInfo['customKey']);
        } else {
            return $this->rebuildUserCustomTableForSqlite($isAdd, $customInfo['customKey']);
        }
    }

    private function createSqliteTable($tableName, $columns)
    {
        $columns = $this->removeDefaultColumns($columns);
        $columns = array_unique($columns);
        $sql = "CREATE TABLE IF NOT EXISTS $tableName(
                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                      userId VARCHAR(100) UNIQUE NOT NULL,
                      phoneId VARCHAR(20),
                      email VARCHAR(100),
                      ";
        foreach ($columns as $column) {
            $sql .= "$column VARCHAR(100), \n";
        }

        $sql .= "addTime BIGINT);";
//        error_log("======new sqlit table sql=" . $sql);
        return $this->db->exec($sql) !== false;
    }

    private function removeDefaultColumns($columns)
    {
        if (empty($columns)) {
            return [];
        }

        foreach ($columns as $key => $column) {
            if (in_array($column, $this->defaultColumns)) {
                unset($columns[$key]);
            }
        }

        return $columns;
    }

    private function checkCustomColumns($columns)
    {
        $customColumns = $this->getAllColumns();
        $customColumns = array_merge($customColumns, $this->defaultColumns);
        foreach ($columns as $key => $column) {
            if (!in_array($column, $customColumns)) {
                unset($columns[$key]);
            }
        }

//        error_log("======custom columns = " . var_export($columns, true));
        return $columns;
    }

    private function rebuildUserCustomTableForMysql($isAddColumn, $alterColumnName)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $prepare = false;//充值prepare

        //migrate data to new table
        if ($isAddColumn) {
            $sql = "alter table $this->table add column $alterColumnName varchar(100);";
        } else {
            $sql = "alter table $this->table drop column $alterColumnName";
        }

        $prepare = $this->ctx->db->prepare($sql);
//        error_log("=========prepare errorInfo= " . var_export($prepare->errorInfo(), true));
        $flag = $prepare->execute();

        if ($prepare) {
            $dbErrCode = $prepare->errorCode();
            if (("00000" == $dbErrCode) || "42S21" == $dbErrCode) {
                return true;
            }
        }

        error_log($tag . " cause=" . var_export($prepare->errorInfo(), true));
        return false;
    }


    private function rebuildUserCustomTableForSqlite($isAddColumn, $alterColumnName)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $columns = $this->getSqliteTableColumns($this->table);

//        error_log("get sqlit table columns=" . var_export($columns, true));

        //alter table
        $dbFlag = $this->getTimeHMS();
        $tempTableName = $this->table . "_" . $dbFlag;
        $sql = "alter table $this->table rename to $tempTableName";
        $result = $this->db->exec($sql);

//        error_log("==========================rename result=" . $result);
        if ($result === false) {
            throw new Exception("rename table:$this->table to $tempTableName error");
        }

        try {
            //checkout column first
            $columns = $this->checkCustomColumns($columns);

            if (!$isAddColumn) {
                $columns = array_diff($columns, [$alterColumnName]);
            } else {
                $columns[] = $alterColumnName;
            }

//            error_log("==========================unset columns=" . var_export($columns, true));

            if (!$this->createSqliteTable($this->table, $columns)) {
                throw new Exception("rebuild sqlite table=" . $this->table . " error");
            }

            $columns = array_diff($columns, [$alterColumnName]);
            $columns = array_unique($columns);

            $queryColumnString = implode(",", $columns);//migrate data to new table
            $sql = "insert into $this->table($queryColumnString) select $queryColumnString from $tempTableName";

//            error_log("=========prepare sql= " . $sql);
            $prepare = false;//reset prepare
            $prepare = $this->ctx->db->prepare($sql);
//            error_log("=========prepare errorInfo= " . var_export($prepare->errorInfo(), true));
            $flag = $prepare->execute();
            $errCode = $prepare->errorCode();
            if ($flag && $errCode == "00000") {
                $this->dropDBTable($tempTableName);
                return true;
            }
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
            $this->rollbackSqliteRebuild($tempTableName, $this->table);
        }
        return false;
    }

    private function rollbackSqliteRebuild($oldTable, $newTable)
    {
        $sql = "drop table $newTable";
        $this->db->exec($sql);
        $sql = "alter table $oldTable rename to $newTable";
        $this->db->exec($sql);
    }

    public function updateUserCustomInfo($data, $where)
    {
        $where['keyType'] = $this->customKeyType;
        return $this->ctx->SiteCustomTable->updateCustomInfo($data, $where);
    }

    public function getAllColumns()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $columns = $this->ctx->SiteCustomTable->queryCustomKeys($this->customKeyType, false, false, $tag);
//        $this->logger->error("==============", "all custom keys for user=" . var_export($columns, true));
        return $columns;
    }

    public function getOpenColumns()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $columns = $this->ctx->SiteCustomTable->queryCustomKeys($this->customKeyType, false, true, $tag);
//        $this->logger->error("==============", "custom keys for user open=" . var_export($columns, true));
        return $columns;
    }

    public function getCustomByKey($customKey)
    {
        $info = $this->ctx->SiteCustomTable->queryCustomByKey($this->customKeyType, $customKey);
        return $info;
    }

    public function getColumnNames()
    {
        $columnInfos = $this->getAllColumnInfos();
        $columnNames = array_column($columnInfos, "keyName", "customKey");

//        error_log("==========get column Names=" . var_export($columnNames, true));
        return $columnNames;
    }

    public function getAllColumnInfos()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $customInfo = $this->ctx->SiteCustomTable->queryCustomInfo($this->customKeyType, false, $tag);
        return $customInfo;
    }

    public function getColumnInfosForRegister()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $status = Zaly\Proto\Core\UserCustomStatus::UserCustomRegisterRequired;
        $customInfo = $this->ctx->SiteCustomTable->queryCustomInfo($this->customKeyType, $status, $tag);

        return $customInfo;
    }

}
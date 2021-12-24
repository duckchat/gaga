<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/10/2018
 * Time: 8:44 PM
 */


class PassportPasswordCountLogTable extends BaseTable
{
    private $table = "passportPasswordCountLog";
    private $columns = [
        "id",
        "userId",
        "num",
        "operateDate",
        "operateTime"
    ];

    private $logTable = "passportPasswordLog";
    private $logColumns = [
        "id",
        "userId",
        "loginName",
        "ip",
        "operation",
        "operateDate",
        "operateTime"
    ];

    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertCountLogData($countLogData, $updateCountData, $logData)
    {
        $tag = __CLASS__.'->'.__FUNCTION__;
        try{
            $this->dbSlave->beginTransaction();
            try{
                $this->insertData($this->table, $countLogData, $this->columns, $tag);
            }catch (Exception $ex) {
                $userId = $updateCountData['userId'];
                $opreateDate = $updateCountData['operateDate'];
                $this->updateCountLogData($userId, $opreateDate);
            }
            $this->insertData($this->logTable, $logData, $this->logColumns, $tag);
            $this->dbSlave->commit();
        }catch (Exception $ex) {
            $this->dbSlave->rollBack();
            $this->ctx->Wpf_Logger->error($tag, $ex);
        }
    }

    public function updateCountLogData($userId, $opreateDate)
    {
        $tag = __CLASS__.'->'.__FUNCTION__;
        $sql = "update $this->table set num=num+1 where userId='{$userId}' and operateDate='{$opreateDate}'";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $result = $prepare->execute();
        $flag = $this->handlerUpdateResult($result, $prepare, $tag);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $opreateDate);
        if($flag) {
            return true;
        }
        throw new Exception("update failed");
    }

    public function deleteCountLogDataByUserId($userId, $opreateDate)
    {
        $tag = __CLASS__.'->'.__FUNCTION__;
        $sql = "delete from $this->table where userId='{$userId}' and operateDate='{$opreateDate}'";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $result = $prepare->execute();
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $opreateDate);
        return $this->handlerResult($result, $prepare, $tag);
    }

    public function getCountLogByUserId($userId, $opreateDate)
    {
        $tag = __CLASS__.'->'.__FUNCTION__;
        $sql = "select num from $this->table where userId='{$userId}' and operateDate='{$opreateDate}'";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->execute();
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $opreateDate);
        $result = $prepare->fetch(\PDO::FETCH_ASSOC);
        if($result) {
            return $result['num'];
        }
        return false;
    }
}
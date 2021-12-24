<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/10/2018
 * Time: 6:04 PM
 */

class PassportPasswordLogTable extends BaseTable
{
    private $table = "passportPasswordLog";
    private $columns = [
        "id",
        "userId",
        "loginName",
        "operation",
        "ip",
        "operateDate",
        "operateTime"
    ];

    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertLogData($log)
    {
        return $this->insertData($this->table, $log, $this->columns);
    }

    public function deleteLogData()
    {
        $tag = __CLASS__.'->'.__FUNCTION__;

        $sql = "delete from $this->table ";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        return $prepare->execute();
    }

    public function getLists($offset = 0, $limit = 200)
    {
        $tag = __CLASS__.'->'.__FUNCTION__;

        $sql = "select id, loginName, operation, ip, operateTime, operateDate from $this->table order by operateTime desc limit $offset, $limit";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }

    public function getAllCount()
    {
        $tag = __CLASS__.'->'.__FUNCTION__;

        $sql = "select count(id) as countNum from $this->table";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->execute();
        $countNum = $prepare->fetchColumn(0);
        return $countNum;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 24/08/2018
 * Time: 11:21 AM
 */
class PassportPasswordTokenTable extends BaseTable
{
    private $table = "passportPasswordToken";
    private $columns = [
        "id",
        "loginName",
        "token",
        "timeReg"
    ];

    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertCodeInfo($codeInfo)
    {
        return $this->insertData($this->table, $codeInfo, $this->columns);
    }

    public function updateCodeInfo($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    public function delCodeInfoByLoginName($loginName)
    {
        $tag = __CLASS__."_".__FUNCTION__;
        $startTime = microtime(true);
        try{
            $sql = "delete from $this->table where loginName=:loginName";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":loginName", $loginName);
            $prepare->execute();
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $loginName, $startTime);
            return true;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    public function getCodeInfoByLoginName($loginName)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where loginName=:loginName";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":loginName", $loginName);
            $prepare->execute();
            $codeInfo = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $loginName, $startTime);
            return $codeInfo;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/11/2018
 * Time: 3:40 PM
 */

class PassportCustomerServiceTable extends BaseTable
{
    private $table = "passportCustomerService";
    private $columns = [
        "id",
        "userId",
        "loginName",
        "password",
        "timeReg"
    ];

    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertUserInfo($userInfo)
    {
        return $this->insertData($this->table, $userInfo, $this->columns);
    }


    public function getUserByUserId($userId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where userId=:userId";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
            return $user;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    public function getUserByLoginName($loginName, $password=false)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where loginName=:loginName";
            if(!($password === false)) {
                $sql .=" and pwd = :pwd";
            }
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":loginName", $loginName);
            if(!($password === false)) {
                $prepare->bindValue(":pwd", $password);
            }
            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $loginName, $startTime);
            return $user;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    public function updateUserData($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }
}
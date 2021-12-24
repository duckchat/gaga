<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/08/2018
 * Time: 6:12 PM
 */

class PassportPasswordTable extends BaseTable
{
    private $table = "passportPassword";
    private $columns = [
        "id",
        "userId",
        "loginName",
        "nickname",
        "email",
        "password",
        "invitationCode",
        "timeReg"
    ];

    private $selectColumns;

    private $preSessionTable = "passportPasswordSession";

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

    public function getUserByEmail($email, $password=false)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where email=:email";
            if(!($password === false)) {
                $sql .=" and pwd = :pwd";
            }
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":email", $email);
            if(!($password === false)) {
                $prepare->bindValue(":pwd", $password);
            }
            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $email, $startTime);
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
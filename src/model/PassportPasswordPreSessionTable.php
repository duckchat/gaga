<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 23/08/2018
 * Time: 6:12 PM
 */

class PassportPasswordPreSessionTable extends  BaseTable
{
    private $table = "passportPasswordPreSession";
    private $columns = [
        "id",
        "userId",
        "preSessionId",
        "sitePubkPem"
    ];

    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertPreSessionData($info)
    {
        return $this->insertData($this->table, $info, $this->columns);
    }
    public function updatePreSessionData($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    public function getInfoByPreSessionId($preSessionId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select 
                        passportPassword.userId,
                        passportPassword.loginName,
                        passportPassword.nickname,
                        passportPassword.invitationCode,
                        passportPasswordPreSession.sitePubkPem
                    from 
                        passportPassword
                    inner join 
                        passportPasswordPreSession
                    on 
                        passportPassword.userId = passportPasswordPreSession.userId
                    where 
                        passportPasswordPreSession.preSessionId = :preSessionId";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":preSessionId", $preSessionId);
            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $preSessionId, $startTime);
            return $user;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    public function delInfoByPreSessionId($preSessionId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "delete from $this->table where preSessionId=:preSessionId";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":preSessionId", $preSessionId);
            $prepare->execute();
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $preSessionId, $startTime);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    public function delInfoByUserId($userId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "delete from $this->table where userId=:userId";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->execute();
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            throw new Exception("delete failed");
            return false;
        }
    }

}
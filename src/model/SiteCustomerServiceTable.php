<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/11/2018
 * Time: 5:31 PM
 */

class SiteCustomerServiceTable extends BaseTable
{

    private $table = "siteCustomerService";
    private $columns = [
        "id",
        "userId",
        "serviceTime",
    ];

    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertCustomerServiceData($info)
    {
        return $this->insertData($this->table, $info, $this->columns);
    }

    public function updateCustomerServiceData($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }


    public function getCustomerService()
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table order by serviceTime ASC limit 0,1";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->execute();
            $users = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, '', $startTime);
            return $users;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }


    public function getCustomerServiceLists($offset=0, $limit=200)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table  limit $offset, $limit";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->execute();
            $users = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, '', $startTime);
            return $users;
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
            return true;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            throw new Exception("delete failed");
            return false;
        }
    }

    public function getCustomerServiceByUserIds($userIds)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try{
            $userIdStr = implode("','",  $userIds);
            $sql = "select userId from $this->table where userId in ('$userIdStr')";
            $prepare = $this->dbSlave->prepare($sql);
            $prepare->execute();
            $users = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userIdStr, $startTime);
            return $users;
        }catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());

            return false;
        }
    }

    public function getCustomerServiceByUserId($userId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try{
            $sql = "select userId from $this->table where userId=:userId";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
            return $user;
        }catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }
}
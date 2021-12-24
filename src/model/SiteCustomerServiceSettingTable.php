<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 21/11/2018
 * Time: 11:36 AM
 */

class SiteCustomerServiceSettingTable extends BaseTable
{
    private $table = "siteCustomerServiceSetting";
    private $columns = [
        "id",
        "serviceKey",
        "serviceValue",
    ];

    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertCustomerServiceSettingData($info)
    {
        return $this->insertData($this->table, $info, $this->columns);
    }

    public function updateCustomerServiceData($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }


    public function getCustomerServiceSettingLists()
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table";
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


}
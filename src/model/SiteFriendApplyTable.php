<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 01/08/2018
 * Time: 4:45 PM
 *
 */

class SiteFriendApplyTable extends BaseTable
{
    private $table = "siteFriendApply";
    private $columns = [
        "id",
        "userId",
        "friendId",
        "greetings",
        "applyTime"
    ];

    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertApplyData($data)
    {
        return $this->insertData($this->table, $data, $this->columns);
    }

    public function updateApplyData($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    /**
     * remove friend apply data
     *
     * @param $fromUserId
     * @param $toUserId
     * @return bool
     * @throws Exception
     */
    public function deleteApplyData($fromUserId, $toUserId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();
        $sql = "delete from $this->table where userId=:userId and friendId=:friendId;";

        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $fromUserId);
            $prepare->bindValue(":friendId", $toUserId);
            $prepare->execute();
            $num = $prepare->rowCount();
            return $num > 0;
        } finally {
            $costTime = $this->getCurrentTimeMills() - $startTime;
            $this->ctx->Wpf_Logger->dbLog($tag, $sql, [$fromUserId, $toUserId], $costTime, $num);
        }
    }

    public function getDataByWhere($where, $limit = 0, $offset = 1)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        if (!is_array($where)) {
            throw new Exception("where params is error");
        }

        $whereKeys = array_keys($where);
        $whereStr = "";
        foreach ($whereKeys as $k => $key) {
            $whereStr .= " $key=:$key and";
        }
        $whereStr = trim($whereStr, "and");
        $sql = "select $this->selectColumns from $this->table where $whereStr limit :limit, :offset";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        foreach ($where as $key => $val) {
            $prepare->bindValue(":$key", $val);
        }
        $prepare->bindValue(":limit", $limit);
        $prepare->bindValue(":offset", $offset);
        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }


    /**
     * @param $userId
     * @param $applyUserId
     * @return mixed
     * @throws Exception
     */
    public function getApplyData($userId, $applyUserId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $sql = "select 
                    $this->selectColumns 
                from 
                    $this->table 
                where 
                    userId=:userId and friendId=:friendId order by applyTime desc limit 1;";

        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":userId", $userId);
        $prepare->bindValue(":friendId", $applyUserId);
        $prepare->execute();

        return $prepare->fetch(\PDO::FETCH_ASSOC);

    }


    public function getApplyList($friendId, $offset, $limit)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $sql = "
            select 
                siteFriendApply.greetings,
                siteFriendApply.applyTime,
                siteUser.userId,
                siteUser.avatar,
                siteUser.loginName,
                siteUser.nickname,
                siteUser.nicknameInLatin,
                siteUser.availableType
            from 
                siteFriendApply
            inner join 
                siteUser
            on 
                siteUser.userId = siteFriendApply.userId
            where 
                siteFriendApply.friendId = :friendId
            order BY
                siteFriendApply.applyTime DESC 
            limit 
                :offset, :limit;";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":friendId", $friendId);
        $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
        $prepare->bindValue(":limit", $limit, PDO::PARAM_INT);
        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->dbLog($tag, $sql, [$friendId, $offset, $limit], "", "");

        return $results;
    }

    public function getApplyListCount($friendId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $sql = "
            select 
                count(siteFriendApply.friendId) as count
            from 
                siteFriendApply
            inner join 
                siteUser
            on 
                siteUser.userId = siteFriendApply.userId
            where 
                siteFriendApply.friendId = :friendId
        ";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":friendId", $friendId);
        $prepare->execute();
        $results = $prepare->fetchColumn();
        return $results;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 23/07/2018
 * Time: 11:32 AM
 */

class SiteUserFriendTable extends BaseTable
{

    private $table = "siteUserFriend";
    private $columns = [
        "id",
        "userId",
        "friendId",
        "aliasName",
        "aliasNameInLatin",
        "relation",//1 表示我关注对方
        "mute", //0:not mute 1:mute(true)
        "version",
        "addTime"
    ];

    private $selectColumns;

    private $userTable = "siteUser";

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    public function saveUserFriend($userId, $friendUserId)
    {
        $relation = Zaly\Proto\Core\FriendRelationType::FriendRelationFollow;
        $data = [
            "userId" => $userId,
            "friendId" => $friendUserId,
            "relation" => (int)$relation,
            "mute" => 0, //false : 0
            "addTime" => $this->getCurrentTimeMills()
        ];
        return $this->insertData($this->table, $data, $this->columns);
    }


    public function queryUserFriendByPage($userId, $offset, $count)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();
        $friends = [];

        $relation = Zaly\Proto\Core\FriendRelationType::FriendRelationFollow;

        $sql = "SELECT
                    a.userId,a.loginName,a.nickname,a.nicknameInLatin,a.avatar,b.aliasName,b.aliasNameInLatin, b.mute
                FROM
                    $this->userTable AS a INNER JOIN $this->table AS b ON b.friendId = a.userId
                WHERE 
                  b.userId=:userId AND b.relation=:relation
                limit :offset, :limitCount;";
        try {

            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":relation", $relation, PDO::PARAM_INT);
            $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
            $prepare->bindValue(":limitCount", $count, PDO::PARAM_INT);
            $prepare->execute();

            $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($result)) {
                $friends = $result;
            }

        } finally {
            $this->ctx->Wpf_Logger->dbLog($tag, $sql, [$userId, $offset, $count], $startTime, count($friends));
        }

        return $friends;
    }

    public function queryUserFriend($userId, $friendUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        try {
            $sql = "SELECT $this->selectColumns FROM $this->table WHERE userId=:userId and friendId=:friendId;";
            $prepare = $this->dbSlave->prepare($sql);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":friendId", $friendUserId);
            $prepare->execute();

            $result = $prepare->fetch(\PDO::FETCH_ASSOC);

            return $result;
        } finally {
            $this->ctx->Wpf_Logger->dbLog($tag, $sql, [$userId], $startTime, $result);
        }

    }

    public function queryUserFriendMute($userId, $friendUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        try {
            $sql = "SELECT mute FROM $this->table WHERE userId=:userId and friendId=:friendId;";
            $prepare = $this->dbSlave->prepare($sql);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":friendId", $friendUserId);
            $prepare->execute();

            $result = $prepare->fetchColumn(0);
            return $result;
        } finally {
            $this->ctx->Wpf_Logger->dbLog($tag, $sql, [$userId, $friendUserId], $startTime, $result);
        }
    }

    public function queryUserFriendCount($userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();
        $count = 0;

        try {
            $sql = "SELECT COUNT(id) as num FROM $this->table WHERE userId=:userId;";
            $prepare = $this->dbSlave->prepare($sql);
            $prepare->bindValue(":userId", $userId);
            $prepare->execute();
            $result = $prepare->fetch(\PDO::FETCH_ASSOC);

            if (!empty($result) && !empty($result['num'])) {
                $count = $result['num'];
            }
        } finally {
            $this->ctx->Wpf_Logger->dbLog($tag, $sql, [$userId], $startTime, $result);
        }

        return $count;
    }

    /**
     * userId follow friendUserId? true : false
     *
     * @param $userId
     * @param $friendUserId
     * @return mixed
     */
    public function isFollow($userId, $friendUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        try {
            $sql = "SELECT COUNT(id) as `count` FROM $this->table WHERE userId=:userId and friendId=:friendId and relation=1;";
            $prepare = $this->dbSlave->prepare($sql);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":friendId", $friendUserId);
            $prepare->execute();
            $count = $prepare->fetchColumn();
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["userId" => $userId, "friendId" => $friendUserId], $startTime);
        }
        return $count;
    }

    public function isFriend($userId, $friendUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $relation1 = $this->isFollow($userId, $friendUserId);
        $this->ctx->Wpf_Logger->info($tag, "message count1 = " . $relation1);

        $relation2 = $this->isFollow($friendUserId, $userId);
        $this->ctx->Wpf_Logger->info($tag, "message count2 = " . $relation2);

        return $relation1 == 1 && $relation2 == 1;
    }


    /**
     * get relation
     *
     * @param $userId
     * @param $friendUserId
     * @return mixed
     */
    public function getRealtion($userId, $friendUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();
        $result = false;
        try {
            $sql = "SELECT $this->selectColumns as `count` FROM $this->table WHERE userId=:userId and friendId=:friendId;";
            $prepare = $this->dbSlave->prepare($sql);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":friendId", $friendUserId);
            $prepare->execute();
            $result = $prepare->fetch(\PDO::FETCH_ASSOC);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["userId" => $userId, "friendId" => $friendUserId], $startTime);
        }
        return $result;
    }

    public function updateData($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    public function deleteFriend($userId, $friendId)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        $sql = "delete from $this->table where userId = :userId and friendId = :friendId";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":userId", $userId);
        $prepare->bindValue(":friendId", $friendId);
        $prepare->execute();
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["userId" => $userId, "friendId" => $friendId], $startTime);

        if ($prepare->rowCount()) {
            return true;
        }
        throw  new Exception("delete failed");
    }

    /**
     * get user friendList not in group
     * @param $userId
     * @param $groupId
     * @param $offset
     * @param $pageSize
     * @return array|bool
     */
    public function getUserFriendListNotInGroup($userId, $groupId, $offset, $pageSize)
    {
        try {
            $startTime = microtime(true);
            $tag = __CLASS__ . "-" . __FUNCTION__;


            $sql = "SELECT
                    a.userId,a.loginName,a.nickname,a.nicknameInLatin,a.avatar,b.aliasName,b.aliasNameInLatin, b.mute
                FROM
                    $this->userTable AS a INNER JOIN $this->table AS b ON b.friendId = a.userId
                WHERE 
                  b.userId=:userId AND b.friendId not in (
                      select 
                        userId 
                      from 
                        siteGroupUser 
                      where groupId=:groupId) limit :offset, :pageSize;";

            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":groupId", $groupId);
            $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
            $prepare->bindValue(":pageSize", $pageSize, PDO::PARAM_INT);
            $prepare->execute();
            $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$userId, $groupId, $offset, $pageSize], $startTime);
            return $result;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/07/2018
 * Time: 2:37 PM
 */

class SiteGroupUserTable extends BaseTable
{
    public $table = "siteGroupUser";
    public $columns = [
        "groupId",
        "userId",
        "memberType",
        "isMute",
        "timeJoin",
    ];
    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    /*
     * 插入数据
     */
    public function insertGroupUserInfo($groupUserInfo)
    {
        return $this->insertData($this->table, $groupUserInfo, $this->columns);
    }

    public function deleteGroupMembers($groupId)
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;
        $sql = "delete from $this->table where groupId=:groupId;";

        $prepare = $this->db->prepare($sql);
        $prepare->bindValue(":groupId", $groupId);

        $result = $prepare->execute();

        return $this->handlerResult($result, $prepare, $tag);
    }

    public function updateGroupUserInfo($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    /**
     * 查看群里User的信息，或者指定角色的User的信息
     * @param $groupId
     * @param $userId
     * @param bool $memberType
     * @return mixed
     * @throws Exception
     */
    public function getGroupUser($groupId, $userId, $memberType = false)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);

        $sql = "select 
                    siteUser.userId as userId,
                    siteUser.loginName as loginName,
                    siteUser.nickname as nickname,
                    siteUser.nicknameInLatin as nicknameInLatin,
                    siteUser.avatar as avatar,
                    siteGroupUser.groupId as groupId,
                    siteGroupUser.memberType as memberType
                from 
                    siteGroupUser
                inner join 
                    siteUser
                on 
                    siteGroupUser.userId = siteUser.userId 
                where 
                    siteGroupUser.groupId=:groupId
                and 
                    siteGroupUser.userId=:userId";

        if ($memberType !== false) {
            $sql .= " and memberType=:memberType";
        }
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":groupId", $groupId);
        $prepare->bindValue(":userId", $userId);
        if ($memberType !== false) {
            $prepare->bindValue(":memberType", $memberType);
        }
        $prepare->execute();
        $result = $prepare->fetch(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);

        return $result;
    }

    /**
     * 查看群里User的信息，或者指定角色的User的信息
     * @param $groupId
     * @param $userIds
     * @param bool $memberType
     * @return mixed
     * @throws Exception
     */
    public function getGroupUsers($groupId, $userIds, $memberType = false)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);

        $userIdStr = implode("','", $userIds);
        $sql = "select 
                    siteUser.userId,
                    siteUser.loginName,
                    siteUser.nickname,
                    siteUser.nicknameInLatin,
                    siteUser.avatar,
                    siteGroupUser.groupId,
                    siteGroupUser.userId,
                    siteGroupUser.memberType
                from 
                    siteGroupUser
                inner join 
                    siteUser
                on 
                    siteGroupUser.userId = siteUser.userId 
                where 
                    siteGroupUser.groupId=:groupId
                and 
                    siteGroupUser.userId in('$userIdStr')";

        if ($memberType !== false) {
            $sql .= " and memberType=:memberType";
        }
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        if ($memberType !== false) {
            $prepare->bindValue(":memberType", $memberType);
        }
        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["groupId" => $groupId, "userIdStr" => $userIdStr], $startTime);

        return $results;
    }


    /**
     * 查看群里User的信息，或者指定角色的User的信息
     * @param $groupId
     * @param $offset
     * @param $pageSize
     * @param $memberType
     * @return mixed
     * @throws Exception
     */
    public function getGroupUserList($groupId, $offset, $pageSize, $memberType = false)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);

        $sql = "select 
                    siteUser.userId,
                    siteUser.loginName,
                    siteUser.nickname,
                    siteUser.nicknameInLatin,
                    siteUser.avatar,
                    siteGroupUser.groupId,
                    siteGroupUser.userId,
                    siteGroupUser.memberType
                from 
                    siteGroupUser
                inner join 
                    siteUser
                on 
                    siteGroupUser.userId = siteUser.userId 
                where 
                    siteGroupUser.groupId=:groupId";

        if ($memberType) {
            $sql .= " and siteGroupUser.memberType=:memberType";
        }

        $sql .= " order by siteGroupUser.memberType DESC,siteGroupUser.timeJoin ASC limit :offset, :pageSize;";

        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":groupId", $groupId);
        $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
        $prepare->bindValue(":pageSize", $pageSize, PDO::PARAM_INT);

        if ($memberType) {
            $prepare->bindValue(":memberType", $memberType);
        }

        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);

        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$groupId, $offset, $pageSize, $memberType], $startTime);
        return $results;
    }


    /**
     * 群主以及群管理员
     * @param $groupId
     * @param $userId
     * @param $adminType
     * @param $ownerType
     * @return mixed
     * @throws Exception
     */
    public function getGroupAdmin($groupId, $userId, $adminType, $ownerType)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "select $this->selectColumns from $this->table where groupId=:groupId and userId=:userId";
        $sql .= " and (memberType=:adminType or memberType=:ownerType)";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":groupId", $groupId);
        $prepare->bindValue(":userId", $userId);
        $prepare->bindValue(":adminType", $adminType, PDO::PARAM_INT);
        $prepare->bindValue(":ownerType", $ownerType, PDO::PARAM_INT);
        $prepare->execute();
        $results = $prepare->fetch(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        return $results;
    }

    /**
     * 获取群主， 所有的群管理员
     * @param $groupId
     * @param $adminType
     * @param $ownerType
     * @return mixed
     * @throws Exception
     */
    public function getGroupAllAdmin($groupId, $adminType, $ownerType)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);

        $sql = "select
                    siteUser.userId,
                    siteUser.loginName,
                    siteUser.nickname,
                    siteUser.nicknameInLatin,
                    siteUser.avatar,
                    siteGroupUser.groupId,
                    siteGroupUser.userId,
                    siteGroupUser.memberType
                from 
                    siteGroupUser
                inner join 
                    siteUser
                on 
                    siteGroupUser.userId = siteUser.userId 
                where 
                    siteGroupUser.groupId=:groupId 
                and 
                    (memberType=:adminType or memberType=:ownerType)";


        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":groupId", $groupId);
        $prepare->bindValue(":adminType", $adminType, PDO::PARAM_INT);
        $prepare->bindValue(":ownerType", $ownerType, PDO::PARAM_INT);
        $prepare->execute();
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        return $prepare->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * 得到群里角色的成员
     * @param $groupId
     * @param bool $memberType
     * @return array
     * @throws Exception
     */
    public function getGroupUserByMemberType($groupId, $memberType = false, $columns = [])
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $startTime = microtime(true);
        if (!$columns) {
            $columns = $this->selectColumns;
        } else {
            $columns = implode(",", $columns);
        }
        $sql = "select $columns from $this->table where groupId=:groupId and memberType=:memberType";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        $prepare->bindValue(":memberType", $memberType, PDO::PARAM_INT);
        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        return $results;
    }

    /**
     * 得到群下的所有成员
     * @param $groupId
     * @return array
     * @throws Exception
     */
    public function getGroupAllUser($groupId)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $startTime = microtime(true);

        $sql = "select $this->selectColumns from $this->table where groupId=:groupId";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":groupId", $groupId);
        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        return $results;
    }

    /**
     * 获取群成员id
     * @param $groupId
     * @return array
     * @throws Exception
     */
    public function getGroupAllMembersId($groupId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;
        try {
            $sql = "select userId from $this->table where groupId=:groupId";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":groupId", $groupId);
            $prepare->execute();
            return $prepare->fetchAll(\PDO::FETCH_ASSOC);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        }

    }

    /**
     * 得到群的总成员数目
     * @param $groupId
     * @return mixed
     * @throws Exception
     */

    public function getGroupUserCount($groupId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;
        $sql = "select count(id) as `count` from $this->table where groupId=:groupId";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        $prepare->execute();
        $result = $prepare->fetchColumn();
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        return $result;
    }

    public function getGroupUserMute($groupId, $userId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;
        $sql = "select isMute from $this->table where groupId=:groupId and userId=:userId;";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        $prepare->bindValue(":userId", $userId);
        $prepare->execute();
        $result = $prepare->fetchColumn(0);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        return $result;
    }

    public function getUserGroups($userId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $sql = "select groupId from $this->table where userId=:userId;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, " userId =" . $userId, $startTime);
            return $results;
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        }
    }

    public function getNotUserGroup($userId, $offset, $limit)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $sql = "select groupId from $this->table where userId!=:userId limit :offset,:limit;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":offset", $offset);
            $prepare->bindValue(":limit", $limit);
            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, " userId =" . $userId, $startTime);
            return $results;
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        }
    }

    /**
     * 判断userids是否在群中，返回在群中的userId
     * @param $userIds
     * @param $groupId
     * @return array|bool
     * @throws Exception
     */

    public function getUserIdExistInGroup($userIds, $groupId)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);

        $userIdStr = implode("','", $userIds);
        $sql = "select userId from $this->table where groupId=:groupId and userId in ('$userIdStr');";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        $prepare->execute();
        $results = $prepare->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
        $this->handlePrepareError($tag, $prepare);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userIdStr . " groupId =" . $groupId, $startTime);
        if ($results) {
            return array_keys($results);
        }
        return false;
    }

    /**
     * 从群中移除群成员
     * @param $userIds
     * @param $groupId
     * @return bool
     * @throws Exception
     */
    public function removeMemberFromGroup($userIds, $groupId)
    {
        if (!count($userIds)) {
            return true;
        }
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);
        $userIdStr = implode("','", $userIds);
        $sql = "delete from $this->table where  userId in ('$userIdStr') and groupId = :groupId ";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        $flag = $prepare->execute();
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userIdStr . " groupId =" . $groupId, $startTime);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function removeMemberRole($userIds, $groupId, $memberType)
    {
        $this->updateRole($userIds, $groupId, $memberType);
    }

    public function addMemberRole($userIds, $groupId, $memberType)
    {
        $this->updateRole($userIds, $groupId, $memberType);

    }

    public function updateMemberRole($userIds, $groupId, $adminMemberType, $nomalMemberType, $ownerMemberType)
    {
        try {
            $tag = __CLASS__ . "_" . __FUNCTION__;
            $startTime = microtime(true);
            $this->db->beginTransaction();
            $this->updateAllMemberRoleToNomal($groupId, $nomalMemberType, $ownerMemberType);
            $this->updateRole($userIds, $groupId, $adminMemberType);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, '', " groupId =" . $groupId, $startTime);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function updateAllMemberRoleToNomal($groupId, $nomalMemberType, $ownerMemberType)
    {
        if (!$groupId) {
            return true;
        }
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "update $this->table set memberType=:memberType where groupId = :groupId and memberType != :ownerMemberType";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":memberType", $nomalMemberType);
        $prepare->bindValue(":groupId", $groupId);
        $prepare->bindValue(":ownerMemberType", $ownerMemberType, PDO::PARAM_INT);
        $flag = $prepare->execute();
        $params = " groupId =$groupId  memberType =$nomalMemberType  ownerMemberType = $ownerMemberType ";
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $params, $startTime);
        if ($flag) {
            return true;
        }
        throw new Exception("updateAllMemberRoleToNomal operation failed");
    }

    private function updateRole($userIds, $groupId, $memberType)
    {
        if (!count($userIds)) {
            return true;
        }
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);
        $userIdStr = implode("','", $userIds);
        $sql = "update $this->table set memberType=:memberType where userId in ('$userIdStr') and groupId = :groupId";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":memberType", $memberType);
        $prepare->bindValue(":groupId", $groupId);
        $flag = $prepare->execute();
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userIdStr . " groupId =" . $groupId . " memberType =" . $memberType, $startTime);
        if ($flag) {
            return true;
        }
        throw new Exception("updateRole operation failed");
    }
}
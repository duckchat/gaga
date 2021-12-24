<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/07/2018
 * Time: 11:40 AM
 */

class SiteGroupTable extends BaseTable
{
    public $table = "siteGroup";
    public $columns = [
        "id",
        'groupId',
        "name",
        "nameInLatin",
        "owner",
        "avatar",
        "description",
        "descriptionType",
        "permissionJoin",
        "canGuestReadMessage",
        "canAddFriend",
        "speakers",
        "maxMembers",
        "status",
        "isWidget",
        "timeCreate"
    ];
    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
    }

    /**
     * 创建群组
     * @param $groupInfo
     * @return bool
     * @throws Exception
     */
    public function insertGroupInfo($groupInfo)
    {
        return $this->insertData($this->table, $groupInfo, $this->columns);
    }

    public function updateGroupInfo($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    /**
     * 解散群组
     * @param $groupId
     * @return bool
     * @throws Exception
     */
    public function deleteGroup($groupId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $startTime = microtime(true);
        if (strlen($groupId) < 1) {
            throw new Exception("delete group failed");
        }
        $sql = "delete from $this->table where groupId=:groupId";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        $result = $prepare->execute();
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        return $result;
    }

    /**
     * 获取群信息, 可以用来判断群是否有
     * @param $groupId
     * @return mixed
     * @throws Exception
     */
    //TODO 需要修改，判断群 status
    public function getGroupInfo($groupId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "select $this->selectColumns from $this->table where groupId=:groupId";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        $prepare->execute();
        $result = $prepare->fetch(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        return $result;
    }

    /**
     * 批量获取群组
     * @param array $groupIdList
     * @return null
     */
    public function getGroupListByGroupIds(array $groupIdList)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $startTime = microtime(true);
        $groupIdStr = implode("','", $groupIdList);
        $sql = "select $this->selectColumns from $this->table where groupId in ('$groupIdStr')";
        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->execute();
            $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupIdList, $startTime);
        }
        return null;
    }

    public function getGroupProfileByNameInLatin($nameInLatin, $pageNum = 1, $pageSize = 20)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "select $this->selectColumns from $this->table where nameInLatin like :nameInLatin limit :offset,:num;";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":nameInLatin", "%" . $nameInLatin . "%");
        $prepare->bindValue(":offset", ($pageNum - 1) * $pageSize, PDO::PARAM_INT);
        $prepare->bindValue(":num", $pageSize, PDO::PARAM_INT);
        $prepare->execute();
        $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $nameInLatin, $startTime);
        return $result;
    }


    public function getGroupProfileByName($name, $pageNum = 1, $pageSize = 20)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "select $this->selectColumns from $this->table where name like :name limit :offset,:num;";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":nameInLatin", "%" . $name . "%");
        $prepare->bindValue(":offset", ($pageNum - 1) * $pageSize, PDO::PARAM_INT);
        $prepare->bindValue(":num", $pageSize, PDO::PARAM_INT);
        $prepare->execute();
        $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $name, $startTime);
        return $result;
    }


    public function getGroupName($groupId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "select name from $this->table where groupId=:groupId";
        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":groupId", $groupId);
            $flag = $prepare->execute();
            $result = $prepare->fetch(\PDO::FETCH_ASSOC);
            if ($flag && $result) {
                return $result['name'];
            }
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
        }
        return null;
    }

    public function getSiteGroupCount()
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        try {
            $startTime = microtime(true);
            $sql = "select count(groupId) from  siteGroup;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->execute();
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [], $startTime);
            return $prepare->fetchColumn(0);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg = " . $ex->getMessage());
        }
    }

    /**
     * user的群总数
     * @param $userId
     * @return mixed
     */
    public function getGroupCount($userId)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        try {
            $startTime = microtime(true);
            $sql = "select 
                  count(siteGroupUser.groupId) as `count` 
                from  
                    siteGroupUser 
                inner join 
                    siteGroup 
                ON 
                    siteGroupUser.groupId = siteGroup.groupId
                where 
                    siteGroupUser.userId=:userId 
                and 
                    siteGroup.status>0
                ;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            $prepare->execute();
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
            return $prepare->fetchColumn(0);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg = " . $ex->getMessage());
        }
    }

    /**
     * user的群列表
     * @param $userId
     * @param $offset
     * @param $pageSize
     * @return array
     * @throws Exception
     */
    public function getGroupList($userId, $offset, $pageSize)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $sql = "select 
                    siteGroupUser.groupId,
                    siteGroupUser.userId,
                    siteGroupUser.memberType,
                    siteGroupUser.isMute,
                    siteGroupUser.timeJoin, 
                    siteGroup.groupId,
                    siteGroup.name,
                    siteGroup.nameInLatin,
                    siteGroup.owner,
                    siteGroup.avatar,
                    siteGroup.description,
                    siteGroup.descriptionType,
                    siteGroup.permissionJoin,
                    siteGroup.canGuestReadMessage,
                    siteGroup.canAddFriend,
                    siteGroup.speakers,
                    siteGroup.maxMembers,
                    siteGroup.timeCreate 
                from 
                    siteGroupUser
                inner join 
                    siteGroup
                on 
                    siteGroupUser.groupId = siteGroup.groupId 
                where 
                    siteGroupUser.userId=:userId
                and 
                    siteGroup.status>0
                ORDER BY
                    siteGroup.id DESC 
                limit 
                    $offset, $pageSize
                 ;
                ";

        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":userId", $userId);
        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        return $results;
    }

    public function getSiteGroupListByOffset($offset, $pageSize)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $sql = "select $this->selectColumns from $this->table ORDER BY id ASC limit $offset,$pageSize;";

        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$offset, $pageSize], $startTime);
        return $results;
    }

    /**
     * 群profile
     * @param $groupId
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    public function getGroupProfile($groupId, $userId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $sql = "select 
                    siteGroupUser.groupId,
                    siteGroupUser.userId,
                    siteGroupUser.memberType,
                    siteGroupUser.isMute,
                    siteGroupUser.timeJoin, 
                    siteGroup.groupId,
                    siteGroup.name,
                    siteGroup.nameInLatin,
                    siteGroup.owner,
                    siteGroup.avatar,
                    siteGroup.description,
                    siteGroup.descriptionType,
                    siteGroup.permissionJoin,
                    siteGroup.canGuestReadMessage,
                    siteGroup.canAddFriend,
                    siteGroup.speakers,
                    siteGroup.maxMembers,
                    siteGroup.timeCreate 
                from 
                    siteGroupUser
                inner join 
                    siteGroup
                on 
                    siteGroupUser.groupId = siteGroup.groupId 
                where 
                    siteGroupUser.groupId=:groupId
                    and siteGroupUser.userId = :userId
                limit 1
                ";

        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":groupId", $groupId);
        $prepare->bindValue(":userId", $userId);

        $prepare->execute();
        $result = $prepare->fetch(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["groupId" => $groupId, "userId" => $userId], $startTime);
        return $result;
    }

    public function getUserWidgetGroupProfile($userId, $groupId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $sql = "select 
                    siteGroupUser.groupId,
                    siteGroupUser.userId,
                    siteGroupUser.memberType,
                    siteGroupUser.isMute,
                    siteGroupUser.timeJoin, 
                    siteGroup.groupId,
                    siteGroup.name,
                    siteGroup.nameInLatin,
                    siteGroup.owner,
                    siteGroup.avatar,
                    siteGroup.description,
                    siteGroup.descriptionType,
                    siteGroup.permissionJoin,
                    siteGroup.canGuestReadMessage,
                    siteGroup.canAddFriend,
                    siteGroup.speakers,
                    siteGroup.maxMembers,
                    siteGroup.timeCreate 
                from 
                    siteGroupUser
                inner join 
                    siteGroup
                on 
                    siteGroupUser.groupId = siteGroup.groupId 
                where 
                    siteGroup.isWidget=1
                and 
                    siteGroup.groupId=:groupId

                and 
                    siteGroupUser.userId = :userId
                limit 1
                ";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":userId", $userId);
        $prepare->bindValue(":groupId", $groupId);

        $prepare->execute();
        $result = $prepare->fetch(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["userId" => $userId, 'groupId' => $groupId], $startTime);
        return $result;
    }


    public function getWidgetGroupProfile($groupId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $sql = "select 
                    siteGroup.groupId,
                    siteGroup.name,
                    siteGroup.nameInLatin,
                    siteGroup.owner,
                    siteGroup.avatar,
                    siteGroup.description,
                    siteGroup.descriptionType,
                    siteGroup.permissionJoin,
                    siteGroup.canGuestReadMessage,
                    siteGroup.canAddFriend,
                    siteGroup.speakers,
                    siteGroup.maxMembers,
                    siteGroup.timeCreate 
                from 
                    siteGroup
                where 
                    siteGroup.isWidget=1
                and 
                    siteGroup.groupId=:groupId
                limit 1
                ";
        $prepare = $this->dbSlave->prepare($sql);
        $prepare->bindValue(":groupId", $groupId);

        $this->handlePrepareError($tag, $prepare);
        $prepare->execute();
        $result = $prepare->fetch(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, "", $startTime);
        return $result;
    }
}
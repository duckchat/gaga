<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 19/07/2018
 * Time: 2:57 PM
 */

class SiteUserTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $table = "siteUser";
    private $columns = [
        "id",
        "userId",
        "loginName",
        "loginNameLowercase",
        "nickname",
        "nicknameInLatin",
        "avatar",
        "availableType",
        "countryCode",
        "phoneId",
        "friendVersion",
        "timeReg"
    ];

    private $selectColumns;

    private $friendTable = "siteUserFriend";

    public function init()
    {
        $this->logger = $this->ctx->getLogger();
        $this->selectColumns = implode(",", $this->columns);
    }

    public function insertUserInfo($userInfo)
    {
        return $this->insertData($this->table, $userInfo, $this->columns);
    }

    public function deleteUserProfile($userId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = $this->getCurrentTimeMills();

        $sql = "delete from $this->table where userId=:userId";
        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $flag = $prepare->execute();
            return $flag;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$userId], $startTime);
        }
    }

    public function getUserByUserId($userId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where userId=:userId";
            $prepare = $this->dbSlave->prepare($sql);
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

    public function getUserByLoginName($loginName)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where loginName=:loginName";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":loginName", $loginName);
            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $loginName, $startTime);
            return $user;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            return false;
        }
    }

    public function getUserByLoginNameLowercase($loginNameLowercase)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where loginNameLowercase=:loginNameLowercase;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":loginNameLowercase", $loginNameLowercase);
            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $loginNameLowercase, $startTime);
            return $user;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            return false;
        }
    }

    public function getUserByPhoneId($phoneId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where phoneId=:phoneId;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":phoneId", $phoneId);
            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $phoneId, $startTime);
            return $user;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            return false;
        }
    }

    //nicknameInLatin
    public function getUserByNicknameInLatin($nicknameInLatin)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select $this->selectColumns from $this->table where nicknameInLatin like :nicknameInLatin limit 20;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":nicknameInLatin", "%" . $nicknameInLatin . "%");
            $prepare->execute();
            $user = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $nicknameInLatin, $startTime);
            return $user;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            return false;
        }
    }

    public function getUserNickName($userId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $sql = "select nickname from $this->table where userId=:userId";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            $flag = $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);

            if ($flag && $user) {
                return $user['nickname'];
            }
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
        return '';
    }

    public function getFriendProfile($userId, $friendId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {

            $sql = "SELECT
                    a.userId,a.loginName,a.nickname,a.nicknameInLatin,a.avatar,a.availableType,b.aliasName,b.aliasNameInLatin,b.relation,b.mute
                FROM
                    $this->table AS a LEFT JOIN (SELECT userId,friendId,aliasName,aliasNameInLatin,relation,mute FROM $this->friendTable WHERE userId=:userId)AS b ON b.friendId = a.userId
                WHERE 
                  a.userId=:friendId;";

            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":friendId", $friendId);

            $prepare->execute();
            $user = $prepare->fetch(\PDO::FETCH_ASSOC);
            return $user;
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["userId" => $userId, "friendId" => $friendId], $startTime);
        }

    }

    public function getSiteUserListByOffset($offset, $length)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $sql = "select  
                        $this->selectColumns 
                    from 
                        siteUser 
                    order by id DESC limit :offset, :length";
        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
            $prepare->bindValue(":length", $length, PDO::PARAM_INT);
            $prepare->execute();
            $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            return false;
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$offset, $length], $startTime);
        }
    }

    /**
     * 小程序用户广场使用
     *
     * @param $userId
     * @param $pageNum
     * @param $pageSize
     * @return array|bool
     * @throws Exception
     */
    public function getSiteUserListWithRelation($userId, $pageNum, $pageSize)
    {
        $startTime = $this->getCurrentTimeMills();

        try {
            $tag = __CLASS__ . "->" . __FUNCTION__;
            $sql = "SELECT 
                        a.userId as userId ,
                        a.nickname as nickname,
                        a.nicknameInLatin as nicknameInLatin,
                        a.avatar as avatar,
                        a.availableType as availableType,
                        b.friendId as friendId 
                    FROM 
                        siteUser AS a 
                    LEFT JOIN 
                        (SELECT userId,friendId FROM siteUserFriend WHERE userId=:userId) AS b 
                    ON a.userId=b.friendId 
                    ORDER BY a.id DESC LIMIT :pageNum,:pageSize;";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":pageNum", (int)(($pageNum - 1) * $pageSize), PDO::PARAM_INT);
            $prepare->bindValue(":pageSize", (int)$pageSize, PDO::PARAM_INT);
            $prepare->execute();

//            $this->logger->error($tag, "result=" . var_export($prepare->errorInfo(), true));

            $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$userId, $pageNum, $pageSize], $startTime);
        }
        return false;
    }

    public function getUserListNotInGroup($groupId, $offset, $pageSize)
    {
        try {
            $startTime = microtime(true);
            $tag = __CLASS__ . "-" . __FUNCTION__;
            ////TODO 待优化
            $sql = "select  
                        $this->selectColumns 
                    from 
                        siteUser 
                    where 
                        userId 
                    not in 
                        (select 
                            userId 
                        from 
                            siteGroupUser 
                        where groupId=:groupId) 
                    order by 
                        timeReg DESC 
                    limit 
                        :offset, :pageSize";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":groupId", $groupId);
            $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
            $prepare->bindValue(":pageSize", $pageSize, PDO::PARAM_INT);
            $prepare->execute();
            $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
            return $result;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    public function getUserCount($groupId)
    {
        try {
            $startTime = microtime(true);
            $tag = __CLASS__ . "-" . __FUNCTION__;
            ////TODO 待优化
            $sql = "select count(userId) as `count` from siteUser where userId not in (select userId from siteGroupUser where groupId=:groupId);";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":groupId", $groupId);
            $prepare->execute();
            $result = $prepare->fetchColumn();
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $groupId, $startTime);
            return $result;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    /**
     * get site total user count
     * @return bool|mixed
     */
    public function getSiteUserCount()
    {
        try {
            $startTime = microtime(true);
            $tag = __CLASS__ . "-" . __FUNCTION__;
            $sql = "select count(userId) as `count` from siteUser;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->execute();
            $result = $prepare->fetchColumn(); // 0
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [], $startTime);
            return $result;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    public function getUserByUserIds($userIds)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        try {
            $userIdStr = implode("','", $userIds);
            $sql = "select $this->selectColumns from $this->table where userId in ('$userIdStr')";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userIdStr, $startTime);
            return $results;

        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            return false;
        }
    }

    public function getUserFriendVersion($userId)
    {
        $tag = __CLASS__ . "-" . __FILE__;
        $startTime = microtime(true);
        $sql = "select friendVersion from $this->table where userId=:userId;";
        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->execute();

            $results = $prepare->fetchColumn(0);
            if (empty($results)) {
                return 0;
            }
            return $results;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, $results, $startTime);
        }
    }

    public function updateUserData($where, $data)
    {
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    public function updateUserFriendVersion($userId, $friendVersion)
    {
        $where = ['userId' => $userId];
        $data = ['friendVersion' => $friendVersion];
        return $this->updateInfo($this->table, $where, $data, $this->columns);

    }

    public function updateNextFriendVersion($userId)
    {
        $version = $this->getUserFriendVersion($userId);

        $friendVersion = 1;
        if (!empty($version)) {
            $friendVersion = $version + 1;
        }
        return $this->updateUserFriendVersion($userId, $friendVersion);
    }

    /**
     * 根据条件查找站点用户profile
     *
     * @param $userId
     * @param $pageNum
     * @param $pageSize
     * @return array|bool
     * @throws Exception
     */
    public function getSiteUserListWithRelationByLoginName($userId, $loginName, $pageNum, $pageSize)
    {
        $startTime = $this->getCurrentTimeMills();
        try {
            $tag = __CLASS__ . "->" . __FUNCTION__;
            $sql = "SELECT 
                        a.userId as userId ,
                        a.nickname as nickname,
                        a.loginName as loginName,
                        a.nicknameInLatin as nicknameInLatin,
                        a.avatar as avatar,
                        a.availableType as availableType,
                        b.friendId as friendId 
                    FROM 
                        siteUser AS a 
                    LEFT JOIN 
                        (SELECT userId,friendId FROM siteUserFriend WHERE userId=:userId) AS b 
                    ON a.userId=b.friendId 
                    WHERE  (a.loginName like :loginName or a.loginNameLowercase like :loginName)
                    ORDER BY a.id DESC LIMIT :pageNum,:pageSize;";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":pageNum", (int)(($pageNum - 1) * $pageSize), PDO::PARAM_INT);
            $prepare->bindValue(":pageSize", (int)$pageSize, PDO::PARAM_INT);
            $prepare->bindValue(":loginName", "%$loginName%");
            $prepare->execute();

//            $this->logger->error($tag, "result=" . var_export($prepare->errorInfo(), true));

            $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$userId, $pageNum, $pageSize], $startTime);
        }
        return false;
    }

    /**
     * 根据条件查找站点用户profile
     *
     * @param $userId
     * @param $pageNum
     * @param $pageSize
     * @return array|bool
     * @throws Exception
     */
    public function getSiteUserListWithRelationByUserId($userId, $searchUserIds)
    {
        $startTime = $this->getCurrentTimeMills();
        $searchUserIdStr = implode("','", $searchUserIds);
        try {
            $tag = __CLASS__ . "->" . __FUNCTION__;
            $sql = "SELECT 
                        a.userId as userId ,
                        a.nickname as nickname,
                        a.loginName as loginName,
                        a.nicknameInLatin as nicknameInLatin,
                        a.avatar as avatar,
                        a.availableType as availableType,
                        b.friendId as friendId 
                    FROM 
                        siteUser AS a 
                    LEFT JOIN 
                        (SELECT userId,friendId FROM siteUserFriend WHERE userId=:userId) AS b 
                    ON a.userId=b.friendId 
                    WHERE  (a.userId in ('{$searchUserIdStr}')) ";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->execute();
            $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$userId, $searchUserIdStr], $startTime);
        }
        return false;
    }
}
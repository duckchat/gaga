<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 13/11/2018
 * Time: 4:35 PM
 */

class SearchSiteTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;

    public function init()
    {
        $this->logger = $this->ctx->getLogger();
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
    public function getSiteUserListWithRelationByNickname($userId, $nickname, $pageNum, $pageSize)
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
                    WHERE  (a.nickname like :nickname)
                    ORDER BY a.id DESC LIMIT :pageNum,:pageSize;";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":pageNum", (int)(($pageNum - 1) * $pageSize), PDO::PARAM_INT);
            $prepare->bindValue(":pageSize", (int)$pageSize, PDO::PARAM_INT);
            $prepare->bindValue(":nickname", "%$nickname%");
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


    public function getGroupProfileByName($name, $pageNum = 1, $pageSize = 20)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "select id,
                        groupId,
                        name,
                        nameInLatin,
                        owner,
                        avatar,
                        description,
                        descriptionType,
                        permissionJoin,
                        canGuestReadMessage,
                        canAddFriend,
                        speakers,
                        maxMembers,
                        status,
                        isWidget,
                        timeCreate 
                from siteGroup where name like :name limit :offset,:num;";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":name", "%" . $name . "%");
        $prepare->bindValue(":offset", ($pageNum - 1) * $pageSize, PDO::PARAM_INT);
        $prepare->bindValue(":num", $pageSize, PDO::PARAM_INT);
        $prepare->execute();
        $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $name, $startTime);
        return $result;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 23/07/2018
 * Time: 11:32 AM
 */


class siteU2MessageTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $table = "siteU2Message";
    private $columns = ["id", "msgId", "userId", "fromUserId", "toUserId", "roomType", "msgType", "content", "msgTime"];

    private $pointerTable = "siteU2MessagePointer";
    private $pointerColumns = ["userId", "deviceId", "clientSideType", "pointer"];


    function init()
    {
        $this->logger = $this->ctx->getLogger();
    }

    /**
     * @param $u2Message
     * @return bool
     * @throws Exception
     */
    public function insertMessage($u2Message)
    {
        return $this->insertData($this->table, $u2Message, $this->columns);
    }

    public function deleteMessage($userId)
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;
        $sql = "delete from $this->table where userId=:userId or fromUserId=:fromUserId;";

        $prepare = $this->db->prepare($sql);
        $prepare->bindValue(":userId", $userId);
        $prepare->bindValue(":fromUserId", $userId);

        $result = $prepare->execute();

        $this->logger->writeSqlLog($tag, $sql, [$userId], $this->getCurrentTimeMills());

        return $this->handlerResult($result, $prepare, $tag);
    }

    public function deleteMessageByTime($msgTime)
    {
        $tag = __CLASS__ . '->' . __FUNCTION__;
        $sql = "delete from $this->table where msgTime <=:msgTime;";

        $prepare = $this->db->prepare($sql);
        $prepare->bindValue(":msgTime", $msgTime);

        $result = $prepare->execute();

        $this->logger->writeSqlLog($tag, $sql, [$msgTime], $this->getCurrentTimeMills());

        return $this->handlerResult($result, $prepare, $tag);
    }

    function deleteMessagePointer($userId)
    {
        $startTime = $this->getCurrentTimeMills();
        $tag = __CLASS__ . '->' . __FUNCTION__;
        $sql = "delete from $this->pointerTable where userId=:userId;";

        $prepare = $this->db->prepare($sql);
        $prepare->bindValue(":userId", $userId);

        $result = $prepare->execute();

        $this->logger->writeSqlLog($tag, $sql, [$userId], $startTime);

        return $this->handlerResult($result, $prepare, $tag);
    }

    function updateMessageType($msgId, $msgType)
    {
        $startTime = $this->getCurrentTimeMills();
        $tag = __CLASS__ . '->' . __FUNCTION__;
        $sql = "update $this->table set msgType=:msgType where msgId=:msgId;";

        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":msgType", $msgType);
        $prepare->bindValue(":msgId", $msgId);

        $result = $prepare->execute();

        $this->logger->writeSqlLog($tag, $sql, [$msgId, $msgType], $startTime);

        return $this->handlerUpdateResult($result, $prepare, $tag);
    }

    /**
     * 查询群组消息
     * @param string $userId
     * @param int $offset
     * @param int $limitCount
     * @return array
     * @throws Exception
     */
    public function queryMessage($userId, $offset, $limitCount)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;

        $notice = Zaly\Proto\Core\MessageType::MessageNotice;
        $webNotice = Zaly\Proto\Core\MessageType::MessageWebNotice;
        $queryFields = implode(",", $this->columns);
        $sql = "select $queryFields 
                from $this->table 
                where 
                  id>:offset 
                  and 
                  (userId=:userId or (fromUserId=:fromUserId and msgType not in ($notice,$webNotice))) 
                order by id limit :limitCount;";

        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":fromUserId", $userId);
            $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
            $prepare->bindValue(":limitCount", $limitCount, PDO::PARAM_INT);

            $prepare->execute();
            return $prepare->fetchAll(\PDO::FETCH_ASSOC);
        } finally {
            $costTime = microtime(true) - $startTime;
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$userId, $offset, $limitCount], $costTime);
        }

        return [];
    }

    public function queryMessageByFromUserIdAndMsgId($fromUserId, $msgId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;

        $queryFields = implode(",", $this->columns);
        $sql = "select $queryFields from $this->table where msgId=:msgId and fromUserId=:fromUserId limit 1;";

        try {

            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":fromUserId", $fromUserId);
            $prepare->bindValue(":msgId", $msgId);

            $prepare->execute();
            return $prepare->fetch(\PDO::FETCH_ASSOC);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$fromUserId, $msgId], $startTime);
        }

    }

    /**
     * 通过msgId查询表中消息
     *
     * @param $msgIdArrays
     * @return array
     * @throws Exception
     */
    public function queryMessageByMsgId($msgIdArrays)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;

        $result = empty($msgIdArrays);

        if ($result) {
            return [];
        }

        $queryFields = implode(",", $this->columns);
        $sql = "select $queryFields from $this->table ";

        try {
            $inSql = implode("','", $msgIdArrays);
            $sql .= "where msgId in ('$inSql') limit 100;";

            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->execute();

            return $prepare->fetchAll(\PDO::FETCH_ASSOC);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $msgIdArrays, $startTime);
        }

        return [];
    }


    /**
     * @param $msgIdArrays
     * @return array
     * @throws Exception
     */
    public function queryColumnMsgIdByMsgId($msgIdArrays)
    {
        $tag = __CLASS__ . "." . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        $result = empty($msgIdArrays);

        if ($result) {
            return [];
        }

        $sql = "select msgId from $this->table ";

        try {
            $inSql = implode("','", $msgIdArrays);
            $sql .= "where msgId in ('$inSql') limit 50;";

            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->execute();

            return $prepare->fetchAll(\PDO::FETCH_COLUMN);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $msgIdArrays, $startTime);
        }

        return [];
    }


    /**
     * @param $userId
     * @param $deviceId
     * @param $clientSideType
     * @param $pointer
     * @return bool
     * @throws Exception
     */
    public function updatePointer($userId, $deviceId, $clientSideType, $pointer)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        try {
            $result = $this->updateU2Pointer($userId, $deviceId, $clientSideType, $pointer);
            if (!$result) {
                $this->saveU2Pointer($userId, $deviceId, $clientSideType, $pointer);
            }
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
        return false;
    }

    /**
     * @param $userId
     * @param $deviceId
     * @param $clientSideType
     * @param $pointer
     * @return bool
     * @throws Exception
     */
    private function updateU2Pointer($userId, $deviceId, $clientSideType, $pointer)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;

        $sql = "update $this->pointerTable set pointer=:pointer,clientSideType=:clientSideType where userId=:userId and deviceId=:deviceId";


        try {

            if (empty($clientSideType)) {
                $clientSideType = Zaly\Proto\Core\UserClientType::UserClientMobileInvalid;
            }

            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":pointer", $pointer, PDO::PARAM_INT);
            $prepare->bindValue(":clientSideType", $clientSideType);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":deviceId", $deviceId);

            $result = $prepare->execute();

            if ($result) {
                $count = $prepare->rowCount();
                return $count > 0;
            }
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$pointer, $userId, $deviceId], $startTime);
        }

        return false;
    }


    /**
     * 保存二人消息游标
     *
     * @param $userId
     * @param $deviceId
     * @param $clientSideType
     * @param $pointer
     * @return bool
     * @throws Exception
     */
    private function saveU2Pointer($userId, $deviceId, $clientSideType, $pointer)
    {
        if (empty($clientSideType)) {
            // 0：默认未知的类型
            // 1：mobile
            // 2：web
            $clientSideType = Zaly\Proto\Core\UserClientType::UserClientMobileInvalid;
        }

        $data = [
            "userId" => $userId,
            "deviceId" => $deviceId,
            "clientSideType" => (int)$clientSideType,
            "pointer" => (int)$pointer,
        ];
        return $this->insertData($this->pointerTable, $data, $this->pointerColumns);
    }

    /**
     * 查询用户的群组消息游标
     *
     * @param $userId
     * @param $deviceId
     * @return int
     * @throws Exception
     */
    public function queryU2Pointer($userId, $deviceId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;
        $sql = "select pointer from $this->pointerTable where userId=:userId and deviceId=:deviceId";

        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":deviceId", $deviceId);

            $prepare->execute();

            $result = $prepare->fetch(PDO::FETCH_ASSOC);
            if (!empty($result)) {
                return $result["pointer"];
            }

            return 0;
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["userId" => $userId, "deviceId" => $deviceId], $startTime);
        }

    }

    /**
     * 多个设备中，最大的那个游标
     *
     * @param $userId
     * @return int
     * @throws Exception
     */
    public function queryMaxU2Pointer($userId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;
        $sql = "select max(pointer) as pointer from $this->pointerTable where userId=:userId";
        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            $prepare->execute();
            $pointerInfo = $prepare->fetch(PDO::FETCH_ASSOC);

            if (!empty($pointerInfo)) {
                return $pointerInfo["pointer"];
            }
            return 0;
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["userId" => $userId], $startTime);
        }
    }

    public function queryMaxMsgId($userId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;
        $sql = "select max(id) as pointer from $this->table where userId=:userId or fromUserId=:fromUserId;";
        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":fromUserId", $userId);
            $prepare->execute();
            $pointerInfo = $prepare->fetch(PDO::FETCH_ASSOC);

            if (!empty($pointerInfo)) {
                return $pointerInfo['pointer'];
            }
            return 0;
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, ["userId" => $userId], $startTime);
        }
    }

    public function getU2LastChat($userId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "." . __FUNCTION__;

        $sql = "select 
            userId, fromUserId, toUserId , msgTime, content, msgType
        from 
            (
                select siteU2Message.msgId, siteU2Message.msgType , siteU2Message.userId, siteU2Message.fromUserId, siteU2Message.toUserId, siteU2Message.content , (siteU2Message.toUserId || siteU2Message.fromUserId) as concatId, siteU2Message.msgTime from siteU2Message where (siteU2Message.fromUserId in (select friendId  from siteUserFriend where userId = :userId) and siteU2Message.toUserId = :userId)
                union 
                select siteU2Message.msgId, siteU2Message.msgType ,siteU2Message.userId, siteU2Message.fromUserId, siteU2Message.toUserId, siteU2Message.content,  (siteU2Message.fromUserId || siteU2Message.toUserId) as concatId , siteU2Message.msgTime from siteU2Message where (siteU2Message.toUserId in (select friendId  from siteUserFriend where userId = :userId) and siteU2Message.fromUserId = :userId)
                order by msgTime asc
         ) as Message  group by concatId order by msgTime Desc;";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":userId", $userId);
        $prepare->execute();
        $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 20/07/2018
 * Time: 12:12 PM
 */

class SiteSessionTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $table = "siteSession";
    private $columns = [
        "id",
        "sessionId",
        "userId",
        "deviceId",
        "devicePubkPem",
        "clientSideType",
        "timeWhenCreated",
        "ipWhenCreated",
        "timeActive",
        "ipActive",
        "userAgent",
        "userAgentType",
        "gatewayURL",
        "gatewaySocketId",
        "loginPluginId",
    ];
    private $selectColumns;

    public function init()
    {
        $this->selectColumns = implode(",", $this->columns);
        $this->logger = $this->ctx->getLogger();
    }

    public function getSessionInfoByUserId($userId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $sql = "select $this->selectColumns from $this->table where userId=:userId order by timeActive DESC limit 1";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":userId", $userId);
        $prepare->execute();
        $sessionInfo = $prepare->fetch(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        return $sessionInfo;
    }


    public function getUserSessionsByUserId($userId, $clientType = Zaly\Proto\Core\UserClientType::UserClientMobileApp)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . '-' . __FUNCTION__;

        $sql = "select 
                  $this->selectColumns from $this->table 
                where userId=:userId and clientSideType=:clientSideType order by timeActive DESC;";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":userId", $userId);
        $prepare->bindValue(":clientSideType", $clientType, PDO::PARAM_STR);
        $prepare->execute();

        $sessionInfo = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        return $sessionInfo;
    }

    public function getAllSessionInfoByUserId($userId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $sql = "select $this->selectColumns from $this->table where userId=:userId";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);
        $prepare->bindValue(":userId", $userId);
        $prepare->execute();

        $sessionInfo = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        return $sessionInfo;
    }

    public function getSessionInfoBySessionId($sessionId)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $sql = "select $this->selectColumns from $this->table where sessionId=:sessionId";
        $prepare = $this->db->prepare($sql);
        $this->handlePrepareError($tag, $prepare);

        $prepare->bindValue(":sessionId", $sessionId);
        $prepare->execute();
        $sessionInfo = $prepare->fetch(\PDO::FETCH_ASSOC);
        $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $sessionId, $startTime);
        return $sessionInfo;
    }

    public function insertSessionInfo($sessionInfo)
    {
        return $this->insertData($this->table, $sessionInfo, $this->columns);
    }

    public function updateSessionInfo($where, $sessionInfo)
    {
        return $this->updateInfo($this->table, $where, $sessionInfo, $this->columns);
    }

    public function updateSessionActive($sessionId)
    {
        $where = [
            'sessionId' => $sessionId
        ];
        $data = [
            "timeActive" => $this->getCurrentTimeMills(),
        ];
        return $this->updateInfo($this->table, $where, $data, $this->columns);
    }

    public function deleteSession($userId, $sessionId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = microtime(true);
        try {
            $sql = "delete from $this->table where sessionId=:sessionId and userId=:userId;";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":sessionId", $sessionId);
            $prepare->bindValue(":userId", $userId);
            return $prepare->execute();
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $sessionId, $startTime);
        } finally {

        }
        return false;
    }

    public function deleteSessionByDeviceId($deviceId)
    {

        $tag = __CLASS__ . '-' . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        $sql = "delete from $this->table where deviceId=:deviceId;";
        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":deviceId", $deviceId);
            $flag = $prepare->execute();
            return $flag;
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$deviceId], $startTime);
        }

        return false;
    }

    public function deleteSessionByUserIdAndDeviceId($userId, $deviceId)
    {

        $tag = __CLASS__ . '-' . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        $sql = "delete from $this->table where userId=:userId and deviceId=:deviceId;";
        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":deviceId", $deviceId);
            $flag = $prepare->execute();
            return $flag;
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$userId, $deviceId], $startTime);
        }

        return false;
    }

    public function getUserLatestDeviceId($userId, $clientSideType, $limit = 1)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "select deviceId from $this->table where userId=:userId and clientSideType=:clientSideType order by timeActive DESC limit :limit;";

        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":clientSideType", $clientSideType, PDO::PARAM_INT);
            $prepare->bindValue(":limit", $limit, PDO::PARAM_INT);
            $flag = $prepare->execute();
            $userTokenInfo = $prepare->fetchAll(\PDO::FETCH_ASSOC);

            if ($flag) {
                return $userTokenInfo;
            }

        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        }
        return null;
    }

    public function getWebUserSessionInfo($userId)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        $startTime = microtime(true);
        $sql = "select $this->selectColumns from $this->table where userId=:userId and deviceId=:deviceId limit 1;";

        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":deviceId", sha1(""));
            $flag = $prepare->execute();
            $userSessionInfo = $prepare->fetch(\PDO::FETCH_ASSOC);
            return $userSessionInfo;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        }
        return false;
    }

    public function deleteSessionByUserId($userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = microtime(true);
        try {
            $sql = "delete from $this->table where userId=:userId;";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);

            $prepare->bindValue(":userId", $userId);
            return $prepare->execute();
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        } finally {

        }
        return false;
    }

    //禁止使用
    public function deleteLimitSession($userId, $limitCount, $clientType = Zaly\Proto\Core\UserClientType::UserClientMobileApp)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = microtime(true);

        if (empty($limitCount)) {
            $limitCount = 1;
        }

        try {
            $sql = "delete from $this->table 
                  where userId=:userId and clientSideType=:clientSideType
                  and deviceId NOT IN (
                    select s.deviceId from (
                          select  deviceId from $this->table 
                          where userId=:userId and clientSideType=:clientSideType order by timeActive DESC limit :limitNum) as s
                  );";
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":limitNum", (int)$limitCount, PDO::PARAM_INT);
            $prepare->bindValue(":clientSideType", $clientType, PDO::PARAM_INT);
            $result = $prepare->execute();

            return $result;
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        }
        return false;
    }

    //使用此方法替代上面方法

    /**
     * @param string $userId
     * @param $deviceIds
     * @param int $clientType
     * @return bool
     */
    public function removeLimitSession($userId, $deviceIds, $clientType = Zaly\Proto\Core\UserClientType::UserClientMobileApp)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = microtime(true);

        try {
            if (empty($deviceIds)) {
                return true;
            }

            $deviceIdStr = implode("','", $deviceIds);

            $sql = "delete from $this->table 
                  where userId=:userId and clientSideType=:clientSideType
                  and deviceId IN ('{$deviceIdStr}');";

            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":clientSideType", $clientType, PDO::PARAM_INT);
            $result = $prepare->execute();

            return $result;
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $userId, $startTime);
        }
        return false;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 11:24 AM
 */

class SiteUicTable extends BaseTable
{
    private $tableName = "siteUic";
    /**
     * status 说明：
     *      -1：表示无效的uic
     *       0：表示无用状态，已经被使用
     *       1：所有人可用
     *       2：过期的token useTime 表示过期时间
     */
    private $columns = [
        "id",
        "code",
        "userId",
        "status",
        "createTime",
        "useTime"
    ];

    private $queryColumns;

    public function init()
    {
        $this->queryColumns = implode(",", $this->columns);
    }

    /**
     * @param int $count 一次生成多少个
     * @param int $length 每个uic的长度
     * @return bool
     * @throws Exception
     */
    public function createUic($count = 20, $length = 16)
    {
        $startTime = $this->ctx->ZalyHelper->getMsectime();
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $sql = "insert into $this->tableName (code,status,createTime) values (:code,1,:createTime);";
        $rowCount = 0;
        try {
            $this->db->beginTransaction();

            for ($i = 0; $i < $count; $i++) {
                $code = $this->ctx->ZalyHelper->generateNumberKey($length);
                $time = $this->ctx->ZalyHelper->getMsectime();

                $prepare = $this->db->prepare($sql);
                $this->handlePrepareError($tag, $prepare);
                $prepare->bindValue(":code", $code);
                $prepare->bindValue(":createTime", $time);

                $res = $prepare->execute();

                if ($res) {
                    $rowCount++;
                }
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->ctx->Wpf_Logger->info($tag, $e);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [], $startTime);
        }

        return $rowCount > 0;
    }

    public function deleteAllUnusedCode()
    {
        $sql = "delete from $this->tableName where status >= 1;";
        return $this->db->exec($sql);
    }

    public function deleteUnusedCode($code)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();
        $sql = "delete from $this->tableName where code=:code and status >= 1;";

        try {
            $prepare = $this->db->prepare($sql);
            $prepare->bindValue(":code", $code);
            $flat = $prepare->execute();
            return $flat;
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$code], $startTime);
        }

    }

    public function updateUicUsed($code, $userId)
    {
        $startTime = $this->getCurrentTimeMills();
        $tag = __CLASS__ . "->" . __FUNCTION__;

        $sql = "update siteUic set userId=:userId,status=:status,useTime=:useTime where code=:code;";
        try {
            $prepare = $this->db->prepare($sql);

            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":userId", $userId);
            $prepare->bindValue(":status", 0, PDO::PARAM_INT);
            $prepare->bindValue(":useTime", $startTime);
            $prepare->bindValue(":code", $code);

            $flat = $prepare->execute();

            $rowCount = $prepare->rowCount();

            if ($flat && $rowCount > 0) {
                return true;
            }

            return fase;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$code, $userId], $startTime);
        }

        return false;
    }

    /**
     * 联表查询，需要获取当前使用的用户信息
     * @param $pageNum
     * @param $pageSize
     * @return array
     * @throws Exception
     */
    public function queryUsedUic($pageNum, $pageSize)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        $offset = ($pageNum - 1) * $pageSize;

        $sql = "select a.code,a.userId,a.status,a.createTime,a.useTime,b.nickname,b.loginName
                from $this->tableName as a left join siteUser as b on a.userId = b.userId
                where status=0 limit :offset,:pageSize;";

        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
            $prepare->bindValue(":pageSize", $pageSize, PDO::PARAM_INT);
            $prepare->execute();

            return $prepare->fetchAll(\PDO::FETCH_ASSOC);

        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$offset, $pageSize], $startTime);
        }

    }

    public function queryUnusedUic($pageNum, $pageSize)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        $offset = ($pageNum - 1) * $pageSize;

        $sql = "select $this->queryColumns from $this->tableName where status>=1 limit :offset,:pageSize;";

        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":offset", $offset, PDO::PARAM_INT);
            $prepare->bindValue(":pageSize", $pageSize, PDO::PARAM_INT);
            $prepare->execute();

            return $prepare->fetchAll(\PDO::FETCH_ASSOC);

        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$offset, $pageSize], $startTime);
        }
    }

    public function queryUicByCode($code)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();

        $sql = "select $this->queryColumns from $this->tableName where code=:code;";

        try {
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":code", $code);
            $prepare->execute();

            return $prepare->fetch(\PDO::FETCH_ASSOC);

        } finally {
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$code], $startTime);
        }
    }
}
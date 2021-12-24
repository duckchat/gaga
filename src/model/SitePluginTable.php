<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 11:24 AM
 */

class SitePluginTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $tableName = "sitePlugin";
    private $columns = [
        "id",
        "pluginId",
        "name",
        "logo",
        "sort",
        "landingPageUrl",
        "landingPageWithProxy",
        "usageType",
        "loadingType",
        "permissionType",
        "authKey",
        "addTime",
        "management",
    ];

    private $queryColumns;

    public function init()
    {
        $this->logger = $this->ctx->getLogger();
        $this->queryColumns = implode(",", $this->columns);
    }


    public function insertMiniProgram($miniProgramProfile)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        $sql = 'insert into
                    sitePlugin(pluginId,
                     name, 
                     logo,
                     sort,
                     landingPageUrl,
                     landingPageWithProxy,
                     usageType,
                     loadingType,
                     permissionType,
                     management,
                     authKey,
                     addTime)
                values
                    (:pluginId,
                    :name,
                    :logo,
                    :sort, 
                    :landingPageUrl,
                    :landingPageWithProxy,
                    :usageType, 
                    :loadingType, 
                    :permissionType,
                    :management,
                    :authKey,
                    :addTime);
                ';

        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":pluginId", $miniProgramProfile["pluginId"], PDO::PARAM_INT);
            $prepare->bindValue(":name", $miniProgramProfile["name"]);
            $prepare->bindValue(":logo", $miniProgramProfile["logo"]);
            $prepare->bindValue(":sort", $miniProgramProfile["sort"], PDO::PARAM_INT);
            $prepare->bindValue(":landingPageUrl", $miniProgramProfile["landingPageUrl"]);
            $prepare->bindValue(":landingPageWithProxy", $miniProgramProfile["landingPageWithProxy"], PDO::PARAM_INT);
            $prepare->bindValue(":usageType", $miniProgramProfile["usageType"], PDO::PARAM_INT);
            $prepare->bindValue(":loadingType", $miniProgramProfile["loadingType"], PDO::PARAM_INT);
            $prepare->bindValue(":permissionType", $miniProgramProfile["permissionType"], PDO::PARAM_INT);
            $prepare->bindValue(":management", $miniProgramProfile["management"]);
            $prepare->bindValue(":authKey", $miniProgramProfile["authKey"]);
            $prepare->bindValue(":addTime", $miniProgramProfile["addTime"]);
            $flag = $prepare->execute();
            $result = $prepare->rowCount();

            if ($flag && $result > 0) {
                return true;
            }

        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }

        return false;
    }

    public function deletePlugin($pluginId)
    {
        $starTime = $this->getCurrentTimeMills();
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $sql = "delete from $this->tableName where pluginId=:pluginId;";

        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":pluginId", $pluginId);

            $flag = $prepare->execute();
            $result = $prepare->rowCount();
            if ($flag && $result > 0) {
                return true;
            }
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$pluginId], $starTime);
        }

        return false;
    }

    public function updateProfile($data, $where)
    {
        return $this->updateInfo($this->tableName, $where, $data, $this->columns);
    }

    public function getMaxPluginId()
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $sql = "select max(pluginId) as pluginId from $this->tableName";

        try {
            $prepare = $this->dbSlave->prepare($sql);
            $flag = $prepare->execute();
            $result = $prepare->fetch(\PDO::FETCH_ASSOC);

            if ($flag && $result) {
                return $result['pluginId'];
            }
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, " error_msg = " . $e);
        }
        return $numbers = range(10000, 10000000000);
    }

    /**
     * get plugin by pluginId(not pk id)
     *
     * @param $pluginId
     * @return array|mixed
     */
    public function getPluginById($pluginId)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        try {
            $startTime = microtime(true);
            $sql = "select $this->queryColumns from $this->tableName where pluginId=:pluginId;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":pluginId", $pluginId);
            $prepare->execute();
            $results = $prepare->fetch(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $pluginId, $startTime);
            return $results;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, " error_msg = " . $ex->getMessage());
            return [];
        }
    }

    public function getPluginsById($pluginId)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        try {
            $startTime = microtime(true);
            $sql = "select $this->queryColumns from $this->tableName where pluginId=:pluginId;";
            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":pluginId", $pluginId);
            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, $pluginId, $startTime);
            return $results;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, " error_msg = " . $ex->getMessage());
            return [];
        }
    }

    /**
     * get plugin list by usageType
     *
     * @param $usageType
     * @param $permissionTypes
     * @return array
     */
    public function getPluginList($usageType, $permissionTypes)
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);
        try {

            $permissionTypes = implode(",", $permissionTypes);

            if ($usageType === Zaly\Proto\Core\PluginUsageType::PluginUsageNone) {
                $sql = "select $this->queryColumns from $this->tableName  
                        where 1!=:usageType and permissionType in ($permissionTypes) 
                        order by sort ASC, id DESC";
            } else {
                $sql = "select $this->queryColumns from $this->tableName 
                        where usageType=:usageType and permissionType in ($permissionTypes) 
                        order by sort ASC, id DESC";
            }

            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":usageType", $usageType, PDO::PARAM_INT);
            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);

            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [$usageType, $permissionTypes], $startTime);

            return $results;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, " error_msg = " . $ex);
            return [];
        }

    }

    public function getAllPluginList()
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);
        try {
            $sql = "select 
                      distinct a.pluginId as pluginId,a.name as name,a.logo as logo,a.management as adminPageUrl 
                    from 
                      (select pluginId,name,logo,management from sitePlugin order by id ASC) as a;";

            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [], $startTime);

            return $results;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, " error_msg = " . $ex->getMessage());
            return [];
        }

    }


    //获取站点小程序列表，忽略usageType其他的数据，这里不存在重复的pluginId
    public function getNonRepeatedPluginList()
    {
        $tag = __CLASS__ . "_" . __FUNCTION__;
        $startTime = microtime(true);
        try {
            $sql = "select a.id, a.pluginId,a.name,a.logo,a.management as adminPageUrl,a.sort as sort
                    from sitePlugin as a 
                    inner join (select min(id) as id, pluginId from sitePlugin group by pluginId) as b 
                    where a.id=b.id
                    order by a.id ASC;";

            $prepare = $this->dbSlave->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->execute();
            $results = $prepare->fetchAll(\PDO::FETCH_ASSOC);
            $this->ctx->Wpf_Logger->writeSqlLog($tag, $sql, [], $startTime);

            return $results;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, " error_msg = " . $ex->getMessage());
            return [];
        }

    }
}
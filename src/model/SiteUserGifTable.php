<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 27/09/2018
 * Time: 2:36 PM
 */

class SiteUserGifTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $table = "siteUserGif";
    private $columns = [
        "id",
        "userId",
        "gifId",
        "addTime"
    ];
    private $queryColumns;

    private $siteGifTable = 'siteGif';
    private $siteGifColumns = [
        "id",
        "gifId",
        "gifUrl",
        "width",
        "height",
        "addTime"
    ];
    private $querySiteGifColumns;


    public function init()
    {
        $this->queryColumns = implode(",", $this->columns);
        $this->querySiteGifColumns = implode(",", $this->siteGifColumns);
    }

    public function addGif($siteGifData, $siteUserGifData)
    {
        try{
            $this->dbSlave->beginTransaction();
            $this->insertData($this->table, $siteUserGifData, $this->columns);
            $this->insertData($this->siteGifTable, $siteGifData, $this->siteGifColumns);
            $this->dbSlave->commit();
        }catch (Exception $ex) {
            $this->dbSlave->rollBack();
            throw  $ex;
        }
    }

    public function addUserGif($siteUserGifData)
    {
        return $this->insertData($this->table, $siteUserGifData, $this->columns);
    }
    public function delGif($userId,$gifId)
    {
        $sql = "delete from $this->table where userId=:userId and gifId=:gifId";
        $prepare = $this->dbSlave->prepare($sql);
        $prepare->bindValue(":userId", $userId, PDO::PARAM_STR);
        $prepare->bindValue(":gifId", $gifId, PDO::PARAM_STR);
        $result = $prepare->execute();
        if($result) {
            return true;
        }
        throw new Exception("删除失败");
    }

    public function getGifByUserId($userId, $offset, $limit)
    {
        $sql = "select gifId, userId from $this->table where (userId=:userId  or userId='duckchat') limit $offset, $limit";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError("site.user.gif", $prepare);
        $prepare->bindValue(":userId", $userId, PDO::PARAM_STR);
        $prepare->execute();
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getGifByGifId($gifId)
    {
        $sql = "select gifId, gifUrl, width, height from $this->siteGifTable where gifId=:gifId";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError("site.user.gif", $prepare);
        $prepare->bindValue(":gifId", $gifId, PDO::PARAM_STR);
        $prepare->execute();
        $result = $prepare->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getGifInfo($userId, $gifId)
    {
        $sql = "select gif.gifId, gifUrl, width, height, userId from siteGif  as gif left join (select gifId, userId from siteUserGif 
                where  (userId=:userId or userId='duckchat')) as userGif on gif.gifId = userGif.gifId where gif.gifId=:gifId;";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError("site.user.gif", $prepare);
        $prepare->bindValue(":gifId", $gifId, PDO::PARAM_STR);
        $prepare->bindValue(":userId", $userId, PDO::PARAM_STR);
        $prepare->execute();
        $result = $prepare->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getGifListFromSiteGif($offset, $limit)
    {
        $sql = "select $this->querySiteGifColumns from $this->siteGifTable order by id desc limit $offset, $limit";
        $prepare = $this->dbSlave->prepare($sql);
        $this->handlePrepareError("site.gif", $prepare);
        $prepare->execute();
        $results = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public function deleteGif($gifIds)
    {
        try{
            $gifIdsStr = implode("','",  $gifIds);

            $this->dbSlave->beginTransaction();
            $sql = "delete from $this->table where gifId in ('$gifIdsStr')";
            $prepare = $this->dbSlave->prepare($sql);
            $result = $prepare->execute();

            $sql = "delete from $this->siteGifTable where gifId in ('$gifIdsStr')";
            $prepare = $this->dbSlave->prepare($sql);
            $resultSiteGif = $prepare->execute();

            if($result && $resultSiteGif) {
                $this->dbSlave->commit();
                return true;
            }
            throw new Exception("删除失败");
        }catch (Exception $ex) {
            $this->dbSlave->rollBack();
            throw new Exception("删除失败");
        }
    }
}


<?php
/**
 * site third party login table
 * User: anguoyue
 * Date: 2018/11/7
 * Time: 8:23 PM
 */

class SiteThirdPartyLoginTable extends BaseTable
{
    /**
     * @var Wpf_Logger
     */
    private $logger;
    private $table = "siteThirdPartyLogin";

    private $columns = [
        "id",
        "userId",
        "loginKey",//source third party key ,sourceKey
        "loginUserId",  //source third party userId,sourceUserId
        "loginTime",//login time
    ];

    private $queryColumns;

    public function init()
    {
        $this->logger = $this->ctx->getLogger();
        $this->queryColumns = implode(",", $this->columns);
    }


    public function saveAccountInfo($data)
    {
        $data['loginTime'] = $this->getCurrentTimeMills();
        return $this->insertData($this->table, $data, $this->columns);
    }

    public function getAccountInfo($loginKey, $loginUserId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $startTime = $this->getCurrentTimeMills();
        $sql = "select $this->queryColumns from $this->table where loginKey=:loginKey and loginUserId=:loginUserId;";

        try {
            $prepare = $this->db->prepare($sql);
            $this->handlePrepareError($tag, $prepare);
            $prepare->bindValue(":loginKey", $loginKey);
            $prepare->bindValue(":loginUserId", $loginUserId);
            $prepare->execute();
            return $prepare->fetch(PDO::FETCH_ASSOC);
        } finally {
            $this->logger->writeSqlLog($tag, $sql, [$loginKey, $loginUserId], $startTime);
        }
    }

}
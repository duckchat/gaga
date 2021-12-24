<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_Group_SearchController extends Manage_CommonController
{

    public function doRequest()
    {
        $result = [
            "errCode" => "error",
        ];

        $value = $_POST['searchValue'];

        if (!isset($value)) {
            throw new Exception("search empty value");
        }

        $groups = $this->searchGroupByName($value);

        if ($groups) {
            $result["errCode"] = "success";
            $result["groups"] = $groups;
        }

        echo json_encode($result);
        return;

    }

    private function searchGroupByName($groupName)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            $pinyin = new \Overtrue\Pinyin\Pinyin();
            $nameInLatin = $pinyin->permalink($groupName, "");
            $groups = $this->ctx->SiteGroupTable->getGroupProfileByNameInLatin($nameInLatin);

            return $groups;
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
    }

}
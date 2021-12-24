<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_GroupController extends Manage_CommonController
{
    private $pageSize = 40;

    public function doRequest()
    {
        $params = ["lang" => $this->language];

        $method = $_SERVER["REQUEST_METHOD"];

        if ($method == "POST") {

            //get user list by page
            $offset = $_POST['pageNum'];
            $length = $_POST['pageSize'];

            if (!$length) {
                $length = $this->pageSize;
            }

            $offset = ($offset - 1) * $length;

            $groupList = $this->getGroupListByOffset($offset, $length);

            if (!empty($groupList)) {
                $params['loading'] = count($groupList) == $length ? true : false;
                $params['data'] = $groupList;
            }

            echo json_encode($params);
        } else {

            $offset = 0;
            $length = $this->pageSize;

            // totalGroupCount
            $params['totalGroupCount'] = $this->getTotalGroupCount();

            $groupList = $this->getGroupListByOffset($offset, $length);

            if ($groupList) {
                $groupProfiles = [];
                foreach ($groupList as $group) {

                    $groupProfiles[] = [
                        'groupId' => $group['groupId'],
                        'name' => htmlspecialchars($group['name']),
                    ];

                }
                $params['groupList'] = $groupProfiles;
            }

            echo $this->display("manage_group_indexList", $params);
        }
        return;
    }

    private function getTotalGroupCount()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteGroupTable->getSiteGroupCount();
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
        return 0;
    }

    private function getGroupListByOffset($offset, $length)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteGroupTable->getSiteGroupListByOffset($offset, $length);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->info($tag, $e);
        }
        return [];
    }
}
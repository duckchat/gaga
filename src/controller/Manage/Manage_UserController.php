<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_UserController extends Manage_CommonController
{
    private $pageSize = 40;

    public function doRequest()
    {
        $params = ["lang" => $this->language];

        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST') {

            //get user list by page
            $pageNum = $_POST['pageNum'];
            $length = $_POST['pageSize'];

            if (empty($length)) {
                $length = $this->pageSize;
            }

            $offset = ($pageNum - 1) * $length;

            $userList = $this->getUserListByOffset($offset, $length);

            if (!empty($userList)) {
                $params['loading'] = count($userList) == $length ? true : false;
                $params['data'] = $userList;
            }

            echo json_encode($params);

            return;

        } else {
            //get user list by page
            $offset = $_GET['offset'];
            $length = $_GET['length'];

            if (empty($offset)) {
                $offset = 0;
            }

            if (empty($length)) {
                $length = $this->pageSize;
            }


            $userList = $this->getUserListByOffset($offset, $length);

            if ($userList) {
                $userProfiles = [];
                foreach ($userList as $user) {
                    $userProfiles[] = [
                        'userId' => $user['userId'],
                        'nickname' => htmlspecialchars($user['nickname']),
                        'loginName' => htmlspecialchars($user['loginName']),
                    ];

                }
                $params['userList'] = $userProfiles;
            }

            $params['totalUserCount'] = $this->getTotalUsers();

            echo $this->display("manage_user_indexList", $params);
        }
        return;
    }

    private function getUserListByOffset($offset, $length)
    {
        return $this->ctx->SiteUserTable->getSiteUserListByOffset($offset, $length);
    }

    private function getTotalUsers()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteUserTable->getSiteUserCount();
        } catch (Exception $e) {
            $this->logger->error($tag, $e);
        }
    }

}
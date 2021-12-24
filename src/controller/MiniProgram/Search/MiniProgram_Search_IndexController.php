<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 12/11/2018
 * Time: 2:06 PM
 */

class MiniProgram_Search_IndexController extends MiniProgram_BaseController
{

    private $miniProgramId   = "";
    private $defaultPageSize = 30;
    private $title = "核武搜索";
    private  $defaultLang = \Zaly\Proto\Core\UserClientLangType::UserClientLangZH;

    public function getMiniProgramId()
    {
        $config = require(dirname(__FILE__)."/recommend.php");
        $this->miniProgramId = isset($config['miniProgramId']) ? $config['miniProgramId'] : $this->miniProgramId;
        return $this->miniProgramId;
    }

    public function requestException($ex)
    {
        $this->showPermissionPage();
    }

    public function preRequest()
    {
    }

    public function doRequest()
    {
        header('Access-Control-Allow-Origin: *');
        $method = $_SERVER['REQUEST_METHOD'];
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $params['title'] = $this->title;
        $params['loginName'] = $this->loginName;
        $for = isset($_GET['for']) ? $_GET['for'] : "index";
        $nickname = isset($_GET['key']) ?  $_GET['key'] : "";
        $params['key']   = $nickname;
        $params['token'] = $this->userId;

        if($method == "post") {
            $page = isset($_POST['page']) ? $_POST['page']:1;
            switch ($for) {
                case "user":
                    $userList = $this->ctx->Manual_User->search($this->userId, $nickname, $page, $this->defaultPageSize);
                    echo json_encode(["data" => $userList]);
                    break;
                case "group":
                    $groupList = $this->ctx->Manual_Group->search($nickname,  $page, $this->defaultPageSize);
                    $groupList = $this->getGroupProfile($groupList);
                    echo json_encode(["data" => $groupList]);
                    break;
                case "joinGroup":
                    try{
                        $groupId = $_POST['groupId'];
                        $userIds = [$this->userId];
                        $joinNotice = "$this->loginName 通过核武搜索进入本群";
                        $this->ctx->Manual_Group->joinGroup($groupId, $userIds, $joinNotice);
                        $results = ["errorCode" => "success", "errorInfo" => ""];
                        echo json_encode($results);
                    }catch (ZalyException $ex) {
                        $results = ["errorCode" => "error", "errorInfo" => $ex->getErrInfo($this->defaultLang)];
                        echo json_encode($results);
                    }
                    break;
            }
            return;
        }else {
            switch ($for) {
                case "search":
                    $userList = $this->ctx->Manual_User->search($this->userId, $nickname, 1, 3);
                    $params['users'] = $userList;

                    $groupList = $this->ctx->Manual_Group->search($nickname, 1, 3);
                    $groupList = $this->getGroupProfile($groupList);
                    $params['groups'] = $groupList;

                    echo $this->display("miniProgram_search_searchList", $params);
                    break;
                case "user":
                    $userList = $this->ctx->Manual_User->search($this->userId, $nickname, 1, $this->defaultPageSize);
                    $params['users'] = $userList;
                    echo $this->display("miniProgram_search_userList", $params);
                    break;
                case "group":
                    $groupList = $this->ctx->Manual_Group->search($nickname, 1, $this->defaultPageSize);
                    $groupList = $this->getGroupProfile($groupList);
                    $params['groups'] = $groupList;
                    echo $this->display("miniProgram_search_groupList", $params);
                    break;
                default:
                    $config = require(dirname(__FILE__)."/recommend.php");
                    $params['groups'] =  [];
                    if(isset($config['groupIds'])) {
                        $groupIds  = $config['groupIds'];
                        $groupList = $this->getGroupListByGroupId($groupIds);
                        $params['groups'] = $groupList;
                    }
                    $params['about_us_desc']    = $config['about_us_desc'];
                    $params['about_us_title']   = $config['about_us_title'];
                    $params['about_us_contact'] = $config['about_us_contact'];

                    echo $this->display("miniProgram_search_indexList", $params);
            }
        }
    }

    protected function getGroupProfile($groupLists)
    {
        $tag = __CLASS__.'->'.__FUNCTION__;
        try{
            $ownerIds = [];
            $groupIds = [];
            foreach ($groupLists as $key => $group) {
                $ownerIds[] = $group['owner'];
                $groupIds[] = $group['groupId'];
            }

            $ownerIds = array_unique($ownerIds);

            $list = $this->ctx->Manual_User->getProfiles($this->userId, $ownerIds);

            $userList = array_column($list, "nickname", "userId");

            $list = $this->ctx->Manual_Group->getProfiles($this->userId, $groupIds);
            $memberInGroupList = array_column($list, "isMember", "groupId");

            foreach ($groupLists as $key => $group) {
                $group['ownerName'] = $userList[$group['owner']];
                $group['isMember']  =  $memberInGroupList[$group['groupId']];
                $groupLists[$key] = $group;
            }

            return $groupLists;
        }catch (Exception $ex) {
            $this->ctx->getLogger()->error($tag, $ex);
        }
        return $groupLists;

    }

    protected function getGroupListByGroupId($groupIds)
    {
        $tag = __CLASS__.'->'.__FUNCTION__;

        try{
            $groupLists = $this->ctx->Manual_Group->getProfiles($this->userId, $groupIds);

            $ownerIds = [];

            foreach ($groupLists as $key => $group) {
                $ownerIds[] = $group['owner'];
            }

            $ownerIds = array_unique($ownerIds);

            $list = $this->ctx->Manual_User->getProfiles($this->userId, $ownerIds);

            $userList = array_column($list, "nickname", "userId");
            foreach ($groupLists as $key => $group) {
                $group['ownerName'] = $userList[$group['owner']];
                $groupLists[$key] = $group;
            }
            return $groupLists;
        }catch (Exception $ex) {
            $this->ctx->getLogger()->error($tag, $ex);
        }
        return [];
    }

}
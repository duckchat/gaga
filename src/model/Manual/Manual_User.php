<?php
/**
 * 内部接口开发手册，群组相关内部接口
 *  1.查找用户
 *  2.批量获取群组资料
 *  3.加群组
 * Author: SAM<an.guoyue254@gmai.com>
 * Date: 2018/11/13
 * Time: 11:00 AM
 */

interface User {
    /**
     * @param $currentUserId
     * @param $search
     * @param int $pageNum
     * @param int $pageSize
     * @return mixed
     */
    public function search($currentUserId, $search, $pageNum = 1, $pageSize = 200);

    /**
     * @param $currentUserId
     * @param array $userIds
     * @return mixed
     */
    public function getProfiles($currentUserId, array $userIds);
}

class Manual_User extends Manual_Common implements User
{

    /**
     * 更具$search查找用户
     * @param $search   查找的内容
     * @param int $pageNum 第几页，从1开始
     * @param int $pageSize 每页面数量
     */
    public function search($currentUserId, $search, $pageNum = 1, $pageSize = 200)
    {
        $results = $this->ctx->SearchSiteTable->getSiteUserListWithRelationByNickname($currentUserId, $search, $pageNum, $pageSize);
        if($results) {
            foreach ($results as $key => $user) {

                if(isset($user['friendId'])) {
                    $user['isFollow'] = true;
                } else {
                    $user['isFollow'] = false;
                }
                $results[$key] = $user;
            }
        } else {
            $results = [];
        }
        return $results;
    }

    /**
     * @param $currentUserId
     * @param array $userIds 需要获取的用户ID数组
     * @return array 返回用户资料的数组 [$user1,$user2,$user3],返回值中附带$currentUserId与每个用户之间的关系
     */
    public function getProfiles($currentUserId, array $userIds)
    {
        $results = $this->ctx->SearchSiteTable->getSiteUserListWithRelationByUserId($currentUserId, $userIds);
        if($results) {
            foreach ($results as $key => $user) {
                if(isset($user['friendId'])) {
                    $user['isFollow'] = true;
                } else {
                    $user['isFollow'] = false;
                }
                $results[$key] = $user;
            }
        } else {
            $results = [];
        }
        return $results;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 23/07/2018
 * Time: 4:20 PM
 */

class Api_Friend_ListController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiFriendListRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiFriendListResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiFriendListRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $userId = $this->userId;
            $offset = $request->getOffset();
            $count = $request->getCount();

            $userFriends = $this->getUserFriends($userId, $offset, $count);

            $friendCount = $this->getUserFriendCount($userId);

            $response = new Zaly\Proto\Site\ApiFriendListResponse();
            $response->setFriends($userFriends);
            $response->setTotalCount($friendCount);

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);

        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    /**
     * @param $userId
     * @param $offset
     * @param $count
     * @return array
     */
    protected function getUserFriends($userId, $offset, $count)
    {
        $userFriendsList = [];
        $friendsFromDB = $this->ctx->SiteUserFriendTable->queryUserFriendByPage($userId, $offset, $count);

        if (!empty($friendsFromDB)) {

            foreach ($friendsFromDB as $friend) {

                $userFriends = new \Zaly\Proto\Core\FriendUserProfile();

                $profile = new Zaly\Proto\Core\PublicUserProfile();

                $profile->setUserId($friend['userId']);

                $aliasName = $friend['aliasName'];

                if (!empty($aliasName)) {
                    $profile->setNickname($aliasName);
                    $profile->setNicknameInLatin($friend['aliasNameInLatin']);
                } else {
                    $profile->setNickname($friend['nickname']);
                    $profile->setNicknameInLatin($friend['nicknameInLatin']);
                }

                $profile->setAvatar($friend['avatar']);
                $profile->setLoginName($friend['loginName']);
                $profile->setRealNickname($friend['nickname']);

                $userFriends->setProfile($profile);
                $userFriends->setMute($friend['mute']);//1(true) mute
                $userFriends->setRelation(\Zaly\Proto\Core\FriendRelationType::FriendRelationFollow);

                $userFriendsList[] = $userFriends;
            }

        }

        return $userFriendsList;
    }

    protected function getUserFriendCount($userId)
    {
        $totalCount = $this->ctx->SiteUserFriendTable->queryUserFriendCount($userId);

        return empty($totalCount) ? 0 : $totalCount;
    }

}
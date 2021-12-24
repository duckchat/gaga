<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 01/08/2018
 * Time: 6:12 PM
 */

class Api_Friend_ApplyListController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiFriendApplyListRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiFriendApplyListResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiFriendApplyListRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $offset = $request->getOffset() ? $request->getOffset() : 0;
            $count = $request->getCount() && $request->getCount() < $this->defaultPageSize ? $request->getCount() : $this->defaultPageSize;
            $list = $this->getApplyList($offset, $count);
            $totalCount = $this->getApplyListCount();
            $response = $this->getApiFriendApplyListResponse($list, $totalCount);
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->setRpcError("error.alert", $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    private function getApplyList($offset, $count)
    {
        $list = $this->ctx->SiteFriendApplyTable->getApplyList($this->userId, $offset, $count);
        return $list;
    }

    private function getApplyListCount()
    {
        return $this->ctx->SiteFriendApplyTable->getApplyListCount($this->userId);
    }


    private function getApiFriendApplyListResponse($list, $count)
    {
        $response = new \Zaly\Proto\Site\ApiFriendApplyListResponse();
        $applyUserProfileList = [];

        if ($list) {
            foreach ($list as $user) {
                $publicUser = $this->getPublicUserProfile($user);
                $applyUserProfile = new \Zaly\Proto\Core\ApplyUserProfile();
                $applyUserProfile->setPublic($publicUser);
                $applyUserProfile->setGreetings($user['greetings']);
                $applyUserProfileList[] = $applyUserProfile;
            }
        }

        $response->setList($applyUserProfileList);
        $response->setTotalCount($count);
        return $response;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 21/07/2018
 * Time: 10:17 AM
 */


class Api_Group_ListController extends Api_Group_BaseController
{
    private $classNameForRequest   = '\Zaly\Proto\Site\ApiGroupListRequest';
    private $classNameForResponse  = '\Zaly\Proto\Site\ApiGroupListResponse';
    public $userId;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiGroupListRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $offset     = $request->getOffset() ? $request->getOffset() : 0;
            $pageSize   = $request->getCount() > $this->defaultPageSize || !$request->getCount() ? $this->defaultPageSize : $request->getCount();
            $groupLists = $this->getGroupList($offset, $pageSize);
            $groupCount = $this->getGroupCount();
            $response   =  $this->getApiGroupListResponse($groupLists, $groupCount);
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
    }

    public function getApiGroupListResponse($groupLists, $groupCount)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try{
            $response = new \Zaly\Proto\Site\ApiGroupListResponse();
            $list = [];
            $listIsMute = [];

            foreach($groupLists as $key => $group) {
                $groupProfile = $this->getPublicGroupProfile($group);
                $list[] = $groupProfile;
            }

            $response->setListIsMute($listIsMute);
            $response->setList($list);
            $response->setTotalCount($groupCount);

            return $response;
        }catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex->getMessage());
        }
    }
}
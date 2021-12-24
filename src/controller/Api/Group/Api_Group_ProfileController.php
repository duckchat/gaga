<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 24/07/2018
 * Time: 1:52 PM
 */

class Api_Group_ProfileController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupProfileRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupProfileResponse';
    public $userId;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiGroupProfileRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        ///处理request，
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $groupId = $request->getGroupId();

            if (!$groupId) {
                $errorCode = $this->zalyError->errorGroupProfile;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                throw new Exception($errorInfo);
            }
            $isGroupExists = $this->getGroupInfo($groupId);
            if ($isGroupExists === false) {
                return;
            }

            //get group profile
            $groupProfile = $this->getGroupProfile($groupId);

            $response = $this->buildApiGroupProfileResponse($groupProfile);

            $this->returnSuccessRPC($response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg=" . $ex);
            $this->returnErrorRPC(new $this->classNameForResponse(), $ex);
        }
    }

    private function buildApiGroupProfileResponse($group)
    {
        if (!$group) {
            $response = new \Zaly\Proto\Site\ApiGroupProfileResponse();
            return $response;
        }

        $canAddFriend = $group["canAddFriend"];

        //get site config
        $config = $this->siteConfig;
        $enableAddFriendInGroup = $config[SiteConfig::SITE_ENABLE_ADD_FRIEND_IN_GROUP];
        $group["canAddFriend"] = $canAddFriend && $enableAddFriendInGroup;

        $memberType = !$group['memberType'] ? \Zaly\Proto\Core\GroupMemberType::GroupMemberGuest : $group['memberType'];
        $groupProfile = $this->getPublicGroupProfile($group);
        $isMute = isset($group['isMute']) && $group['isMute'] == 1 ? 1 : 0;
        $response = new \Zaly\Proto\Site\ApiGroupProfileResponse();
        $response->setProfile($groupProfile);
        $response->setIsMute($isMute);
        $response->setMemberType($memberType);
        return $response;
    }

}
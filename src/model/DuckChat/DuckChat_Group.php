<?php
/**
 * 提供duckchat外部接口同时，也提供内部接口
 * User: anguoyue
 * Date: 2018/11/9
 * Time: 3:39 PM
 */

class DuckChat_Group
{

    private $ctx;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
    }

    /**
     * @param $pluginId
     * @param \Zaly\Proto\Plugin\DuckChatGroupCheckMemberRequest $request
     * @return \Zaly\Proto\Plugin\DuckChatGroupCheckMemberResponse
     * @throws Exception
     */
    public function checkMember($pluginId, $request)
    {
        $groupId = $request->getGroupId();
        $userId = $request->getUserId();
        $memberType = $this->getMemberType($groupId, $userId);
        $response = $this->getGroupCheckMemberResponse($memberType);
        return $response;
    }

    private function getMemberType($groupId, $userId)
    {
        $groupUserInfo = $this->ctx->SiteGroupUserTable->getGroupUser($groupId, $userId);
        if ($groupUserInfo == false) {
            throw new Exception("none user");
        }
        return $groupUserInfo['memberType'];
    }

    private function getGroupCheckMemberResponse($memberType)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;

        try {
            $response = new \Zaly\Proto\Plugin\DuckChatGroupCheckMemberResponse();
            $response->setMemberType($memberType);
            return $response;
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg ==" . $ex->getMessage());

        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 10/08/2018
 * Time: 4:06 PM
 */

class Message_News
{

    private $ctx;
    private $newsAction = "im.stc.news";

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
    }

    /**
     * tell client im.stc.news
     *
     * @param $isGroup
     * @param $toId
     * @throws Exception
     */
    public function tellClientNews($isGroup, $toId)
    {
        try {

            if (!$this->enablePersistentForIM()) {
                return;
            }

            if ($isGroup) {
                $groupUserIdList = $this->ctx->SiteGroupUserTable->getGroupAllMembersId($toId);
                if (!empty($groupUserIdList)) {
                    foreach ($groupUserIdList as $groupMember) {
                        $userId = $groupMember["userId"];
                        $this->ctx->Gateway_Client->sendMessageByUserId($userId, $this->newsAction, new Zaly\Proto\Client\ImStcNewsRequest());
                    }
                }
            } else {
                $this->ctx->Gateway_Client->sendMessageByUserId($toId, $this->newsAction, new Zaly\Proto\Client\ImStcNewsRequest());
            }
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($this->newsAction, $e);
        }
    }

    public function tellClientNewsBySession($sessionId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {

            if (!$this->enablePersistentForIM()) {
                return;
            }

            $this->ctx->Gateway_Client->sendMessageBySessionId($sessionId, $this->newsAction, new Zaly\Proto\Client\ImStcNewsRequest());
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
    }

    private function enablePersistentForIM()
    {
        $zalyAddress = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_ZALY_ADDRESS);
        $wsAddress = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_WS_ADDRESS);

        return !empty($zalyAddress) || !empty($wsAddress);
    }

}
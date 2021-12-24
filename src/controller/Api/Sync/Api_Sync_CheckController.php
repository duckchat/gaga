<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 03/08/2018
 * Time: 9:02 PM
 */

class Api_Sync_CheckController extends BaseController
{

    /**
     * @var string
     */
    protected $action = "api.sync.check";
    private $classNameForRequest = '\Zaly\Proto\Site\ApiSyncCheckRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiSyncCheckResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiSyncCheckRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $userId = $this->userId;
        $request->getLocalVersion();
        $type = $request->getType();

        $response = new \Zaly\Proto\Site\ApiSyncCheckResponse();

        try {

            switch ($type) {
                case \Zaly\Proto\Site\ApiSyncType::ApiSyncFriendList:
                    $friendVerion = $this->getUserFriendVersion($userId);

                    $response->setVersion($friendVerion);
                    $this->setRpcError($this->defaultErrorCode, "");
                    break;
                case \Zaly\Proto\Site\ApiSyncType::ApiSyncInvalid:
                    $this->setRpcError("error.notExists", "");
                    break;

            }

        } catch (Exception $e) {
            $this->setRpcError("error.alert", "");
            $this->ctx->wpf_Logger->error($tag, $e);
        }

        $this->rpcReturn($this->action, $response);
    }

    /**
     * @param $userId
     * @return int
     */
    private function getUserFriendVersion($userId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {
            return $this->ctx->SiteUserTable->getUserFriendVersion($userId);
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, $e);
        }
        return 0;
    }

}
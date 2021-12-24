<?php

use Zaly\Proto\Core\TransportData;
use Zaly\Proto\Site\ImCtsAuthRequest;

/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 23/07/2018
 * Time: 11:25 AM
 */
class Im_Cts_AuthController extends Im_BaseController
{
    private $requestAction = "im.cts.auth";
    public $classNameForCtsAuthRequest = '\Zaly\Proto\Site\ImCtsAuthRequest';
    public $classNameForCtsAuthResponse = '\Zaly\Proto\Site\ImCtsAuthResponse';


    //检测proto类时使用
    public function rpcRequestClassName()
    {
        return $this->classNameForCtsAuthRequest;
    }

    /**
     * 当前不需要做其他业务逻辑
     * @param \Zaly\Proto\Site\ImCtsAuthRequest $request
     * @param Zaly\Proto\Core\TransportData $transportData
     * @return mixed|void
     */
    public function doRequest(\Google\Protobuf\Internal\Message $request, TransportData $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $result = true;

            $gatewayHost = !empty($_GET["gw-host"]) ? $_GET["gw-host"] : $_SERVER["REMOTE_ADDR"];
            $gatewayPort = !empty($_GET["gw-port"]) ? $_GET["gw-port"] : "";
            $gatewaySocketId = !empty($_GET['gw-socket-id']) ? $_GET['gw-socket-id'] : "";

            $sessionId = $this->getSessionId($transportData);
            if (!empty($gatewayPort) && !empty($gatewaySocketId)) {
                $gatewayURL = "{$gatewayHost}:{$gatewayPort}";
                $where = ["sessionId" => $sessionId];
                /**
                 * UserClientMobileApp = 1;
                 * UserClientWeb = 2;(bodyFormatType = json)
                 */
                $clientSideType = 1;
                if ("json" == $this->bodyFormatType) {
                    $clientSideType = 2;
                }

                $this->ctx->SiteSessionTable->updateSessionInfo($where, array(
                    "clientSideType" => $clientSideType,
                    "gatewayURL" => $gatewayURL,
                    "gatewaySocketId" => $gatewaySocketId,
                    "timeActive" => $this->ctx->ZalyHelper->getMsectime(),
                ));
            }

            if ($result) {
                $this->setRpcError($this->defaultErrorCode, "");
                $this->keepSocket();//keep socket
                $this->notifySyncNotice($sessionId);
            } else {
                $errorCode = $this->zalyError->errorSession;
                $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                $this->setRpcError($errorCode, $errorInfo);
                $this->notKeepSocket();
            }

            $this->rpcReturn($transportData->getAction(), new $this->classNameForCtsAuthResponse());
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg ==" . $ex->getMessage());
            $this->returnErrorRPC(new $this->classNameForCtsAuthResponse(), $ex);
        }

        return;
    }

    private function notifySyncNotice($sessionId)
    {
        $this->ctx->Message_News->tellClientNewsBySession($sessionId);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: sssl
 * Date: 2018/7/24
 * Time: 1:58 PM
 */


use \Google\Protobuf\Internal\Message;
use \Zaly\Proto\Gateway\GwSocketWriteRequest;

class Gateway_Client
{

    /**
     * @var BaseCtx
     */
    private $ctx;

    public function __construct(BaseCtx $ctx)
    {
        $this->ctx = $ctx;
    }

    public function closeSocketByUserId($userId)
    {
        $sessionInfo = $this->ctx->SiteSessionTable->getSessionInfoByUserId($userId);
        $this->closeSocket($sessionInfo);
    }


    public function closeSocketBySessionId($sessionId)
    {
        $sessionInfo = $this->ctx->SiteSessionTable->getSessionInfoBySessionId($sessionId);
        $this->closeSocket($sessionInfo);
    }


    public function closeSocket($sessionInfo)
    {
        $gatewayURL = $sessionInfo["gatewayURL"];
        $gatewaySocketId = $sessionInfo["gatewaySocketId"];

        if ($gatewayURL == "" || $gatewaySocketId == "") {
            return;
        }

        $requestProto = new \Zaly\Proto\Gateway\GwSocketCloseRequest();
        $requestProto->setSocketIds(array($gatewaySocketId));

        $gatewayURL = "http://{$gatewayURL}/gw/socket/close";
        $this->curl($gatewayURL, $requestProto->serializeToString());
        return;
    }

    public function sendMessageByUserId($userId, $action, \Google\Protobuf\Internal\Message $request)
    {
        $list = $this->ctx->SiteSessionTable->getAllSessionInfoByUserId($userId);
        $this->sendMessageToMultiSessions($list, $action, $request);
    }

    public function sendMessageByUserIds(array $userIds, $action, \Google\Protobuf\Internal\Message $request)
    {
        $list = array();
        foreach ($userIds as $userId) {
            $users = $this->ctx->SiteSessionTable->getAllSessionInfoByUserId($userId);
            $list = array_merge($list, $users);
        }
        $this->sendMessageToMultiSessions($list, $action, $request);
    }

    public function sendMessageBySessionId($sessionId, $action, \Google\Protobuf\Internal\Message $request)
    {
        $sessionInfo = $this->ctx->SiteSessionTable->getSessionInfoBySessionId($sessionId);
        $this->sendMessageToMultiSessions([$sessionInfo], $action, $request);
    }

    public function sendMessageToMultiSessions(array $sessionInfos, $action, \Google\Protobuf\Internal\Message $request)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $packages = array();
        foreach ($sessionInfos as $sessionInfo) {
            $gatewayURL = $sessionInfo["gatewayURL"];
            $gatewaySocketId = $sessionInfo["gatewaySocketId"];
            if ($gatewayURL == "" || $gatewaySocketId == "") {
                continue;
            }

            $clientSiteType = $sessionInfo["clientSideType"];
            $bodyFormat = "json";
            if ($clientSiteType == \Zaly\Proto\Core\UserClientType::UserClientMobileApp) {
                $bodyFormat = "pb";
            }
            $packages[$gatewayURL][$bodyFormat][] = $gatewaySocketId;
        }

        try {

            foreach ($packages as $url => $list) {
                $gwRequestPackages = array();
                foreach ($list as $bodyFormat => $socketIds) {
                    $tmpPackage = new \Zaly\Proto\Gateway\GwSocketWritePackage();
                    $tmpPackage->setContent($this->buildPackage($bodyFormat, $action, $request));
                    $tmpPackage->setSocketIds($socketIds);
                    $gwRequestPackages[] = $tmpPackage;
                }
                $gwRequest = new GwSocketWriteRequest();
                $gwRequest->setPackages($gwRequestPackages);
                $gatewayURL = "http://{$url}/gw/socket/write";
                $response = $this->curl($gatewayURL, $gwRequest->serializeToString());
                // for debug.
            }

        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, $ex->getMessage());
        }
    }

    private function buildPackage($bodyFormat, $action, \Google\Protobuf\Internal\Message $request)
    {
        $any = new \Google\Protobuf\Any();
        $any->pack($request);

        $transportData = new \Zaly\Proto\Core\TransportData();
        $transportData->setAction($action);
        $transportData->setBody($any);
        $transportData->setPackageId(mt_rand(0, 1000000000));

        $dataForWrite = "";
        switch ($bodyFormat) {
            case "json":
                $dataForWrite = $transportData->serializeToJsonString();
                $dataForWrite = trim($dataForWrite);
                break;
            case "pb":
                $dataForWrite = $transportData->serializeToString();
                break;
            case "base64pb":
                $dataForWrite = $transportData->serializeToString();
                $dataForWrite = base64_encode($dataForWrite);
                break;
            default:
                return "";
        }

        return $dataForWrite;
    }


//    public function sendMessage($sessionInfo, $action, \Google\Protobuf\Internal\Message $request)
//    {
//        $tag = __CLASS__ . "-" . __FUNCTION__;
//        try {
//            $gatewayURL = $sessionInfo["gatewayURL"];
//            $gatewaySocketId = $sessionInfo["gatewaySocketId"];
//
//
//            $clientSiteType = $sessionInfo["clientSideType"];
//
//            $bodyFormat = "json";
//            if ($clientSiteType == \Zaly\Proto\Core\UserClientType::UserClientMobileApp) {
//                $bodyFormat = "pb";
//            }
//
//
//            if ($gatewayURL == "" || $gatewaySocketId == "") {
//                return;
//            }
//            $any = new \Google\Protobuf\Any();
//            $any->pack($request);
//
//            $transportData = new \Zaly\Proto\Core\TransportData();
//            $transportData->setAction($action);
//            $transportData->setBody($any);
//            $transportData->setPackageId(mt_rand(0, 1000000000));
//
//            // TODO: Fix header
//            //$transportData->setHeader();
//
//            $dataForWrite = "";
//            switch ($bodyFormat) {
//                case "json":
//                    $dataForWrite = $transportData->serializeToJsonString();
//                    $dataForWrite = trim($dataForWrite);
//                    break;
//                case "pb":
//                    $dataForWrite = $transportData->serializeToString();
//                    break;
//                case "base64pb":
//                    $dataForWrite = $transportData->serializeToString();
//                    $dataForWrite = base64_encode($dataForWrite);
//                    break;
//                default:
//                    return;
//            }
//
//            $tmpPackage = new \Zaly\Proto\Gateway\GwSocketWritePackage();
//            $tmpPackage->setContent($this->buildPackage($bodyFormat, $action, $request));
//            $tmpPackage->setSocketIds([$gatewaySocketId]);
//
//            $gwRequest = new GwSocketWriteRequest();
//            $gwRequest->setPackages([$tmpPackage]);
//            $gatewayURL = "http://{$gatewayURL}/gw/socket/write";
//
//
//            $response = $this->curl($gatewayURL, $gwRequest->serializeToString());
//            $responseProto = new \Zaly\Proto\Gateway\GwSocketWriteResponse();
//            $responseProto->mergeFromString($response);
//            $writeLength = $responseProto->getLength();
//            return;
//        } catch (Exception $ex) {
//            $this->ctx->Wpf_Logger->error($tag, $ex->getMessage());
//        }
//    }

    private function curl($url, $body)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $ret = curl_exec($ch);
        return $ret;
    }
}

/*

service GwSocketWriteService {
    rpc SendMessage (GwSocketWriteRequest) returns (GwSocketWriteResponse);
}

message GwSocketWriteRequest {
    string socketId = 1;
    bytes content = 2;
}

message GwSocketWriteResponse {
    int32 length = 1;
}




 */
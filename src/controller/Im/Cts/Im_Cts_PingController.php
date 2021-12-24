<?php


class Im_Cts_PingController extends \BaseController
{
    public $classNameForRequest = 'Zaly\Proto\Site\ImCtsPingRequest';
    public $classNameForResponse = 'Zaly\Proto\Client\ImStcPongRequest';
    public $pongAction = "im.stc.pong";

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpcResponseClassName()
    {
        return $this->classNameForResponse;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $this->keepSocket();
        $random = $request->getRandom();
        $response = new Zaly\Proto\Client\ImStcPongRequest();
        $response->setRandom($random);

        $sessionId = $this->sessionId;

//        // for debug
//
////        $this->ctx->Gateway_Client->sendMessage($sessionInfo, "im.stc.pong", $response);
//        $sessionInfo = array(
//            "gatewayURL" => "127.0.0.1:8000",
//            "gatewaySocketId" => $_GET["gw-socket-id"]
//        );
//
//        $this->ctx->Gateway_Client->sendMessage($sessionInfo, "im.stc.pong", $response);

        $this->rpcReturn($this->pongAction, $response);
    }

}


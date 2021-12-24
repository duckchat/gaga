<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 13/07/2018
 * Time: 10:41 PM
 */

use Zaly\Proto\Core\NoneResponse;
use Zaly\Proto\Site\ApiGwOnDisconnectRequest;
use Zaly\Proto\Site\ApiGwConfigResponse;

class Api_Gw_ConfigController extends \BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGwConfigRequest';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $this->updateData();
        $this->rpcReturn($transportData->getAction(), new ApiGwConfigResponse());
    }


    private function updateData()
    {
    }

}
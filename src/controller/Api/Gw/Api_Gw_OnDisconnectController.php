<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 13/07/2018
 * Time: 10:27 PM
 */

use Zaly\Proto\Site\ApiGwOnDisconnectRequest;
use Zaly\Proto\Site\ApiGwOnDisconnectResponse;

class Api_Gw_OnDisconnectController extends \BaseController
{
    public $classNameForRequest = '\Zaly\Proto\Site\ApiGwOnDisconnectRequest';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $this->updateData();
        $this->rpcReturn($transportData->getAction(), new ApiGwOnDisconnectResponse());
    }


}
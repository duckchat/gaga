<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 23/07/2018
 * Time: 4:20 PM
 */

class Api_User_ReportController extends BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiUserReportRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiUserReportResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiUserReportRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;

        $this->logger->info("api.user.report", "request=" . $request->serializeToString());

        $this->returnSuccessRPC(new Zaly\Proto\Site\ApiUserReportResponse());

        return;
    }

}
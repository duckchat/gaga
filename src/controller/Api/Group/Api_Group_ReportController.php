<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 24/07/2018
 * Time: 1:52 PM
 */

class Api_Group_ReportController extends Api_Group_BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiGroupReportRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiGroupReportResponse';
    public $userId;

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiGroupReportRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $this->logger->info($this->action, "request=" . $request->serializeToString());

        return $this->returnSuccessRPC(new \Zaly\Proto\Site\ApiGroupReportResponse());
    }

}
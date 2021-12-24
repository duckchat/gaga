<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 24/07/2018
 * Time: 4:51 PM
 */

class Im_Cts_UpdatePointerController extends Im_BaseController
{
    private $requestAction = "im.cts.updatePointer";

    private $classNameFromCtsUpdatePointerRequest = 'Zaly\Proto\Site\ImCtsUpdatePointerRequest';

    public function rpcRequestClassName()
    {
        return $this->classNameFromCtsUpdatePointerRequest;
    }

    /**
     * 己收到请求，并且校验完成session，准备处理具体逻辑
     * 当执行到doRealRpc() ,开始处理各自的具体业务逻辑
     *
     * @param \Zaly\Proto\Site\ImCtsUpdatePointerRequest $request
     * @param \Zaly\Proto\Core\TransportData $transportData
     * @return mixed
     */
    public function doRequest(\Google\Protobuf\Internal\Message $request, Zaly\Proto\Core\TransportData $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            //每个sync 请求均会执行updatepointer操作
            $userId = $this->userId;
            $deviceId = $this->deviceId;
            $u2Pointer = $request->getU2Pointer();
            $groupPointerMap = $request->getGroupsPointer();

            // 更新二人pointer
            // #TODO update u2 pointer
            $this->ctx->Wpf_Logger->info("im.cts.updatePointer", "clientPointer=" . $u2Pointer);

            if (isset($u2Pointer)) {

                $currentU2Pointer = $this->ctx->SiteU2MessageTable->queryU2Pointer($userId, $deviceId);

                if (empty($currentU2Pointer)) {
                    $currentU2Pointer = 0;
                }

                //update pointer > currentPointer
                if ($u2Pointer > $currentU2Pointer) {
                    $currentU2Pointer = $u2Pointer;

                    $maxU2Pointer = $this->ctx->SiteU2MessageTable->queryMaxMsgId($userId);

                    if (empty($maxU2Pointer)) {
                        $maxU2Pointer = 0;
                    }

                    $this->ctx->Wpf_Logger->info("im.cts.updatePointer", "currentPointer2=" . $currentU2Pointer);
                    $this->ctx->Wpf_Logger->info("im.cts.updatePointer", "maxPointer=" . $maxU2Pointer);

                    if ($currentU2Pointer > $maxU2Pointer) {
                        //容错
                        $currentU2Pointer = $maxU2Pointer;
                    }
                    $this->ctx->Wpf_Logger->info("im.cts.updatePointer", "pointer=" . $currentU2Pointer);

                    //clientSideType=1: 手机客户端  clientSideType=2:web端
                    $this->ctx->SiteU2MessageTable->updatePointer($userId, $deviceId, "1", $currentU2Pointer);
                }
            }


            //update group pointer
            if (!empty($groupPointerMap)) {
                foreach ($groupPointerMap as $groupId => $groupPointer) {

                    if (empty($groupPointer)) {
                        continue;
                    }

                    $currentGroupPointer = $groupPointer;
                    $maxGroupUserPointer = $this->ctx->SiteGroupMessageTable->queryMaxPointerByUser($groupId, $userId);

                    if ($currentGroupPointer < $maxGroupUserPointer) {
                        $currentGroupPointer = $maxGroupUserPointer;
                    }

                    $maxGroupPointer = $this->ctx->SiteGroupMessageTable->queryMaxIdByGroup($groupId);

                    if ($currentGroupPointer > $maxGroupPointer) {
                        $currentGroupPointer = $maxGroupPointer;
                    }

                    $this->ctx->SiteGroupMessageTable->updatePointer($groupId, $userId, $deviceId, $currentGroupPointer);

                }
            }
            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($this->requestAction, null);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg ==" . $ex->getMessage());
        }
    }

}

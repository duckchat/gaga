<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 18/07/2018
 * Time: 8:32 AM
 */


class Api_File_DownloadController extends \BaseController
{
    private $classNameForRequest  = '\Zaly\Proto\Site\ApiFileDownloadRequest';
    private $notMsgMimeType = [
        "image/jpeg",
        "image/jpg",
        "image/png",
    ];

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    public function rpcResponseClassName()
    {
        return $this->classNameForResponse;
    }

    /**
     * @param ApiFileDownloadRequest $request
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__.'_'.__FUNCTION__;

        ////TODO 校验用户是否有权限查看图片
        $response = new \Zaly\Proto\Site\ApiFileDownloadResponse();
        try{
            $fileId = $request->getFileId();
            $returnBase64 = $request->getReturnBase64();
            $messageId = $request->getMessageId();
            $isGroupMessage = $request->getIsGroupMessage();

            $mimeType = $this->ctx->File_Manager->contentType($fileId);
            if(!in_array($mimeType, $this->notMsgMimeType) && !$messageId) {
                throw new Exception("it's msg attachment");
            }
            if($messageId) {
                if($isGroupMessage == true) {
                    $info = $this->ctx->SiteGroupMessageTable->checkUserCanLoadImg($messageId, $this->userId);
                    if(!$info) {
                        throw new Exception("can't load img");
                    }
                    $this->ctx->Wpf_Logger->info($tag, "info ==" . json_encode($info) );
                } else {
                    ////TODO u2 can load img
                    $info = $this->ctx->SiteU2MessageTable->queryMessageByMsgId([$messageId]);
                    if(!$info) {
                        throw new Exception("can't load img");
                    }
                    $info = array_shift($info);

                    if($info['fromUserId'] != $this->userId && $info['toUserId'] != $this->userId) {
                        throw new Exception("can't load img");
                    }
                }
                $contentJson = $info['content'];
                $contentArr  = json_decode($contentJson, true);
                $url = $contentArr['url'];
                if($url != $fileId) {
                    throw new Exception("get img content is not ok");
                }
            }

            $content = $this->ctx->File_Manager->readFile($fileId);
            if(strlen($content)<1) {
                throw new Exception("download img void");
            }
            if ($returnBase64) {
                $response->setFileBase64(base64_encode($content));
            } else {
                $response->setFile($content);
            }
            $response->setContentType($this->ctx->File_Manager->contentType($fileId));

            $this->setRpcError($this->defaultErrorCode, "");
            $this->rpcReturn($this->getRequestAction(), $response);
        }catch (Exception $ex) {
            $this->logger->error("api.file.download", $ex);
            $errorCode = $this->zalyError->errorFileDownload;
            $errorInfo = $this->zalyError->getErrorInfo($errorCode);
            $this->setRpcError($errorCode, $errorInfo);
            $this->rpcReturn($this->getRequestAction(), $response);
        }
    }
}


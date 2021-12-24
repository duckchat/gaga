<?php

/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 18/07/2018
 * Time: 8:32 AM
 */
class Api_File_UploadController extends \BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiFileUploadRequest';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiFileUploadRequest $request $request
     * @param \Google\Protobuf\Internal\Message $transportData
     * @throws Exception
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $file = $request->getFile();
        $fileType = $request->getFileType();
        $isMessageAttachment = $request->getIsMessageAttachment();

        $response = new \Zaly\Proto\Site\ApiFileUploadResponse();
        try {
            //check max file size ，default 10M
            $maxFileSize = $this->ctx->Site_Config->getFileSizeConfig();
            $isUploadAllowed = $this->ctx->File_Manager->judgeFileSize(strlen($file), $maxFileSize);
            if(!$isUploadAllowed) {
                $errorInfo = ZalyText::getText("upload.file.size", $this->language);
                throw new Exception($errorInfo);
            }

            $fileId = $this->ctx->File_Manager->saveFile($file);
            if (empty($fileId)) {
                $this->setRpcError("error.file.wrong", "the file type is not supported.");
            }
            $response->setFileId($fileId);
            $this->setRpcError($this->defaultErrorCode, "");

        } catch (Exception $e) {
            $this->setRpcError("error.alert", $e->getMessage());
            $this->logger->error($this->action, $e);
        }
        $this->rpcReturn($this->getRequestAction(), $response);
        return;
    }
}


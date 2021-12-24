<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 27/07/2018
 * Time: 8:51 PM
 */


class Http_File_DownloadFileController extends \HttpBaseController
{
    private $documentMimeType = "application/octet-stream";
    private $notMsgMimeType = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/gif",
    ];
    public function index()
    {
        $tag = __CLASS__."-".__FUNCTION__;
        $fileId = $_GET['fileId'];
        $mimeType = $this->ctx->File_Manager->contentType($fileId);
        $isGroupMessage = isset($_GET['isGroupMessage']) ? $_GET['isGroupMessage'] : "";
        $messageId = isset($_GET['messageId']) ? $_GET['messageId'] : "";
        $returnBase64 = $_GET['returnBase64'];
        try{
            if(!in_array($mimeType, $this->notMsgMimeType) && !$messageId) {
                throw new Exception("it's msg attachment");
            }
            if($messageId) {
                if($isGroupMessage == true) {
                    $info = $this->ctx->SiteGroupMessageTable->checkUserCanLoadImg($messageId, $this->userId);
                    $mimeType = $info['msgType'] == \Zaly\Proto\Core\MessageType::MessageDocument ? $this->documentMimeType : $mimeType;
                    if(!$info) {
                        throw new Exception("no group premission, can't load img");
                    }
                } else {
                    ////TODO u2 can load img
                    $info = $this->ctx->SiteU2MessageTable->queryMessageByMsgId([$messageId]);
                    if(!$info) {
                        throw new Exception("no premission, can't load img");
                    }
                    $info = array_shift($info);
                    $mimeType = $info['msgType'] == \Zaly\Proto\Core\MessageType::MessageDocument ? $this->documentMimeType : $mimeType;

                    if($info['fromUserId'] != $this->userId && $info['toUserId'] != $this->userId) {
                        throw new Exception("no read permission, can't load img");
                    }
                }

                $contentJson = $info['content'];
                $contentArr  = json_decode($contentJson, true);
                $url = $contentArr['url'];
                if($url != $fileId) {
                    throw new Exception("get img content is not ok");
                }
            }
            $fileContent = $this->ctx->File_Manager->readFile($fileId);

            if(strlen($fileContent)<1) {
                throw new Exception("load file void");
            }

            header('Cache-Control: max-age=86400, public');
            header("Content-type:$mimeType");
            if($mimeType == $this->documentMimeType) {
                header("Content-Disposition:attachment");
            }

            if($returnBase64) {
                echo base64_decode($fileContent);
            } else {
                echo $fileContent;
            }
        }catch (Exception $ex) {
            header("Content-type:$mimeType");
            $this->ctx->Wpf_Logger->error($tag, $ex);
            echo "failed";
        }
    }
}

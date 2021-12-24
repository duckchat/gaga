<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 11/08/2018
 * Time: 4:17 PM
 */


class Http_File_DownloadWebMsgController extends \HttpBaseController
{
    public function index()
    {
        $tag = __CLASS__."-".__FUNCTION__;
        try{
            $msgId   = $_GET['msgId'];

            $isGroupMessage = isset($_GET['isGroupMessage']) ? $_GET['isGroupMessage'] : "";
            if(!strlen($msgId)) {
                throw new Exception("can't group load file");
            }
            if($isGroupMessage == true) {
                $info = $this->ctx->SiteGroupMessageTable->checkUserCanLoadImg($msgId, $this->userId);
                if(!$info) {
                    throw new Exception("can't group load file");
                }
            } else {
                ////TODO u2 can load img
                $info = $this->ctx->SiteU2MessageTable->queryMessageByMsgId([$msgId]);

                if(!$info) {
                    throw new Exception("no msg, can't u2 load file");
                }
                $info = array_shift($info);
                if($info['fromUserId'] != $this->userId && $info['toUserId'] != $this->userId) {
                    throw new Exception("no permission, can't u2 load file");
                }
            }
            $contentJson = $info['content'];
            $contentArr  = json_decode($contentJson, true);
            header("Content-type:text/html");
            echo $contentArr['code'];
        }catch (Exception $e) {
            $this->ctx->Wpf_Logger->error($tag, "error_msg ==" .$e->getMessage() );
            echo "";
        }
    }
}

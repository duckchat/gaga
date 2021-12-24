<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 08/10/2018
 * Time: 10:59 AM
 */

class MiniProgram_Gif_InfoController extends MiniProgram_BaseController
{
    private $fileType = 'gif';

    private $gifMiniProgramId = 104;
    private $title = "GIF";

    public function getMiniProgramId()
    {
        return $this->gifMiniProgramId;
    }

    public function requestException($ex)
    {
        $this->showPermissionPage();
    }

    public function preRequest()
    {
    }

    public function doRequest()
    {
        header('Access-Control-Allow-Origin: *');
        $method = $_SERVER['REQUEST_METHOD'];
        $tag = __CLASS__."-".__FUNCTION__;
        $gifId = $_GET['gifId'];
        $returnBase64 = isset($_GET['returnBase64']) ? $_GET['returnBase64'] : "";
        try{
            $result = $this->ctx->SiteUserGifTable->getGifByGifId($gifId);
            if(!$result) {
                echo "failed";
                return;
            }
            $gifUrl = $result['gifUrl'];
            $mimeType = $this->ctx->File_Manager->contentType($gifUrl);
            $fileContent = $this->ctx->File_Manager->readFile($gifUrl, $this->fileType );

            if(strlen($fileContent)<1) {
                throw new Exception("load file void");
            }

            header('Cache-Control: max-age=86400, public');
            header("Content-type:$mimeType");

            if($returnBase64) {
                $fileContent =  base64_encode($fileContent);
            } else {
                $fileContent =  $fileContent;
            }
            echo $fileContent;
        }catch (Exception $e) {
            header("Content-type:$mimeType");
            $this->ctx->Wpf_Logger->error($tag, "error_msg ==" .$e->getMessage() );
            echo "failed";
        }
    }
}
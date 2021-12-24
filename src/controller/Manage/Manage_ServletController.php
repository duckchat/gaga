<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 13/07/2018
 * Time: 6:32 PM
 */

abstract class Manage_ServletController extends MiniProgram_BaseController
{

    protected function getMiniProgramId()
    {
        return 100;
    }


    protected function preRequest()
    {
        if (!$this->ctx->Site_Config->isManager($this->userId)) {
            //不是管理员，exception
            throw new Exception("user has no permission");
        }
    }

    protected function doRequest()
    {
        $method = $_SERVER["REQUEST_METHOD"];

        if ($method == "POST") {
            $this->doPost();
        } elseif ($method == "GET") {
            $this->doGet();
        }
        return;
    }

    abstract protected function doGet();

    abstract protected function doPost();

    /**
     * @param Exception $ex
     * @return mixed|void
     */
    protected function requestException($ex)
    {
        echo $ex->getMessage();
        $this->showPermissionPage();
    }

}
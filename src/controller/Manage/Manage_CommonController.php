<?php
/**
 * 管理平台继承的小程序公用静态类
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:58 AM
 */

abstract class Manage_CommonController extends MiniProgram_BaseController
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
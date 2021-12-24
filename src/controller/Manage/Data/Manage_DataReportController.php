<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_DataReportController extends Manage_CommonController
{

    public function doRequest()
    {
        $this->ctx->Wpf_Logger->info("1111111", 'mange index');
        echo $this->display("manage_dataReportIndex");
        return;
    }

}
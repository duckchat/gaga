<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 3:48 PM
 */

class Manage_Uic_CreateController extends Manage_CommonController
{

    public function doRequest()
    {
        $lang = $this->language;

        $response = [
            'errCode' => "error",
        ];

        try {
            if ($this->createUic()) {
                $response['errCode'] = "success";
            } else {
                $this->ctx->Wpf_Logger->error("manage.uic.create", "create uic error");
            }
        } catch (Exception $e) {
            $response['errInfo'] = $e->getMessage();
            $this->ctx->Wpf_Logger->error("manage.uic.create", $e);
        }

        echo json_encode($response);
        return;
    }


    private function createUic()
    {
        return $this->ctx->SiteUicTable->createUic(20, 16);
    }
}
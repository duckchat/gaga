<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 3:48 PM
 */

class Manage_Uic_DeleteController extends Manage_CommonController
{

    public function doRequest()
    {
        $code = $_GET['code'];

        $params = ["lang" => $this->language];

        $response = [
            'errCode' => "error"
        ];


        if ($code) {
            $res = false;
            if ("all" == code) {
                $res = $this->deleteALlInvitationCode();
            } else {
                $res = $this->deleteInvitationCode($code);
            }

            if ($res) {
                $response['errCode'] = "success";
            }

        } else {
            $response['errInfo'] = "delete uic with error type";
        }


        echo json_encode($response);
        return;
    }

    private function deleteALlInvitationCode()
    {
        return $this->ctx->SiteUicTable->deleteAllUnusedCode();
    }

    private function deleteInvitationCode($code)
    {
        return $this->ctx->SiteUicTable->deleteAllUnusedCode($code);
    }

}
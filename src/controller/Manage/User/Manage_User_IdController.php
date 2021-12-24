<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 15/08/2018
 * Time: 10:59 AM
 */

class Manage_User_IdController extends Manage_CommonController
{

    public function doRequest()
    {
        $userId = $_GET['userId'];

        $params["userId"] = $userId;

        echo $this->display("manage_user_id", $params);
        return;
    }

}
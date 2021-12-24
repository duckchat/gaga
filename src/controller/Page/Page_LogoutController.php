<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 03/08/2018
 * Time: 10:20 AM
 */

class Page_LogoutController extends HttpBaseController
{

    public function index()
    {
        setcookie ($this->siteCookieName, "", time()-3600, "/", "", false, true);
        $apiPageLogin = "./index.php?action=page.passport.login";
        header("Location:" . $apiPageLogin);
        exit();
    }
}
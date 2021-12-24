<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 17/08/2018
 * Time: 3:42 PM
 */

class Page_LoginController extends HttpBaseController
{
    public  $headers;

    public function index()
    {
        $tag = __CLASS__.'->'.__FUNCTION__;

        try{
            $this->checkUserCookie();
            if($this->userId) {
                $jumpPage = $this->getJumpUrlFromParams();
                $apiPageIndex = ZalyConfig::getApiIndexUrl();
                if($jumpPage) {
                    if (strpos($apiPageIndex, "?")) {
                        $apiPageIndex .= "&".$jumpPage;
                    } else {
                        header("Location:" . $apiPageIndex . "?".$jumpPage);
                        $apiPageIndex .= "?".$jumpPage;
                    }
                }
                header("Location:" . $apiPageIndex);
                exit();
            }
        } catch (Exception $ex) {
            $this->logger->error($tag, $ex);
        }
        $apiPageLogin = "./index.php?action=page.passport.login";
        header("Location:" . $apiPageLogin);
        exit();
    }
}
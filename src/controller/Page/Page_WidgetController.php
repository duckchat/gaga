<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 13/08/2018
 * Time: 10:44 AM
 */

class Page_WidgetController extends  HttpBaseController
{

    public function doIndex()
    {
        $this->index();
    }

    public function index()
    {
//        $tag = __CLASS__.'-'.__FUNCTION__;
//        try{
//            $preSessionId = isset($_GET['preSessionId']) ? $_GET['preSessionId'] : "";
//            if($preSessionId) {
//                $this->handlePreSessionId();
//                $apiPageWidget = ZalyConfig::getApiPageWidget();
//                header("Location:".$apiPageWidget);
//                exit();
//            }
//            $this->checkUserCookie();
//        }catch (Exception $ex){
//            $this->userId = "";
//            $this->sessionId = "";
//            $this->ctx->Wpf_Logger->error($tag, $ex->getMessage());
//        }
//        $groupId = isset($_GET['groupId']) ? $_GET['groupId'] : "";
//        if(!$groupId) {
//            echo "not allowd access by widget";
//            return ;
//        }
//        if($this->userId) {
//            $profile = $this->ctx->SiteGroupTable->getUserWidgetGroupProfile($this->userId, $groupId);
//        } else {
//            $profile = $this->ctx->SiteGroupTable->getWidgetGroupProfile($groupId);
//        }
//        echo $this->display("widget_index", $profile);
        return;
    }
}
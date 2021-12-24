<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 15/10/2018
 * Time: 5:22 PM
 */


class MiniProgram_Test_IndexController extends  MiniProgram_BaseController
{

    private $testMiniProgramId = 115;
    private $action = "duckChat.message.send";
    private $groupType = "g";
    private $u2Type = "u";
    private $limit=30;
    private $roomType="";
    private $toId;

    public function getMiniProgramId()
    {
        return $this->testMiniProgramId;
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
        $tag = __CLASS__ ."-".__FUNCTION__;

        $pageUrl = $_COOKIE['duckchat_page_url'];
        $pageUrl = parse_url($pageUrl);
        parse_str($pageUrl['query'], $queries);
        $x = $queries['x'];
        list($type, $this->toId) = explode("-", $x);
        if($this->toId == $this->userId) {
            return;
        }

        if($type == $this->groupType) {
            $this->roomType = \Zaly\Proto\Core\MessageRoomType::MessageRoomGroup;
        }elseif($type == $this->u2Type) {
            $this->roomType = \Zaly\Proto\Core\MessageRoomType::MessageRoomU2;
        }


        if ($method == 'POST') {
            try{
                $type = isset($_POST['type']) ? $_POST['type'] :"send_msg";
                switch ($type) {
                    case "text" :
                        $this->setTextMessage($_POST);
                        break;
                }
                echo json_encode(["errorCode" => "success", "errorInfo" => ""]);
            }catch (Exception $ex) {
                echo json_encode(["errorCode" => "error.alert", 'errorInfo' => $ex->getMessage()]);
            }
        } else {

                $gifs = $this->ctx->SiteUserGifTable->getGifByUserId($this->userId, 0, $this->limit);
                foreach ($gifs as $key => $gif) {
                    $gif['gifUrl'] = "./index.php?action=miniProgram.gif.info&gifId=".$gif['gifId'];
                    $gif['isDefault'] = $gif['userId'] === 0 ?  0 : 1;
                    $gifs[$key] = $gif;
                }
                $results['gifs'] = $gifs;
                $results['gifs'] = json_encode($results['gifs']);
                echo $this->display("miniProgram_test_index", $results);
                return;
        }
    }
    private function setTextMessage()
    {
        $roomType = $this->roomType ? \Zaly\Proto\Core\MessageRoomType::MessageRoomU2 : \Zaly\Proto\Core\MessageRoomType::MessageRoomGroup;
        $time = ZalyHelper::getMsectime();
        $body = $time;
        $messageId = ZalyHelper::getMsgId($this->roomType, $this->toId);
        if($roomType == \Zaly\Proto\Core\MessageRoomType::MessageRoomU2) {
            $requestTransportDataString = ' {
                "action":"duckChat.message.send",
                "body":{
                    "@type":"type.googleapis.com/plugin.DuckChatMessageSendRequest",
                    "message":{
                        "msgId":"' . $messageId . '",
                        "fromUserId":"' . $this->userId . '",
                        "timeServer":"1539599093869",
                        "toUserId":"' . $this->toId . '",
                        "type":"MessageText",
                        "roomType":"MessageRoomU2",
                        "text":{
                            "body":"'.$body.'"
                        }
                    }
                },
                "timeMillis":"' . $time . '"
            }';
        } else {
            $requestTransportDataString = ' {
                "action":"duckChat.message.send",
                "body":{
                    "@type":"type.googleapis.com/plugin.DuckChatMessageSendRequest",
                    "message":{
                        "msgId":"' . $messageId . '",
                        "fromUserId":"' . $this->userId . '",
                        "timeServer":"1539599093869",
                        "toGroupId":"' . $this->toId . '",
                        "type":"MessageText",
                        "roomType":"MessageRoomGroup",
                        "text":{
                            "body":"'.$body.'"
                        }
                    }
                },
                "timeMillis":"' . $time . '"
            }';
        }

        $miniProgramProfile = $this->getMiniProgramProfileForTest($this->testMiniProgramId);

        $authKey = $miniProgramProfile['authKey'];


        $encryptedTransportData = $this->ctx->ZalyAes->encrypt($requestTransportDataString, $authKey);
        $requestUrl = "/?action=" . $this->action . "&body_format=json&miniProgramId=" . $this->testMiniProgramId;

        $requestUrl = ZalyHelper::getFullReqUrl($requestUrl);

        $this->ctx->Wpf_Logger->error( $this->action, "fihttp request url =" . $requestUrl);

        $this->ctx->ZalyCurl->request($requestUrl, "POST", $encryptedTransportData);

    }

    private function getMiniProgramProfileForTest($miniProgramId)
    {
        $miniProgramProfile = $this->ctx->SitePluginTable->getPluginById($miniProgramId);

        if (!empty($miniProgramProfile)) {

            if (empty($miniProgramProfile['authKey'])) {
                if (empty($authKey)) {
                    $miniProgramProfile['authKey'] = $this->ctx->Site_Config->getConfigValue(SiteConfig::SITE_PLUGIN_PLBLIC_KEY);
                }
            }

        }

        return $miniProgramProfile;
    }

}
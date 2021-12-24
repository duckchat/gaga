<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 05/09/2018
 * Time: 2:27 PM
 */

class MiniProgram_Gif_IndexController extends MiniProgram_BaseController
{

    private $gifMiniProgramId = 104;
    private $msgSendaction = "duckchat.message.send";
    private $groupType = "groupMsg";
    private $u2Type = "u2Msg";
    private $userRelationAction = "duckchat.user.relation";
    private $limit = 30;
    private $title = "GIF";
    private $roomType = "";
    private $toId;
    private $seeType = "see_gif";
    private $defaultGif = "duckchat";

    public function getMiniProgramId()
    {
        return $this->gifMiniProgramId;
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
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $pageUrl = $_COOKIE['duckchat_page_url'];
        $pageUrl = parse_url($pageUrl);
        parse_str($pageUrl['query'], $queries);

        $type = isset($queries['page']) ? $queries['page'] : "";
        $this->toId = isset($queries['x']) ? $queries['x'] : "";
        if ($this->toId == $this->userId) {
            return;
        }

        if ($type == $this->groupType) {
            $this->roomType = \Zaly\Proto\Core\MessageRoomType::MessageRoomGroup;
        } elseif ($type == $this->u2Type) {
            $this->roomType = \Zaly\Proto\Core\MessageRoomType::MessageRoomU2;
        }


        if ($method == 'POST') {
            try {
                $type = isset($_POST['type']) ? $_POST['type'] : "send_msg";
                switch ($type) {
                    case "send_msg" :
                        if ($this->toId) {
                            $this->sendWebMessage($_POST);
                        }
                        break;
                    case "add_gif":
                        $this->addGif($_POST);
                        break;
                    case "del_gif":
                        $this->delGif($_POST);
                        break;
                    case "save_gif":
                        $this->saveGif($_POST);
                        break;
                }
                $this->ctx->Wpf_Logger->error($tag, "post msg =" . json_encode($_POST));
                echo json_encode(["errorCode" => "success", "errorInfo" => ""]);
            } catch (Exception $ex) {
                echo json_encode(["errorCode" => "error.alert", 'errorInfo' => $ex->getMessage()]);
            }
        } else {
            $results = [
                "roomType" => $this->roomType,
                "toId" => $this->toId,
                "fromUserId" => $this->userId,
            ];
            $type = isset($_GET['type']) ? $_GET['type'] : "";
            if ($type == "see_gif") {
                $gifId = isset($_GET['gifId']) ? $_GET['gifId'] : '';
                $gif = $this->ctx->SiteUserGifTable->getGifInfo($this->userId, $gifId);
                if (!$gif) {
                    echo $this->display("miniProgram_gif_info", []);
                    return;
                }

                if ($gif['userId'] == $this->defaultGif || $gif['userId'] == $this->userId) {
                    $gif['isDefault'] = 1;
                } else {
                    $gif['isDefault'] = 0;
                }
                unset($gif['userId']);
                //gifId, gifUrl, width, height, userId
                $gif['gifUrl'] = "./index.php?action=miniProgram.gif.info&gifId=" . $gif['gifId'];
                echo $this->display("miniProgram_gif_info", $gif);
                return;
            } else {
                $gifs = $this->ctx->SiteUserGifTable->getGifByUserId($this->userId, 0, $this->limit);
                foreach ($gifs as $key => $gif) {
                    $gif['gifUrl'] = "./index.php?action=miniProgram.gif.info&gifId=" . $gif['gifId'];
                    $gif['isDefault'] = $gif['userId'] === 0 ? 0 : 1;
                    $gifs[$key] = $gif;
                }
                $results['gifs'] = $gifs;
                $results['gifs'] = json_encode($results['gifs']);
                echo $this->display("miniProgram_gif_index", $results);
                return;
            }
        }
    }

    private function sendWebMessage($data)
    {
        try {
            $gifId = $data['gifId'];
            $roomType = $this->roomType ? \Zaly\Proto\Core\MessageRoomType::MessageRoomU2 : \Zaly\Proto\Core\MessageRoomType::MessageRoomGroup;
            if ($roomType == \Zaly\Proto\Core\MessageRoomType::MessageRoomU2) {
                $userRelationReq = new \Zaly\Proto\Plugin\DuckChatUserRelationRequest();
                $userRelationReq->setUserId($this->userId);
                $userRelationReq->setOppositeUserId($this->toId);
                $response = $this->requestDuckChatInnerApi($this->gifMiniProgramId, $this->userRelationAction, $userRelationReq);

                if ($response->getRelationType() != \Zaly\Proto\Core\FriendRelationType::FriendRelationFollow) {
                    $errorCode = $this->zalyError->errorFriend;
                    $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                    throw new Exception($errorInfo);
                }

                $userRelationReq = new \Zaly\Proto\Plugin\DuckChatUserRelationRequest();
                $userRelationReq->setUserId($this->toId);
                $userRelationReq->setOppositeUserId($this->userId);
                $response = $this->requestDuckChatInnerApi($this->gifMiniProgramId, $this->userRelationAction, $userRelationReq);

                if ($response->getRelationType() != \Zaly\Proto\Core\FriendRelationType::FriendRelationFollow) {
                    $errorCode = $this->zalyError->errorFriend;
                    $errorInfo = $this->zalyError->getErrorInfo($errorCode);
                    throw new Exception($errorInfo);
                }

            }

            $gifInfo = $this->ctx->SiteUserGifTable->getGifByGifId($gifId);
            $gifUrl = "index.php?action=miniProgram.gif.info&gifId=" . $gifInfo['gifId'];
            $webCode = '<!DOCTYPE html> <html> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"><style>body, html{margin:0; padding:0;}</style></head> <body> <img src="' . $gifUrl . '" width="100%" height="100%"> </body> </html>';

            $landingPageUrl = "index.php?action=miniProgram.gif.index&type=see_gif&gifId=" . $gifInfo['gifId'];

            $simplePluginProfile = new \Zaly\Proto\Core\SimplePluginProfile();
            $simplePluginProfile->setId($this->gifMiniProgramId);
            $simplePluginProfile->setLoadingType(\Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage);
            $simplePluginProfile->setLandingPageUrl($landingPageUrl);
            $simplePluginProfile->setLandingPageWithProxy(true);

            $webMsg = new \Zaly\Proto\Core\WebMessage();
            $webMsg->setWidth($gifInfo['width']);
            $webMsg->setHeight($gifInfo['height']);
            $webMsg->setCode($webCode);
            $webMsg->setPluginId($this->gifMiniProgramId);
            $webMsg->setTitle($this->title);
            $webMsg->setJumpPluginProfile($simplePluginProfile);

            $messageId = ZalyHelper::getMsgId($this->roomType, $this->toId);

            $message = new \Zaly\Proto\Core\Message();
            $message->setMsgId($messageId);
            $message->setType(\Zaly\Proto\Core\MessageType::MessageWeb);
            $message->setTimeServer(ZalyHelper::getMsectime());
            $message->setWeb($webMsg);
            $message->setRoomType($roomType);
            $message->setFromUserId($this->userId);
            if ($roomType == \Zaly\Proto\Core\MessageRoomType::MessageRoomU2) {
                $message->setToUserId($this->toId);
            } else {
                $message->setToGroupId($this->toId);
            }

            $duckchatReqData = new \Zaly\Proto\Plugin\DuckChatMessageSendRequest();
            $duckchatReqData->setMessage($message);
            $this->requestDuckChatInnerApi($this->gifMiniProgramId, $this->msgSendaction, $duckchatReqData);
        } catch (Exception $ex) {
            $tag = __CLASS__ . '->' . __FUNCTION__;
            $this->logger->error($tag, $ex);
        }
    }

    public function addGif($data)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $gifUrl = $data['gifId'];
            $gifId = md5($gifUrl);
           try{
               list($width, $height, $type, $attr) = $this->ctx->File_Manager->getFileSize($gifUrl);
           }catch (Exception $ex) {
               $width = " 200p";
               $height = "200";

           }

            $siteGifData = [
                'gifId' => $gifId,
                'gifUrl' => $gifUrl,
                'width'  => $width,
                'height' => $height,
                'addTime' => ZalyHelper::getMsectime()
            ];

            $siteUserGifData = [
                'userId' => $this->userId,
                'gifId' => $gifId,
                'addTime' => ZalyHelper::getMsectime()
            ];
            $this->ctx->SiteUserGifTable->addGif($siteGifData, $siteUserGifData);
        } catch (Exception $ex) {
            $this->logger->error($tag, $ex);
            throw $ex;
        }
    }

    public function delGif($data)
    {
        $gifId = $data['gifId'];
        return $this->ctx->SiteUserGifTable->delGif($this->userId, $gifId);
    }

    public function saveGif($data)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $gifId = $data['gifId'];
            $siteUserGifData = [
                'userId' => $this->userId,
                'gifId' => $gifId,
                'addTime' => ZalyHelper::getMsectime()
            ];
            $this->ctx->SiteUserGifTable->addUserGif($siteUserGifData);
        } catch (Exception $ex) {
            $this->logger->error($tag, $ex);
            throw $ex;
        }
    }

}
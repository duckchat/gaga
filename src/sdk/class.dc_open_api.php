<?php

/**
 *
 * DuckChat小程序开放接口SDK
 *
 * v1.0.14
 *
 * User: anguoyue
 * Date: 11/09/2018
 * Time: 5:43 PM
 */
class DC_Open_Api
{

    private $miniProgramId;
    private $secretKey;
    private $serverAddress;
    private $requestTimeOut;//默认超时时间3s

    public function __construct($serverAddress, $miniProgramId, $secretKey, $requestTimeOut = 3)
    {
        $this->miniProgramId = $miniProgramId;
        $this->secretKey = $secretKey;
        $this->serverAddress = $serverAddress;
        $this->requestTimeOut = $requestTimeOut;
    }

    public function getSessionProfile($duckchatSessionId)
    {
        $requestAction = "duckchat.session.profile";
        $requestData = array(
            "action" => $requestAction,
            "body" => array(
                "@type" => "type.googleapis.com/plugin.DuckChatSessionProfileRequest",
                "encryptedSessionId" => $duckchatSessionId
            ),
            "timeMillis" => $this->getTimeMillis(),
        );


        $response = $this->duckChatRequest($requestAction, $requestData);

        if (empty($response)) {
            throw new Exception("get empty response by duckchat_sessionid error");
        }

        //这里需要处理
        $profile = $response;
        return $profile;

    }

    public function getUserProfile($userId)
    {
        $requestAction = "duckchat.user.profile";

        $requestData = array(
            "action" => $requestAction,
            "body" => array(
                "@type" => "type.googleapis.com/plugin.DuckChatUserProfileRequest",
                "userId" => $userId,
            ),
            "timeMillis" => $this->getTimeMillis(),
        );

        $response = $this->duckChatRequest($requestAction, $requestData);

        return $response;
    }

    //get two user relation
    public function getUserRelation($userId, $oppositeUserId)
    {
        $requestAction = "duckchat.user.relation";

        $requestData = array(
            "action" => $requestAction,
            "body" => array(
                "@type" => "type.googleapis.com/plugin.DuckChatUserRelationRequest",
                "userId" => $userId,
                "oppositeUserId" => $oppositeUserId,
            ),
            "timeMillis" => $this->getTimeMillis(),
        );

        $response = $this->duckChatRequest($requestAction, $requestData);

        return $response;
    }

    public function getSiteAdmins()
    {
        $requestAction = "duckchat.site.admins";

        $requestData = array(
            "action" => $requestAction,
            "body" => array(
                "@type" => "type.googleapis.com/plugin.DuckChatSiteAdminsRequest",
            ),
            "timeMillis" => $this->getTimeMillis(),
        );

        $response = $this->duckChatRequest($requestAction, $requestData);

        return $response;
    }

    //send proxy text message
    public function sendTextMessage($isGroup, $fromUserId, $toId, $textBody)
    {
        $requestAction = "duckchat.message.send";

        $requestData = array(
            "action" => $requestAction,
            "body" => array(
                "@type" => "type.googleapis.com/plugin.DuckChatMessageSendRequest",
                "message" => array(
                    "msgId" => $this->buildMessageId($isGroup, $fromUserId),
                    "fromUserId" => $fromUserId,
                    "type" => "MessageText",
                    "roomType" => $isGroup ? "MessageRoomGroup" : "MessageRoomU2",
                    "text" => array(
                        "body" => $textBody,
                    ),
                    "timeServer" => $this->getTimeMillis(),
                ),
            ),
            "timeMillis" => $this->getTimeMillis(),
        );

        if ($isGroup) {
            $requestData['body']['message']['toGroupId'] = $toId;
        } else {
            $requestData['body']['message']['toUserId'] = $toId;
        }

        $response = $this->duckChatRequest($requestAction, $requestData);

        return true;
    }

    //send notice message
    public function sendNoticeMessage($isGroup, $fromUserId, $toId, $noticeBody)
    {
        $requestAction = "duckchat.message.send";

        $requestData = array(
            "action" => $requestAction,
            "body" => array(
                "@type" => "type.googleapis.com/plugin.DuckChatMessageSendRequest",
                "message" => array(
                    "msgId" => $this->buildMessageId($isGroup, $fromUserId),
                    "fromUserId" => $fromUserId,
                    "type" => "MessageNotice",
                    "roomType" => $isGroup ? "MessageRoomGroup" : "MessageRoomU2",
                    "notice" => array(
                        "body" => $noticeBody,
                    ),
                    "timeServer" => $this->getTimeMillis(),
                ),
            ),
            "timeMillis" => $this->getTimeMillis(),
        );

        if ($isGroup) {
            $requestData['body']['message']['toGroupId'] = $toId;
        } else {
            $requestData['body']['message']['toUserId'] = $toId;
        }

        $response = $this->duckChatRequest($requestAction, $requestData);

        return true;
    }

    //send web message
    public function sendWebMessage($isGroup, $fromUserId, $toId, $title, $webHtmlCode, $width, $height, $gotoUrl = false, $useProxy = false)
    {
        $requestAction = "duckchat.message.send";

        $requestData = array(
            "action" => $requestAction,
            "body" => array(
                "@type" => "type.googleapis.com/plugin.DuckChatMessageSendRequest",
                "message" => array(
                    "msgId" => $this->buildMessageId($isGroup, $fromUserId),
                    "fromUserId" => $fromUserId,
                    "type" => "MessageWeb",
                    "roomType" => $isGroup ? "MessageRoomGroup" : "MessageRoomU2",
                    "web" => array(
                        "title" => $title,
                        "code" => $webHtmlCode,
                        "width" => $width,
                        "height" => $height,
                        "hrefURL" => $useProxy ? $gotoUrl : "",
                        "pluginId" => $this->miniProgramId,
                        "jumpPluginProfile" => array(
                            "id" => $this->miniProgramId,
                            "landingPageUrl" => $gotoUrl,
                        ),
                    ),
                    "timeServer" => $this->getTimeMillis(),
                ),
            ),
            "timeMillis" => $this->getTimeMillis(),
        );

        if ($isGroup) {
            $requestData['body']['message']['toGroupId'] = $toId;
        } else {
            $requestData['body']['message']['toUserId'] = $toId;
        }

        $response = $this->duckChatRequest($requestAction, $requestData);

        return true;

    }

    //send web notice message
    public function sendWebNoticeMessage($isGroup, $fromUserId, $toId, $title, $noticeHtmlCode, $height, $gotoUrl = false, $useProxy = false)
    {
        $requestAction = "duckchat.message.send";

        $requestData = array(
            "action" => $requestAction,
            "body" => array(
                "@type" => "type.googleapis.com/plugin.DuckChatMessageSendRequest",
                "message" => array(
                    "msgId" => $this->buildMessageId($isGroup, $fromUserId),
                    "fromUserId" => $fromUserId,
                    "type" => "MessageWebNotice",
                    "roomType" => $isGroup ? "MessageRoomGroup" : "MessageRoomU2",
                    "webNotice" => array(
                        "title" => $title,
                        "code" => $noticeHtmlCode,
                        "height" => $height,
                        "hrefURL" => $useProxy ? $gotoUrl : "",
                        "pluginId" => $this->miniProgramId,
                        "jumpPluginProfile" => array(
                            "id" => $this->miniProgramId,
                            "landingPageUrl" => $gotoUrl,
                        ),
                    ),
                    "timeServer" => $this->getTimeMillis(),
                ),
            ),
            "timeMillis" => $this->getTimeMillis(),
        );

        if ($isGroup) {
            $requestData['body']['message']['toGroupId'] = $toId;
        } else {
            $requestData['body']['message']['toUserId'] = $toId;
        }

        $response = $this->duckChatRequest($requestAction, $requestData);

        return true;

    }

    protected function duckChatRequest($action, $request)
    {
        $requestUrl = $this->serverAddress . "/?action=" . $action
            . "&body_format=json&miniProgramId=" . $this->miniProgramId;

        //json_encode, turn array to string
        $request = json_encode($request);

        //加密发送
        $encryptedRequestData = $this->encrypt($request, $this->secretKey);

        $encryptedResponse = $this->doCurlRequest($encryptedRequestData, $requestUrl, 'POST');

        //解密结果
        $httpResponseData = $this->decrypt($encryptedResponse, $this->secretKey);

        return $httpResponseData;
    }

    private function getHeaderValue($header, $key)
    {
        if (empty($header)) {

        }
        return $header['_' . $key];
    }


    private function doCurlRequest($params, $url, $method)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $_curlObj = curl_init();

            curl_setopt($_curlObj, CURLOPT_URL, $url);
            curl_setopt($_curlObj, CURLOPT_TIMEOUT, $this->requestTimeOut);//3s timeout
            curl_setopt($_curlObj, CURLOPT_NOBODY, false);
            curl_setopt($_curlObj, CURLOPT_POST, true);
            curl_setopt($_curlObj, CURLOPT_POSTFIELDS, $params);
            curl_setopt($_curlObj, CURLOPT_RETURNTRANSFER, true);

            if (($resp = curl_exec($_curlObj)) === false) {
                throw new Exception(curl_error($_curlObj));
            }
            curl_close($_curlObj);
            return $resp;
        } catch (\Exception $e) {
            $this->print_errorLog($tag, $e);
        }
    }


    /******************** tools function ****************/

    /**
     * @param $isGroup 是否是群组消息
     * @param $fromUserId 发送者的userId
     * @return string
     */
    public function buildMessageId($isGroup, $fromUserId)
    {
        $messageId = "U2-";
        if ($isGroup) {
            $messageId = "GP-";
        }

        if (!empty($fromUserId)) {
            $messageId .= substr($fromUserId, 0, 8);
        } else {
            $randomStr = $this->generateStrKey(8);
            $messageId .= $randomStr;
        }

        $messageId .= "-" . $this->getTimeMillis();
        return $messageId;
    }

    /**
     * get current time (ms)
     * @return float
     */
    public function getTimeMillis()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    private function print_errorLog($tag, $e)
    {
        error_log($tag . " " . $e->getMessage() . " " . $e->getTraceAsString());
    }

    private function generateStrKey($length = 16, $strParams = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        if (!is_int($length) || $length < 0) {
            $length = 16;
        }

        $str = '';
        for ($i = $length; $i > 0; $i--) {
            $str .= $strParams[mt_rand(0, strlen($strParams) - 1)];
        }

        return $str;
    }

    /**
     * AES加密方式，Electronic Codebook Book
     */
    CONST METHOD = "AES-128-ECB";
    /**
     * 如果不设置，OPENSSL_ZERO_PADDING， 默认将会按照PKCS#7填充
     * OPENSSL_RAW_DATA 按照 raw data解析 ，不然默认是base64
     */
    //    //////CONST OPTION = OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING;
    CONST OPTION = OPENSSL_RAW_DATA;

    /**
     * aes128ecb pkcs5加密数据
     * mcrypt_encrypt 在php7.2以后弃用
     *
     * @author 尹少爷 2018.03.21
     *
     * @param string $key 加密key
     * @param string $data 加密数据
     *
     * @return string base64
     */
    private function encrypt($data, $key)
    {
        return openssl_encrypt($data, self::METHOD, $key, self::OPTION);
    }

    /**
     * aes128ecb pkcs5解密数据
     * mcrypt_decrypt 在php7.2以后弃用
     *
     * @author 尹少爷 2018.03.21
     *
     * @param string $key 解密key
     * @param string $data 解密数据
     *
     * @return string
     */
    private function decrypt($data, $key)
    {
        $data = openssl_decrypt($data, self::METHOD, $key, self::OPTION);
        return $data;
    }
}
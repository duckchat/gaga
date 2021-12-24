<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 9:57 AM
 */

class ZalyHelper
{
    /*
     * php 毫秒
     */
    public static function getMsectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    public function getCurrentTimeSeconds()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)));
        return $msectime;
    }


    public static function getMsgId($type, $toId)
    {
        if($type === 1) {
            $timeMillis = self::getMsectime();
            $msgId = "U2-" . substr($toId, 0, 8) . "-" . $timeMillis;
        } else {
            $timeMillis = self::getMsectime();
            $msgId = "GP-";
            if (!empty($toId)) {
                $msgId .= substr($toId, 0, 8);
            } else {
                $randomStr = self::generateStrKey(8);
                $msgId .= $randomStr;
            }
            $msgId .= "-" . $timeMillis;
        }
        return $msgId;
    }

    public static function generateStrId()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public static function generateStrKey($length = 16, $strParams = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
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


    public static function generateNumberKey($length = 16, $strParams = '0123456789')
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


    public function judgeOrigin()
    {
        //获取USER AGENT
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        //分析数据
        $isWeb = (strpos($agent, 'windows nt')) ? true : false;
        $isIphone = (strpos($agent, 'iphone')) ? true : false;
        $isAndroid = (strpos($agent, 'android')) ? true : false;
        //输出数据
        if ($isWeb) {
            return 1;
        }

        if ($isIphone || $isAndroid) {
            return 0;
        }

    }

    public static function quickSortMsg($arr)
    {
        $len = count($arr);
        if ($len <= 1) {
            return $arr;
        }
        $key = $arr[0];
        $arrLeft = array();
        $arrRight = array();
        for ($i = 1; $i < $len; $i++) {
            if ($arr[$i]["msgTime"] <= $key["msgTime"]) {
                $arrLeft[] = $arr[$i];
            } else {
                $arrRight[] = $arr[$i];
            }
        }
        $arrLeft = self::quickSortMsg($arrLeft);
        $arrRight = self::quickSortMsg($arrRight);
        return array_merge($arrRight, array($key), $arrLeft);
    }

    public static function hideMobile($phone)
    {
        $isMob = "/^1[0-9]{1}\d{9}$/";

        if (preg_match($isMob, $phone)) {
            $phone = substr_replace($phone, '****', 3, 4);
        }
        return $phone;
    }

    public static function checkOpensslEncryptExists()
    {
        if (!function_exists("openssl_encrypt")) {
            return false;
        }
        return true;
    }

    public static function isEmail($email)
    {
        return preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/", $email);
    }

    public static function isPhoneNumber($phoneNumber)
    {
        return preg_match("/^1[3456789]{1}\d{9}$/", $phoneNumber);
    }

    public static function verifyChars($verifyChars, $containsChars)
    {
        if($containsChars == "") {
            return true;
        }
        $flagLetter = true;
        $flagNum = true;
        $flagSpecialCharacters = true;

        if(strpos($containsChars, "letter") !== false) {
            $flagLetter = preg_match("/[a-zA-Z]/", $verifyChars, $matches);
        }
        if(strpos($containsChars, "number") !== false) {
            $flagNum = preg_match("/\d/", $verifyChars, $matches);
        }
        if(strpos($containsChars, "special_characters") !== false) {
            $flagSpecialCharacters = preg_match("/[\^%#`@&*$\(\){}!\.~:,\<\>_\-\+\=|;:\'\"]/", $verifyChars, $matches);
        }
        if($flagLetter && $flagNum && $flagSpecialCharacters) {
            return true;
        }
        return false;
    }

    public static function getFullReqUrl($reqUrl)
    {
        try {
            $reqUrlStruct = parse_url($reqUrl);

            if (!empty($reqUrlStruct["scheme"])) {
                $query = !empty($reqUrlStruct["query"]) ? "?" . $reqUrlStruct["query"] : "";
                $reqUrl = $reqUrlStruct["path"] . $query;
            }

            $defaultScheme = "http";
            if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')) {
                $defaultScheme = 'https';
            }

            $defaultHost = $_SERVER['HTTP_HOST'];
            $schema = "";
            $host = "";
            // 必须用scheme，防止用户多输//
            if (empty($reqUrlStruct["scheme"]) && empty($reqUrlStruct['host'])) {
                $schema = $defaultScheme;
                $host = $defaultHost;
            } else {
                $schema = $reqUrlStruct["scheme"];
                $host = $reqUrlStruct["host"];
                $port = empty($reqUrlStruct["port"]) ? "" : ":{$reqUrlStruct["port"]}";
                $host = $host . $port;
            }

            if (strpos($reqUrl, "/") == 0) {
                $fullUrl = "{$schema}://{$host}{$reqUrl}";
            } elseif (strpos($reqUrl, "./") == 0) {
                $reqUrl = str_replace("./", "/", $reqUrl);
                $fullUrl = "{$schema}://{$host}{$reqUrl}";
            } else {
                $fullUrl = "{$schema}://{$host}/{$reqUrl}";
            }
            return $fullUrl;
        } catch (Exception $ex) {
            return $reqUrl;
        }
    }

    public static function getRequestAddressPath()
    {
        $defaultScheme = "http";
        if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')) {
            $defaultScheme = 'https';
        }

        $defaultHost = $_SERVER['HTTP_HOST'];
        $requestUri = isset($_SERVER['REQUEST_URI']) ? str_replace(array("\\", "//"), array("/", "/"), $_SERVER['REQUEST_URI']) : "";
        $requestUris = explode("/", $requestUri);
        array_pop($requestUris);
        $requestUriPath = "";
        if(count($requestUris)) {
            $requestUriPath = implode("/", $requestUris);
        }
        return $defaultScheme."://".$defaultHost.$requestUriPath;
    }

    public static function isUicNumber($str)
    {
        return preg_match("/^[0-9]{6,20}$/", $str);
    }

    public static function isLoginName($str)
    {
        return preg_match("/^[A-Za-z0-9_]+$/", $str);
    }
    public static function getIp()
    {
        $ip = "";
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;

    }
}
<?php

use Zaly\Proto\Core\TransportDataHeaderKey;

/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 17/07/2018
 * Time: 10:34 AM
 */
class ZalyCurl
{
    protected $_curlObj = '';
    protected $_bodyContent = '';
    protected $timeOut = 3;///单位秒
    protected $wpf_Logger;
    protected $_delHeaders = array("host", "content-length", "connection", "accept-encoding", "content-encoding");
    protected $contentEncoding = "content-encoding";
    protected $acceptEncoding = "accept-encoding";

    public function __construct()
    {
        $this->wpf_Logger = new Wpf_Logger();
    }

    /**
     * @param $method
     * @param $url
     * @param $requestBody
     * @param int $timeOut
     * @return mixed
     * @throws Exception
     */
    public function httpRequestByAction($method, $url, $requestBody, $timeOut = 3)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $this->timeOut = $timeOut;

            //解析url，获取action && body_format
            $actionParams = $this->getActionUrlParams($url);

            $action = $actionParams['action'];
            $bodyFormat = $actionParams['body_format'];

            if (empty($action)) {
                throw new Exception("request url with no action");
            }

            //request
            $params = $this->buildRequestTransportData($action, $requestBody, $bodyFormat);

            $respData = $this->_curl($url, $method, $params, []);

            //response
            $responseBody = $this->resolveResponseTransportData($action, $respData, $bodyFormat);

            return $responseBody;
        } catch (\Exception $e) {
            $message = sprintf("msg:%s file:%s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
            $this->wpf_Logger->error($tag, 'when run Router, unexpected error :', $message);
            throw $e;
        }
    }

    private function buildRequestTransportData($action, $requestBody, $formatBody = 'pb')
    {
        $anyBody = new \Google\Protobuf\Any();
        $anyBody->pack($requestBody);

        $transportData = new \Zaly\Proto\Core\TransportData();
        $transportData->setAction($action);
        $transportData->setBody($anyBody);

        $requestParams = "";
        switch ($formatBody) {
            case "json":
                $requestParams = $transportData->serializeToJsonString();
                break;
            case "pb":
                $requestParams = $transportData->serializeToString();
                break;
            case "base64pb":
                $body = $transportData->serializeToString();
                $requestParams = base64_encode($body);
                break;
        }

        return $requestParams;
    }

    private function resolveResponseTransportData($action, $requestResponse, $formatBody = 'pb')
    {
        $responseTransportData = new Zaly\Proto\Core\TransportData();
        switch ($formatBody) {
            case "json":
                $responseTransportData->mergeFromJsonString($requestResponse);
                break;
            case "pb":
                $responseTransportData->mergeFromString($requestResponse);
                break;
            case "base64pb":
                $realData = base64_decode($requestResponse);
                $responseTransportData->mergeFromString($realData);
                break;
        }

        if ($action != $responseTransportData->getAction()) {
            throw new Exception("response with error action,request action=" . $action
                . " response action=" . $responseTransportData->getAction());
        }


        $responseHeader = $responseTransportData->getHeader();

        if (empty($responseHeader)) {
            throw new Exception("action=" . $action . " response with empty header");
        }

        $errCode = $this->getHeaderValue($responseHeader, TransportDataHeaderKey::HeaderErrorCode);

        if ("success" == $errCode) {
            $responseMessage = $responseTransportData->getBody()->unpack();
            return $responseMessage;
        } else {
            $errInfo = $this->getHeaderValue($responseHeader, TransportDataHeaderKey::HeaderErrorInfo);
            throw new Exception("action=" . $action . "errCode=" . $errCode . " errInfo=" . $errInfo);
        }
    }

    private function getHeaderValue($header, $key)
    {
        return $header['_' . $key];
    }

    /**
     * @param $action
     * @param $requestBody
     * @param $url add &body_format=pb
     * @param $method
     * @return mixed
     * @throws Exception
     */
    public function requestWithActionByPb($action, $requestBody, $url, $method, $isBase64Pb = false, $timeOut = 3)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $this->timeOut = $timeOut;

            $anyBody = new \Google\Protobuf\Any();
            $anyBody->pack($requestBody);

            $transportData = new \Zaly\Proto\Core\TransportData();
            $transportData->setAction($action);
            $transportData->setBody($anyBody);
            $params = $transportData->serializeToString();
            if ($isBase64Pb == true) {
                $params = base64_encode($params);
            }
            $resp = $this->_curl($url, $method, $params, []);
            return $resp;
        } catch (\Exception $e) {
            $message = sprintf("msg:%s file:%s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
            $this->wpf_Logger->error($tag, 'when run Router, unexpected error :', $message);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 发送curl请求
     *
     * @author 尹少爷 2017.12.22
     *
     * @param string method
     * @param string url
     * @param array params
     * @param array headers
     *
     * @return bool|mix
     * @throws Exception
     */
    public function request($url, $method, $params = [], $headers = [], $timeOut = 3)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $this->timeOut = $timeOut;
            $resp = $this->_curl($url, $method, $params, $headers);
            curl_close($this->_curlObj);
            return $resp;
        } catch (\Exception $e) {
            $message = sprintf("msg:%s file:%s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
            $this->wpf_Logger->error($tag, 'when run Router, unexpected error :', $message);
            throw new Exception($e->getMessage());
        }
    }

    public function requestAndReturnHeaders($url, $method, $params = [], $headers = [])
    {
        try {
            $urlParams = parse_url($url);
            $query = isset($urlParams['query']) ? $urlParams['query'] : [];
            $urlParams = $this->convertUrlQuery($query);

            $bodyFormat = isset($urlParams['body_format']) ? $urlParams['body_format'] : "";
            $action = isset($urlParams['action']) ? $urlParams['action'] : "";
            $body = json_decode($params, true);

            if (isset($bodyFormat) && !isset($body['action'])) {
                switch ($bodyFormat) {
                    case 'json':
                        $body = json_decode($params, true);
                        $params = [
                            "action" => $action,
                            "body" => $body,
                        ];
                        $params = json_encode($params);
                        break;
                    case 'pb':
                        $anyBody = new \Google\Protobuf\Any();
                        $anyBody->pack($params);
                        $transportData = new \Zaly\Proto\Core\TransportData();
                        $transportData->setAction($action);
                        $transportData->setBody($anyBody);
                        $params = $transportData->serializeToString();
                        break;
                    case 'base64pb':
                        trigger_error("TO DO", E_USER_ERROR);
                        break;
                }
            }

            $resp = $this->_curl($url, $method, $params, $headers, true);

            $curl_info = curl_getinfo($this->_curlObj);
            $httpCode = curl_getinfo($this->_curlObj, CURLINFO_HTTP_CODE);

            curl_close($this->_curlObj);

            $header_size = $curl_info['header_size'];

            $header = substr($resp, 0, $header_size);
            $body = substr($resp, $header_size);

            $headerRows = explode("\r\n", $header);

            $header = array();
            foreach ($headerRows as $val) {
                $row = explode(":", $val, 2);
                if (count($row) != 2) {
                    continue;
                }

                $headerKey = trim($row[0]);
                $headerValue = trim($row[1]);
                $header[$headerKey] = $headerValue;
            }

            $retValue = array(
                "body" => $body,
                "httpCode" => $httpCode,
                "header" => $header
            );
            return $retValue;
        } catch (\Exception $e) {
            $message = sprintf("msg:%s file:%s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
            error_log('when run Router, unexpected error :' . $message);
            throw new Exception($e->getMessage());
        }
    }

    private function _curl($url, $method, $params, $headers, $isReturnHeader = false)
    {
        $tag = __CLASS__ . "-" . __FUNCTION__;

        $this->_curlObj = curl_init();

        if (!empty($params)) {
            $this->_bodyContent = $params;
            if (is_array($params)) {
                $this->_bodyContent = http_build_query($params, '', '&');
            }
        }
        $newHeaders = array();

        $acceptEncoding = '';
        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                $delKey = strtolower($key);
                if ($delKey == $this->acceptEncoding) {
                    $acceptEncoding = $value;
                }
                if (in_array($delKey, $this->_delHeaders)) {
                    continue;
                }
                $newHeaders[] = $key . ': ' . $value;

            }
            curl_setopt($this->_curlObj, CURLOPT_HTTPHEADER, $newHeaders);
        }

        curl_setopt($this->_curlObj, CURLOPT_URL, $url);
        curl_setopt($this->_curlObj, CURLOPT_TIMEOUT, $this->timeOut);
        if ($acceptEncoding != "") {
            curl_setopt($this->_curlObj, CURLOPT_ENCODING, $acceptEncoding);
        }
        curl_setopt($this->_curlObj, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->_curlObj, CURLOPT_MAXREDIRS, 6);
        curl_setopt($this->_curlObj, CURLOPT_RETURNTRANSFER, true);
        //跳过ssl校验
//        curl_setopt($this->_curlObj, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($this->_curlObj, CURLOPT_SSL_VERIFYHOST, FALSE);

        switch (strtoupper($method)) {
            case 'HEAD':
                curl_setopt($this->_curlObj, CURLOPT_NOBODY, true);
                break;
            case 'POST':
                curl_setopt($this->_curlObj, CURLOPT_POSTFIELDS, $this->_bodyContent);
                curl_setopt($this->_curlObj, CURLOPT_NOBODY, false);
                curl_setopt($this->_curlObj, CURLOPT_POST, true);
                break;
            default:
                ////GET
                curl_setopt($this->_curlObj, CURLOPT_HTTPGET, true);
        }

        if ($isReturnHeader == true) {
            curl_setopt($this->_curlObj, CURLOPT_HEADER, true);
        }

        if (($resp = curl_exec($this->_curlObj)) === false) {
            $this->wpf_Logger->error($tag, 'when run Router, unexpected error :' . curl_error($this->_curlObj));
            throw new Exception(curl_error($this->_curlObj));
        }
        return $resp;
    }


    public function getActionUrlParams($url)
    {
        $urlParams = parse_url($url);
        $query = isset($urlParams['query']) ? $urlParams['query'] : [];
        $urlParams = $this->convertUrlQuery($query);
        $bodyFormat = $urlParams['body_format'];

        if (empty($bodyFormat)) {
            $bodyFormat = 'json';
        }

        return [
            'action' => $urlParams['action'],
            'body_format' => $bodyFormat,
        ];
    }

    public function convertUrlQuery($query)
    {
        if (empty($query)) {
            return [];
        }

        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }
}
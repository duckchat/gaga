<?php

use Zaly\Proto\Core\TransportDataHeaderKey;

/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 16/07/2018
 * Time: 3:33 PM
 */
class Api_Plugin_ProxyController extends \BaseController
{
    private $classNameForRequest = '\Zaly\Proto\Site\ApiPluginProxyRequest';
    private $classNameForResponse = '\Zaly\Proto\Site\ApiPluginProxyResponse';

    public function rpcRequestClassName()
    {
        return $this->classNameForRequest;
    }

    /**
     * @param \Zaly\Proto\Site\ApiPluginProxyRequest $request
     * @param \Google\Protobuf\Internal\Message $transportData
     */
    public function rpc(\Google\Protobuf\Internal\Message $request, \Google\Protobuf\Internal\Message $transportData)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        try {
            $pluginId = $request->getPluginId();
            $reqUrl = $request->getUrl();

            $requestUrl = $this->getPluginRequestUrl($reqUrl, $pluginId);
            $this->ctx->Wpf_Logger->error($tag, "proxy request: from {$reqUrl} to {$requestUrl}");


            $method = $request->getMethod();

            switch ($method) {
                case \Zaly\Proto\Core\HttpQueryType::HttpQueryGet:
                    $method = "get";
                    break;
                case \Zaly\Proto\Core\HttpQueryType::HttpQueryPost:
                    $method = "post";
                    break;
                default:
                    throw new Exception("http query invalid");
            }

            $headers = array();

            foreach ($request->getHeaders() as $key => $value) {
                $headers[$key] = $value;
            }

            $body = $request->getBody();

            $httpProxyResponse = $this->ctx->ZalyCurl->requestAndReturnHeaders($requestUrl, $method,  $body, $headers);
            $response = $this->buildApiPluginProxyResponse($httpProxyResponse["body"], $httpProxyResponse["httpCode"], $httpProxyResponse["header"]);
            $this->setRpcError($this->defaultErrorCode, "");

            $this->rpcReturn($transportData->getAction(), $response);
        } catch (Exception $ex) {
            $this->ctx->Wpf_Logger->error($tag, "error=" . $ex);
            $this->setRpcError("error.alert", $ex->getMessage());
            $this->rpcReturn($transportData->getAction(), new $this->classNameForResponse());
        }
        return;
    }


    /**
     * @param $reqUrl
     * @param $pluginId
     * @param $hostUrl
     * @return string
     */
    private function getPluginRequestUrl($reqUrl, $pluginId)
    {
        $defaultScheme = "http";

        if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
            (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
            $defaultScheme = 'https';
        }

        $defaultHost = $_SERVER['HTTP_HOST'];

        $reqUrlStruct = parse_url($reqUrl);
        if (!empty($reqUrlStruct["scheme"])) {
            $query = !empty($reqUrlStruct["query"]) ? "?" . $reqUrlStruct["query"] : "";
            $reqUrl = $reqUrlStruct["path"] . $query;
        }

        $tag = __CLASS__ . "->" . __FUNCTION__;
        try {

            $pluginProfile = $this->getPluginFromDB($pluginId);
            $pluginUrl = parse_url($pluginProfile['landingPageUrl']);

            $this->ctx->Wpf_Logger->info($tag, "get plugin proxy pluginUrl =" . json_encode($pluginUrl));

            $schema = "";
            $host = "";

            // 必须用scheme，防止用户多输//
            if (empty($pluginUrl["scheme"]) && empty($pluginUrl['host'])) {
                $schema = $defaultScheme;
                $host = $defaultHost;
            } else {
                $schema = $pluginUrl["scheme"];
                $host = $pluginUrl["host"];
                $port = empty($pluginUrl["port"]) ? "" : ":{$pluginUrl["port"]}";
                $host = $host . $port;
            }
            if(strpos($reqUrl, "/") == 0) {
                $url = "{$schema}://{$host}{$reqUrl}";
            } else {
                $url = "{$schema}://{$host}/{$reqUrl}";
            }
            return $url;
        } catch (Exception $e) {
            $this->ctx->Wpf_Logger->info($tag, " error_msg=" . $e->getMessage());
            return "";
        }
    }

    /**
     * @param $pluginId
     * @return mixed
     */
    private function getPluginFromDB($pluginId)
    {
        $pluginProfile = $this->ctx->SitePluginTable->getPluginById($pluginId);
        return $pluginProfile;
    }

    /**
     * @param $body
     * @return \Zaly\Proto\Site\ApiPluginProxyResponse
     */
    private function buildApiPluginProxyResponse($body, $httpCode, $header = array())
    {
        $response = new \Zaly\Proto\Site\ApiPluginProxyResponse();
        $response->setBody($body);
        $response->setHttpCode(intval($httpCode));

        $invalidHeaderKey = array(
            "transfer-encoding",
            "content-encoding",
            "content-length"
        );

        foreach ($header as $key => $val) {
            if (in_array(strtolower($key), $invalidHeaderKey)) {
                unset($header[$key]);
            }
        }

        $response->setHeaders($header);
        return $response;
    }
}
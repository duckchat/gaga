<?php
/**
 * WEB类程序管理器
 * 
 * 调用Router分析uri，获取Controller与Action名称，并执行。
 * 
 * @author walu
 */
class Wpf_Web
{
	public function run()
    {
		try {
			$context = new BaseCtx();
			
			$requestUri = $_SERVER['REQUEST_URI'];
			if (strpos($requestUri, $_ENV['WPF_URL_PATH_SUFFIX']) === 0) {
				$requestUri = substr($requestUri, strlen($_ENV['WPF_URL_PATH_SUFFIX']));
			}
			
			$router = $context->Wpf_Router;
			$router->parse($requestUri);
	
			$controllerName = $router->getControllerName();
			$actionName     = $router->getActionName();
			$controller     = new $controllerName($context);
			$controller->$actionName();
		} catch (Exception $e) {
			$message = sprintf("msg:%s file:%s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
			error_log("exception when run Web: " . $message);
		}
	}
}
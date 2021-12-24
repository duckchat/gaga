<?php
/**
 *
 * URL路由
 *
 *
 * 路由规则定义：
 * 
 * /router/param
 * 
 * 一、Router部分
 * 
 * 1. router包含controller与action两部分信息，用 '-' 分割，如'/user-doAdd'、'/user-password-doReset'。
 * 2. '-' 分割后，第一项一定是controller，如果多于一项，且最后一项以do开头，则其为action。
 * 3. controller默认为Index，action默认为doIndex。 
 * 4. controller对应到类名时，需要添加Controller类名后缀。
 * 
 * 例子：
 * 
 * URI			类名					方法名
 * /			IndexController		doIndex
 * /user		UserController		doIndex
 * /user-doAdd	UserController		doAdd
 * 
 * /user-blog		UserBlogController	doIndex
 * /user-blog-doAdd	UserBlogController	doAdd
 * 
 * 
 * 
 * 
 * 二、Param部分
 *
 * Param部分用于美化URL，如/blog/walu-186，解析到BlogController，同时有“name=walu&id=186”的参数。
 * 
 * Pattern格式："#id#-#PHPMailer#.html" 可以 "189-seo"，并且id=189&PHPMailer=seo
 * 在目标url格式中，用##包括起变量名即可。
 * 
 * 在Action的首行，可以调用此代码：
 * $this->parseUrlParamByPattern("#id#-#PHPMailer#.html");
 *	
 *
 * 解析成功后获得的参数，将合并到$_REQUEST全局变量中。
 *
 *
 * @author walu
 *
 */
class Wpf_Router {

	private $controller = "Index";
	private $action     = "doIndex";
	private $param      = "";


	public function parse($requestUri) {

		// 优先从Get参数解析
		if ($this->parseFromGet()) {
			return;
		}

		$pathLog = preg_split('!/+!', ltrim($requestUri, '/'));

		if (empty($pathLog[0])) {
			return;
		}

		//parse resourceName and actionName
		$resourceAndAction = explode('-', $pathLog[0]);
		if (count($resourceAndAction) == 1) {
			$this->controller = $resourceAndAction[0];
		} else {
			$testActionName = end($resourceAndAction);
			if (strpos($testActionName, 'do')===0) {
				$this->action = $testActionName;
				array_pop($resourceAndAction);
			}
			$this->controller = implode('_', $resourceAndAction);
		}

		if (isset($pathLog[1])) {
			$this->param = $pathLog[1];
		}
	}

    // 对参数做一下处理，以支持duckchat-api
    // 比如想通过 action 参数来接收客户端的请求方法
    // api.site.config，最后一个看作是方法，前面的看作是分类
    //



    // 这样的话，去完善ApiSite/ApiSiteConfigController.php，实现他的doIndex方法。
    //$_GET[$configControllerName] = "{$controller}_{$method}";
	public function parseFromGet()
    {
        $this->controller = $_ENV['WPF_URL_CONTROLLER_NAME'];

        if (false == isset($this->controller)) {
            return false;
        }
        $this->action     = $_ENV['WPF_URL_CONTROLLER_METHOD_PARAM_NAME'];
		return true;
	}

    public function getControllerName() {
		return $this->controller . $_ENV['WPF_CONTROLLER_NAME_SUFFIX'];
	}

    public function getActionName() {
		return $this->action;
	}

    public function getParam() {
		return $this->param;
	}
	/**
	 * #id#.html
	 * @param string $param
	 */
	public function parseUrlParamByPattern($paramPattern) {
		$paramPattern = preg_quote($paramPattern);
		$pattern = preg_replace('!#([a-z]+)#!i', '(?<\1>[^#]+)', $paramPattern);

		$ok = preg_match('!'.$pattern.'!', $this->getParam(), $m);
		if (!$ok) {
			return;
		}
		$numKeyCount = (count($m)-1)/2;
		for ($i=0; $i<=$numKeyCount; $i++) {
			unset($m[$i]);
		}
		return $m;
	}
}
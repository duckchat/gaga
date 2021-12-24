<?php

/**
 * 
 * 运行时上下文，提高组织代码的效率。
 * 
 * @property Wpf_Router $Wpf_Router
 * 
 * @author walu
 *
 */
abstract class Wpf_Ctx {

	private $instance = array();

	final public function __get($key) {
		if (isset($this->instance[$key])) {
			return $this->instance[$key];
		}

		$methodName = 'get' . $key;
		if (method_exists($this, $methodName)) {
			$this->instance[$key] = call_user_func(array($this, $methodName));
		} elseif (class_exists($key)) {
			$this->instance[$key] = new $key($this);
		}
		return $this->instance[$key];
	}
}

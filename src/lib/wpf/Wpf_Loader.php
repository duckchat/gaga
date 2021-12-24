<?php

class Wpf_Autoloader {

	public $pathList = array();

	public function __construct() {
		$this->pathList = array();
	}

	private $registedClasses = array();
	public function registerClass(array $classAndPath) {
        $this->registedClasses = $classAndPath;
    }

	public function addDir($dir) {
		$this->pathList[] = $dir;
	}

	public function addNamedDir($name, $dir) {
		$this->pathList[$name] = $dir;
	}

	public function classNameToPath($className) {
		$path = '';
		$lastpos = strrpos($className, "_");
		if (false !== $lastpos) {
			$path = '/' . str_replace('_', '/', substr($className, 0, $lastpos));
		}
        $lastpos = strrpos($className, "\\");
        if (false !== $lastpos) {
            $classNameArr = explode("\\", $className);
            $className = array_pop($classNameArr);
            $path = join("/", $classNameArr);
        }
        return "{$path}/{$className}.php";
	}

	public function load($className) {
        $classNamePath = "";
        if (isset($this->registedClasses[$className])) {
            $classNamePath = WPF_LIB_DIR . "/../" . $this->registedClasses[$className];
        } else {
            $classNamePath = $this->classNameToPath($className);
            foreach ($this->pathList as $dir) {
                $tmppath = $dir . $classNamePath;
                if (file_exists($tmppath)) {
                    $classNamePath = $tmppath;
                    break;
                }
            }
        }
        require_once ($classNamePath);
	}
}

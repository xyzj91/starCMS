<?php

defined('ACCESS') or exit('Access Denied');


function Loads() {
	static $loader;

    if(empty($loader)) {
		$loader = new Loader();
	}
	return $loader;
}


class Loader {
	
	private $cache = array();
//	public $coreLibPath = CORE_PATH . "Lib//";
	public $coreLibPath = "";

    public function __construct()
    {
        $this->coreLibPath = CORE_PATH . "Lib/";
    }

    function func($name) {
		global $_W;
		if (isset($this->cache['func'][$name])) {
			return true;
		}
		$file = $this->coreLibPath . $name . '.func.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['func'][$name] = true;
			return true;
		} else {
			trigger_error("Invalid Helper Function {$file}", E_USER_ERROR);
			return false;
		}
	}
	
	function model($name) {
		global $_W;
		if (isset($this->cache['model'][$name])) {
			return true;
		}
		$file = $this->coreLibPath . $name . '.mod.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['model'][$name] = true;
			return true;
		} else {
			trigger_error("Invalid Model {$file}", E_USER_ERROR);
			return false;
		}
	}
	
	function classs($name) {
		global $_W;
		if (isset($this->cache['class'][$name])) {
			return true;
		}
		$file = $this->coreLibPath . $name . '.class.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['class'][$name] = true;
			return true;
		} else {
			trigger_error("Invalid Class {$file}", E_USER_ERROR);
			return false;
		}
	}
	
	function web($name) {
		global $_W;
		if (isset($this->cache['web'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/web/common/' . $name . '.func.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['web'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Web Helper /web/common/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}
	
	function appcomm($name,$model=null) {
		global $_W;
		if (isset($this->cache['app'][$name])) {
			return true;
		}
		$model=$model?$model:APP_NAME;	
		$file = IA_ROOT . '/'.$model.'/common/' . $name . '.func.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['app'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid App Function /'.$model.'/common/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}
	function appmodel($name,$model=null) {
		global $_W;
		if (isset($this->cache['app'][$name])) {
			return true;
		}
		$model=$model?$model:APP_NAME;	
		$file = IA_ROOT . '/'.$model.'/model/' . $name . '.ctrl.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['app'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid App Function /'.$model.'/model/' . $name . '.ctrl.php', E_USER_ERROR);
			return false;
		}
	}
	
	
}

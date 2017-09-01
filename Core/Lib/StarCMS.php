<?php
namespace Core\Lib;
/**
 * StarCMS 引导类
 */
class StarCMS{
	/**
     * 应用程序初始化
     * @access public
     * @return void
     */
     static public  function start(){
     	// URL调度
     	Dispatcher::dispatch();
     }
	 /**
     * 执行应用程序
     * @access public
     * @return void
     */
    static public function exec() {
    	if(!preg_match('/^[A-Za-z](\/|\w)*$/',CONTROLLER_NAME)){ // 安全检测
            $module  =  false;
		}else{
            //创建控制器实例
            $actions = array();
			$modelDir = APP_PATH .'/'.BASE_MODEL_NAME.'/' .APP_DISCOUNT_PATH. MODULE_NAME;//模型目录
			if(!is_dir($modelDir)){
				throw new \Exception("Error: ".BASE_MODEL_NAME." ".MODULE_NAME." Not Exit", 1);
			}
			$handle = opendir($modelDir);
			if(!empty($handle)) {
				while($dir = readdir($handle)) {
					if($dir != '.' && $dir != '..' && strexists($dir, BASE_CONTROLLER_NAME.EXT)) {
						$dir = str_replace(EXT, '', $dir);
						$actions[] = $dir;
					}
				}
			}
			$controller = CONTROLLER_NAME==""?$router[BASE_CONTROLLER_NAME]['default']:CONTROLLER_NAME.BASE_CONTROLLER_NAME;
			if(!in_array($controller, $actions)) {
				throw new \Exception("Error: ".BASE_CONTROLLER_NAME." ".CONTROLLER_NAME." Not Exit", 1);
			}
			$actionPath = $modelDir."/".$controller.EXT;
			if(!is_file($actionPath)){
				throw new \Exception("Error: ".BASE_CONTROLLER_NAME." ".CONTROLLER_NAME." Not Exit", 1);
			}
			require_once $actionPath;
			$module = new $controller();
			$action = ACTION_NAME==""?$router[BASE_ACTION_NAME]['default']:ACTION_NAME;
        }
		// 获取当前操作名 支持动态路由
        if(!isset($action)){
            $action    =   ACTION_NAME.globalConfig('ACTION_SUFFIX');  
        }
		try{
            self::invokeAction($module,$action);
        } catch (Exception $e) { 
            throw new \Exception($e, 1);
        }
        return ;
		
    }
	/**
	 * 执行Action操作
	 */
	public static function invokeAction($module,$action){
		if(!preg_match('/^[A-Za-z](\w)*$/',$action)){
			// 非法操作
			throw new \Exception("Error: param Error", 1);
		}
		//执行当前操作
		$method =   new \ReflectionMethod($module, $action);//反射机制获取函数信息
		if($method->isPublic() && !$method->isStatic()) {
			$class  =   new \ReflectionClass($module);
			// 前置操作
			if($class->hasMethod('_before_'.$action)) {
				$before =   $class->getMethod('_before_'.$action);
				if($before->isPublic()) {
					$before->invoke($module);
				}
			}
			// URL参数绑定检测
			if($method->getNumberOfParameters()>0 && globalConfig("URL_PARAMS_BIND")){
				switch($_SERVER['REQUEST_METHOD']) {
					case 'POST':
						$vars    =  array_merge($_GET,$_POST);
						break;
					case 'PUT':
						parse_str(file_get_contents('php://input'), $vars);
						break;
					default:
						$vars  =  $_GET;
				}
				$params =  $method->getParameters();
				$paramsBindType     =   globalConfig('URL_PARAMS_BIND_TYPE');
				foreach ($params as $param){
					$name = $param->getName();
                    if( 1 == $paramsBindType && !empty($vars) ){
						$args[] =   array_shift($vars);
					}elseif( 0 == $paramsBindType && isset($vars[$name])){
						$args[] =   $vars[$name];
					}elseif($param->isDefaultValueAvailable()){
						$args[] =   $param->getDefaultValue();
					}else{
						throw new \Exception("Error: Param {$name} is Not Exit", 1);
					}
				}

				// 开启绑定参数过滤机制
				if(globalConfig('URL_PARAMS_SAFE')){
					$filters     =   globalConfig('URL_PARAMS_FILTER')?:globalConfig('DEFAULT_FILTER');
					if($filters) {
						$filters    =   explode(',',$filters);
						foreach($filters as $filter){
							$args   =   array_map_recursive($filter,$args); // 参数过滤
						}
					}                        
				}
				array_walk_recursive($args,'ihtmlspecialchars');
				$method->invokeArgs($module,$args);
			}else{
				$method->invoke($module);
			}
			// 后置操作
			if($class->hasMethod('_after_'.$action)) {
				$after =   $class->getMethod('_after_'.$action);
				if($after->isPublic()) {
					$after->invoke($module);
				}
			}
		}else{
			// 操作方法不是Public 抛出异常
			throw new \Exception("Error: Action is Not Public", 1);
		}
    }
	 
	 /**
     * 运行应用实例 入口文件使用的快捷方法
     * @access public
     * @return void
     */
    static public function run() {
        if(!IS_CLI){//在非cli模式下才执行
            StarCMS::start();
            StarCMS::exec();
        }
    }
}
?>
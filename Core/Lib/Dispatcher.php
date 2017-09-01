<?php
namespace Core\Lib;
use Exception;
use Common\Lib\Response;
/**
 * 完成	URL解析 路由 和调度
 */
class Dispatcher{
	/**
     * URL映射到控制器
     * @access public
     * @return void
     */
	public static function dispatch(){
		global $_GPP;
		$varPath        =   VAR_PATHINFO;
		$urlCase 		=   URL_CASE_INSENSITIVE;
		$varAddon       =   VAR_ADDON;
        $varModule      =   VAR_MODULE;
        $varController  =   VAR_CONTROLLER;
        $varAction      =   VAR_ACTION;
		if(IS_CLI){ // CLI模式下 index.php module/controller/action/params/...
            $_SERVER['PATH_INFO'] = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
        }
		 // 分析PATHINFO信息
        if(!isset($_SERVER['PATH_INFO'])) {
            $types   =  explode(',',URL_PATHINFO_FETCH);
            foreach ($types as $type){
                if(0===strpos($type,':')) {// 支持函数判断
                    $_SERVER['PATH_INFO'] =   call_user_func(substr($type,1));
                    break;
                }elseif(!empty($_SERVER[$type])) {
                    $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type],$_SERVER['SCRIPT_NAME']))?
                        substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME']))   :  $_SERVER[$type];
                    break;
                }
            }
        }
		$depr = URL_PATHINFO_DEPR;
        define('MODULE_PATHINFO_DEPR',  $depr);
        $isDiscount = false;
        $paths_module_num = 0;//模块在URL中的序号
        $clip_num = 2;
		if(empty($_SERVER['PATH_INFO'])) {
            $_SERVER['PATH_INFO'] = '';
            define('__INFO__','');
            define('__EXT__','');
        }else{
            define('__INFO__',trim($_SERVER['PATH_INFO'],'/'));
            // URL后缀
            define('__EXT__', strtolower(pathinfo($_SERVER['PATH_INFO'],PATHINFO_EXTENSION)));
            $_SERVER['PATH_INFO'] = __INFO__;
            if(!defined('BIND_MODULE') && !URL_ROUTER_ON){
                if (__INFO__ && MULTI_MODULE){ // 获取模块名
                    $paths      =   explode($depr,__INFO__,$clip_num);
                    $allowList  =   MODULE_ALLOW_LIST; // 允许的模块列表
                    $module     =   preg_replace('/\.' . __EXT__ . '$/i', '',$paths[$paths_module_num]);
                    if( empty($allowList) || (is_array($allowList) && in_array_case($module, $allowList))){
                        $_GET[$varModule]       =   $module;
                        $_SERVER['PATH_INFO']   =   isset($paths[$clip_num-1])?$paths[$clip_num-1]:'';
                    }
                    define("APP_DIST_VERSION",$isDiscount?strtolower($paths[0]):"");
                }
            }             
        }
        if($isDiscount){
		    if(!defined('APP_DIST_VERSION')){
		        define('APP_DIST_VERSION',"");//设置版本号为空
                define("APP_DISCOUNT_PATH","");//设置版本地址为空
            }else{
                define("APP_DISCOUNT_PATH",APP_DIST_VERSION."/");
            }
        }else{
            define("APP_DISCOUNT_PATH","");
        }
		// URL常量
        define('__SELF__',strip_tags($_SERVER[URL_REQUEST_URI]));
		// 获取模块名称
        define('MODULE_NAME', defined('BIND_MODULE')? BIND_MODULE : self::getModule($varModule));
		$modelDir = APP_PATH .'/'.BASE_MODEL_NAME.'/' .APP_DISCOUNT_PATH. MODULE_NAME;//模型目录
		//检测模型是否存在
        if(!is_dir($modelDir)){
		    if(APP_DEBUG){
                throw new \Exception("Error: ".BASE_MODEL_NAME." 模块 [ '".MODULE_NAME."' ] 不存在", 1);
            }else{//线上状态直接返回404
		        Response::getInstance()->sendHttpStatus(404);
                exit();
            }
		}
		if(!defined('__APP__')){
	        $urlMode        =   URL_MODEL;
	        if($urlMode == URL_COMPAT ){// 兼容模式判断
	            define('PHP_FILE',_PHP_FILE_.'?'.$varPath.'=');
	        }elseif($urlMode == URL_REWRITE ) {
	            $url    =   dirname(_PHP_FILE_);
	            if($url == '/' || $url == '\\')
	                $url    =   '';
	            define('PHP_FILE',$url);
	        }else {
	            define('PHP_FILE',_PHP_FILE_);
	        }
	        // 当前应用地址
	        define('__APP__',strip_tags(PHP_FILE));
	    }
		$moduleName    =   defined('MODULE_ALIAS')? MODULE_ALIAS : MODULE_NAME;
		define('__MODULE__',(defined('BIND_MODULE') || !MULTI_MODULE)? __APP__ : __APP__.'/'.($urlCase ? strtolower($moduleName) : $moduleName));

		if('' != $_SERVER['PATH_INFO'] && !URL_ROUTER_ON ){   // 检测路由规则 如果没有则按默认规则调度URL
            // 检查禁止访问的URL后缀
            if(URL_DENY_SUFFIX && preg_match('/\.('.trim(URL_DENY_SUFFIX,'.').')$/i', $_SERVER['PATH_INFO'])){
                Redlib\Core\Response::getInstance()->sendHttpStatus(404);
                exit;
            }
            // 去除URL后缀
            $_SERVER['PATH_INFO'] = preg_replace(URL_HTML_SUFFIX? '/\.('.trim(URL_HTML_SUFFIX,'.').')$/i' : '/\.'.__EXT__.'$/i', '', $_SERVER['PATH_INFO']);

            $depr   =   URL_PATHINFO_DEPR;
            $paths  =   explode($depr,trim($_SERVER['PATH_INFO'],$depr));
            if(!defined('BIND_CONTROLLER')) {// 获取控制器
                if(CONTROLLER_LEVEL>1){// 控制器层次
                    $_GET[$varController]   =   implode('/',array_slice($paths,0,CONTROLLER_LEVEL));
                    $paths  =   array_slice($paths, CONTROLLER_LEVEL);
                }else{
                    $_GET[$varController]   =   array_shift($paths);
                }
            }

            // 获取操作
            if(!defined('BIND_ACTION')){
                $_GET[$varAction]  =   array_shift($paths);
            }
            // 解析剩余的URL参数
            $var  =  array();
            if(URL_PARAMS_BIND && 1 == URL_PARAMS_BIND_TYPE){
                // URL参数按顺序绑定变量
                $var    =   $paths;
            }else{
                preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$var){$var[$match[1]]=strip_tags($match[2]);}, implode('/',$paths));
            }

            $_GET   =  array_merge($var,$_GET);
        }
		// 获取控制器的命名空间（路径）
        define('CONTROLLER_PATH',   self::getSpace($varAddon,$urlCase));

        // 获取控制器和操作名
        define('CONTROLLER_NAME',   defined('BIND_CONTROLLER')? BIND_CONTROLLER : self::getController($varController,$urlCase));

        define('ACTION_NAME',       defined('BIND_ACTION')? BIND_ACTION : self::getAction($varAction,$urlCase));
		 // 当前控制器的UR地址
        $controllerName    =   defined('CONTROLLER_ALIAS')? CONTROLLER_ALIAS : CONTROLLER_NAME;
        define('__CONTROLLER__',__MODULE__.$depr.(defined('BIND_CONTROLLER')? '': ( $urlCase ? parse_name($controllerName) : $controllerName )) );

        // 当前操作的URL地址
        define('__ACTION__',__CONTROLLER__.$depr.(defined('ACTION_ALIAS')?ACTION_ALIAS:ACTION_NAME));

		//保证$_REQUEST正常取值
        $_GPP = array_merge($_GET,$_POST,$_COOKIE);	// -- 加了$_COOKIE.  保证哦..
        $_GPP = htmlParamTrim($_GPP);

	}
	
	/**
     * 获得实际的控制器名称
     */
    static private function getController($var,$urlCase) {
        $controller = (!empty($_GET[$var])? parse_name($_GET[$var],true):DEFAULT_CONTROLLER);
        unset($_GET[$var]);
        if($maps = URL_CONTROLLER_MAP) {
            if(isset($maps[strtolower($controller)])) {
                // 记录当前别名
                define('CONTROLLER_ALIAS',strtolower($controller));
                // 获取实际的控制器名
                return   ucfirst($maps[CONTROLLER_ALIAS]);
            }elseif(array_search(strtolower($controller),$maps)){
                // 禁止访问原始控制器
                return   '';
            }
        }
        if($urlCase) {
            // URL地址不区分大小写
            // 智能识别方式 user_type 识别到 UserTypeController 控制器
            $controller = parse_name($controller,1);
        }
        return strip_tags(ucfirst($controller));
    }

    /**
     * 获得实际的操作名称
     */
    static private function getAction($var,$urlCase) {
        $action   = lcfirst(!empty($_POST[$var]) ?
            parse_name($_POST[$var],true) :
            (!empty($_GET[$var])?parse_name($_GET[$var],true):DEFAULT_ACTION));
        unset($_POST[$var],$_GET[$var]);
        if($maps = URL_ACTION_MAP) {
            if(isset($maps[strtolower(CONTROLLER_NAME)])) {
                $maps =   $maps[strtolower(CONTROLLER_NAME)];
                if(isset($maps[strtolower($action)])) {
                    // 记录当前别名
                    define('ACTION_ALIAS',strtolower($action));
                    // 获取实际的操作名
                    if(is_array($maps[ACTION_ALIAS])){
                        parse_str($maps[ACTION_ALIAS][1],$vars);
                        $_GET   =   array_merge($_GET,$vars);
                        return $maps[ACTION_ALIAS][0];
                    }else{
                        return $maps[ACTION_ALIAS];
                    }
                    
                }elseif(array_search(strtolower($action),$maps)){
                    // 禁止访问原始操作
                    return   '';
                }
            }
        }
        return strip_tags( $urlCase? strtolower($action) : $action );
    }
	

	/**
     * 获得控制器的命名空间路径 便于插件机制访问
     */
    static private function getSpace($var,$urlCase) {
        $space  =   !empty($_GET[$var])?strip_tags(parse_name($_GET[$var],true)):'';
        unset($_GET[$var]);
        return $space;
    }
	/**
     * 获得实际的模块名称
     */
    static private function getModule($var) {
        $module   = (!empty($_GET[$var])?parse_name($_GET[$var],true):DEFAULT_MODULE);
        unset($_GET[$var]);
        if($maps = URL_MODULE_MAP) {//检查模块映射
            if(isset($maps[strtolower($module)])) {
                // 记录当前别名
                define('MODULE_ALIAS',strtolower($module));
                // 获取实际的模块名
                return   ucfirst($maps[MODULE_ALIAS]);
            }elseif(array_search(strtolower($module),$maps)){
                // 禁止访问原始模块
                return   '';
            }
        }
        return strip_tags(ucfirst($module));
    }
}
?>
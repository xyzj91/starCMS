<?php
/**
 * 框架入口文件
 */
error_reporting( E_ALL );
define('ACCESS',TRUE);//入口验证变量
define("APP_ROOT_DIR",ROOT_PATH."/app");//应用根目录
define("COMMON_APP_PATH", APP_ROOT_DIR."/Common");//公共应用路径
define("APP_PATH", APP_ROOT_DIR."/".APP_NAME);//当前应用路径
define("COMMON_PATH",CORE_PATH."Common/");//核心文件中的Common路径//此处文件会自动加载
define("LIB_PATH",CORE_PATH."Lib/");//核心文件中的Lib路径
define("VENDOR_PATH",ROOT_PATH."/vendor/");//composer目录
define("EXT", ".php");//扩展名
define("DATA_PATH",ROOT_PATH."/data/");
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('BASE_MODEL_NAME',"Module");
define('BASE_CONTROLLER_NAME',"Controller");
define('BASE_ACTION_NAME',"Action");
// 系统信息
define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

define('APP_COMMON_PATH',APP_PATH."/Common/");

$config = require_once CORE_PATH.'/Config/Config.inc'.EXT;//加载框架配置文件
require_once LIB_PATH."/File.func".EXT;//加载全局函数
require_once LIB_PATH."/Global.func".EXT;//加载全局函数
autoLoadCommon(COMMON_PATH);//加载核心comm文件
//define('APP_DEBUG',$config['debug']);//调试模式
_LoadConfigToCommon($config);//自动定义全局配置文件

session_start();
//设置时区
if(!isset($_SESSION['timezone'])){
    $timezone = TIMEZONE;//现在将时区改为不从数据库取,直接读取配置文件 ALen
    $_SESSION['timezone']=$timezone;
    date_default_timezone_set($_SESSION['timezone']);
}
// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);
// 记录内存初始使用
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
if(MEMORY_LIMIT_ON) $GLOBALS['_startUseMems'] = memory_get_usage();


if(!IS_CLI) {
    // 当前文件名
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER['PHP_SELF']);
            define('_PHP_FILE_',    rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));
        }
    }
    if(!defined('__ROOT__')) {
        $_root  =   rtrim(dirname(_PHP_FILE_),'/');
        define('__ROOT__',  (($_root=='/' || $_root=='\\')?'':$_root));
    }
}

// 自动加载配置中的命名空间
foreach ($config["NAMESPASE"] as $k => $v){
    //注册命名空间别名
    $classLoader = new SplClassLoader($k, $v);
//    $classLoader = new SplClassLoader();
    $classLoader->register();
}


//var_dump(COMMON_APP_PATH);die;

router();
(new \Core\Lib\AppInit)->init();

function router(){
    /***************URL 路由*************/
    $_GPP = $_W = $INSTANCE = array();
    if(MAGIC_QUOTES_GPC) {
        $_GET = istripslashes($_GET);
        $_POST = istripslashes($_POST);
        $_COOKIE = istripslashes($_COOKIE);
        if($_POST){
            define('IS_POST',true);
        }
    }
    if(!defined('IS_POST')){
        if($_POST){
            define('IS_POST',true);
        }else{
            define('IS_POST',false);
        }

    }
    $_GPP = array_merge($_GET, $_POST, $_GPP);
    $_GPP = ihtmlspecialchars($_GPP);
    if(@!$_W['isajax']) {
        $input = file_get_contents("php://input");
        if (!empty($input)) {
            $__input = @json_decode($input, true);
            if (!empty($__input)) {
                $_GPP['__input'] = $__input;
                $_W['isajax'] = true;
            }
        }
        unset($input, $__input);
    }
}


Core\Lib\StarCMS::run();

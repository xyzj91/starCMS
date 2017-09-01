<?php 
//全局配置文件
defined('ACCESS') or exit('Access Denied');
//autoload 使用常量
define ( 'ADMIN_BASE', APP_PATH."/include");
define ( 'ADMIN_BASE_LIB', ADMIN_BASE . '/lib/' );
define ( 'ADMIN_BASE_CLASS', ADMIN_BASE . '/class/' );

//Smarty模板使用常量
define ( 'TEMPLATE_DIR', APP_PATH  . '/Template/' );
//define ( 'TEMPLATE_COMPILED', ADMIN_BASE . '/compiled/' );
define ( 'TEMPLATE_PLUGINS', ADMIN_BASE_LIB . 'Smarty/plugins/' );
define ( 'TEMPLATE_SYSPLUGINS', ADMIN_BASE_LIB . 'Smarty/sysplugins/' );
define ( 'TEMPLATE_CONFIGS', ADMIN_BASE . '/config/' );
define ( 'TEMPLATE_CACHE', '/tmp/cache/' );
define ('TEMPLATE_EXT',".tpl");//模板后缀名
define ("TIMEZONE","Asia/Shanghai");//时区
define("URL_COMMON",0);//普通模式
define("URL_PATHINFO",1);//PATHINFO模式
define("URL_REWRITE",2);//REWRITE模式
define("URL_COMPAT",3);//兼容模式

//global $_Config;
$_Config = [
	'FILE_MODE'             =>  655,//文件生成权限
	'URL_PATHINFO_FETCH'    =>  'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL', // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
	'URL_PATHINFO_DEPR'     =>  '/',	// PATHINFO模式下，各参数之间的分割符号
	'MULTI_MODULE'          =>  true, // 是否允许多模块 如果为false 则必须设置 DEFAULT_MODULE
	'URL_ROUTER_ON'         =>  false,   // 是否开启URL路由
	'MODULE_ALLOW_LIST'     =>   "",
	'URL_REQUEST_URI'       =>  'REQUEST_URI', // 获取当前页面地址的系统变量 默认为REQUEST_URI
	'DEFAULT_MODULE'		=>	'Index',//默认模块名称
	'DEFAULT_CONTROLLER'	=>  'Index',//默认控制器
	'DEFAULT_ACTION'	    =>  'Index',//默认操作
	'URL_MODEL'             =>  URL_REWRITE,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
	'URL_CASE_INSENSITIVE'  =>  false,   // 默认false 表示URL区分大小写 true则表示不区分大小写
	'VAR_MODULE'            =>  'm',     // 默认模块获取变量
    'VAR_ADDON'             =>  'addon',     // 默认的插件控制器命名空间变量
    'VAR_CONTROLLER'        =>  'c',    // 默认控制器获取变量
    'VAR_ACTION'            =>  'a',    // 默认操作获取变量
    'VAR_AJAX_SUBMIT'       =>  'ajax',  // 默认的AJAX提交变量
    'DEFAULT_C_LAYER'       =>  'Controller', // 默认的控制器层名称
    'APP_USE_NAMESPACE'     =>  true,    // 应用类库是否使用命名空间
    'ACTION_SUFFIX'         =>  '', // 操作方法后缀
    'URL_PARAMS_BIND'       =>  true, // URL变量绑定到Action方法参数
    'URL_PARAMS_BIND_TYPE'  =>  0, // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
    'DEFAULT_FILTER'        =>  'htmlspecialchars', // 默认参数过滤方法 用于I函数...
    'URL_PARAMS_FILTER'     =>  false, // URL变量绑定过滤
    'VAR_PATHINFO'          =>  's',    // 兼容模式PATHINFO获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_DEPR
    'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置
    'URL_HTML_SUFFIX'       =>  'html',  // URL伪静态后缀设置
    'CONTROLLER_LEVEL'      =>  1,
    'LANG_PATH'             =>  "Lang",//语言目录
    'LANGUAGE'              =>  'zh-cn',//设置语言,为空则自动判断
    'URL_MODULE_MAP'        =>  "",//模块映射
    'URL_CONTROLLER_MAP'    =>  "",
    'URL_ACTION_MAP'        =>  "",
    'APP_LOG_LEVEL'         =>  "",
    'NAMESPASE'             =>  [
        'Core'              =>  ROOT_PATH,
        'Common'            =>  APP_ROOT_DIR,
        'App'               =>  APP_PATH,
    ],//自定义命名空间
    'DATA_CONFIG_PATH'             =>  DATA_PATH."/config/",//默认config目录地址
    'DEFAULT_CONFIG_NAME'   =>  "config",//自动加载的配置文件名称
    'LOG_PATH'              =>  DATA_PATH."/log/",//错误日志文件路径
    'FILE_STORAGE_PATH'     =>  DATA_PATH."/storage/",//文件存储路径
];
return $_Config;
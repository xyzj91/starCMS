<?php
/**
 * APP初始化
 * Created by taping.
 * User: Alen
 * Date: 2017/8/15 0015
 * Time: 下午 5:11
 */

namespace Core\Lib;

date_default_timezone_set('PRC');
define('APP_CONF_DIR', APP_PATH.'/config');//应用下的conf路径
define('COMMON_CONFIG_PATH',APP_COMMON_PATH."Config/");
define('COMMON_CONTROLLER_PATH',APP_COMMON_PATH."Controller/");
define('COMMON_LIB_PATH',APP_COMMON_PATH."Lib/");
define('TIMESTAMP', time());
define('MODULE_PATH', ROOT_PATH . '/comlib/Redlib/Service/');
if(!defined("APP_DEBUG")){
    define('APP_DEBUG',false);
}
if(strtoupper(substr(PHP_OS,0,3))==='WIN'){
    define('REAL_DIR',dirname((dirname(__FILE__))));
}else{
    define('REAL_DIR','');
}
//加载应用配置文件
$appConfig = require_once DATA_PATH.DEFAULT_CONFIG_NAME.EXT;
_LoadConfigToCommon($appConfig);
use Analog\Handler\File;
use Analog\Handler\Threshold;
use Common\Lib\Response;

class AppInit
{
    public  function init(){
        //加载composer
        require_once VENDOR_PATH. '/autoload.php';
        // 错误处理
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            set_exception_handler(function (\Throwable $e) {
                $status = $e->getCode()? $e->getCode(): -8;

                // clear when > 10M
                if (@filesize(APP_LOG_FILE) > 10 * 1024 * 1024) {
                    file_put_contents(APP_LOG_FILE, '');
                }
                \Analog::debug('file:' . $e->getFile() . ' error line:' . $e->getLine() .
                    ' msg:' . $e->getMessage());
                //有定义的错误处理在cli模式下将不再exit,防止进程被kill
                if(APP_DEBUG){//未定义的错误处理
                    //采用错误代码页面来展示错误
                    $data = [
                        "error_message" => $e->getMessage(),
                        "error_where" => $e->getFile(),
                        "error_line" => $e->getLine(),
                        "error_trace" => $e->getTrace(),
                    ];
                    template("error_message",TEMPLATE_DISPLAY,$data);
                }else{
                    (new Response)->errorResponse($status, $e->getMessage());
                }
                if(!IS_CLI){
                    exit();
                }
            });
        }else{

            set_exception_handler(function (\Exception $e) {
                $status = $e->getCode()? $e->getCode(): -8;
                // clear when > 10M
                if (@filesize(APP_LOG_FILE) > 10 * 1024 * 1024) {
                    file_put_contents(APP_LOG_FILE, '');
                }
                \Analog::debug('file:' . $e->getFile() . ' error line:' . $e->getLine() .
                    ' msg:' . $e->getMessage());
                //有定义的错误处理在cli模式下将不再exit,防止进程被kill
                if(APP_DEBUG){//未定义的错误处理
                    //采用错误代码页面来展示错误
//                    Template::assign("error_message",$e->getMessage());
//                    Template::assign("error_where",$e->getFile());
//                    Template::assign("error_line",$e->getLine());
//                    Template::assign("error_trace",$e->getTrace());
//                    Template::display("error_message.tpl");
                    $data = [
                        "error_message" => $e->getMessage(),
                        "error_where" => $e->getFile(),
                        "error_line" => $e->getLine(),
                        "error_trace" => $e->getTrace(),
                    ];
                    template("error_message",TEMPLATE_DISPLAY,$data);
                }else{
                    (new Response)->errorResponse($status, $e->getMessage()." ->File:".$e->getFile()."第".$e->getLine()."行");
                }
                if(!IS_CLI){
                    exit();
                }
            });
        }
        // log init
        $analog_level = (APP_LOG_LEVEL == 'error') ? \Analog::ERROR : \Analog::DEBUG;
        \Analog::$timezone = 'PRC';
        \Analog::handler(Threshold::init(
            File::init(APP_LOG_FILE),
            \Analog::DEBUG
        ));
    }

}
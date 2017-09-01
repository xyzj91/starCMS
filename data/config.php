<?php
/**
 * Created by taping.
 * User: Alen
 * Date: 2017/8/15 0015
 * Time: 下午 3:12
 */
return $config = [
    "debug"                 => false,//调试模式
    "VENDOR_DIR"            => "",//composer地址
    "APP_LOG_FILE"          => LOG_PATH."SystemError.log",//系统运行日志地址
    "APP_LOG_LEVEL"         => "debug",//系统日志类型
    "TEMPLATE_DISPLAY"      => 0,
    "TEMPLATE_FETCH"        =>  1,
    "TEMPLATE_INCLUDEPATH"  =>  2,
    "IA_ROOT"               =>  ROOT_PATH,
    "TEMPLATE_PATH"         =>  "/Template/",//模版目录
    "THEME"                 =>  "Default",//主题名称
];
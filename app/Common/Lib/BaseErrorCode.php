<?php
/**
 * 全局错误代码类
 */
namespace Common\Lib;
define("BASE_SYSTEM",1);//系统级错误
define("BASE_CLIENT",2);//应用级错误
class BaseErrorCode{
    const Error = BASE_SYSTEM . 18001;
    const PARAM_ERROR = BASE_SYSTEM . 10000;
    const DEVICR_TYPE_ERROR = BASE_SYSTEM . 10001;
    const DATA_DOES_NOT_EXIST = BASE_SYSTEM . 10002;
    const TOKEN_CHECK_ERROR = BASE_SYSTEM . 10003;
    const YOUR_REQUEST_IS_FORBIDDEN = BASE_SYSTEM . 10004;

    private static $errCode =  array(
        self::DEVICR_TYPE_ERROR => '设备类型错误',
        self::DATA_DOES_NOT_EXIST => '获取数据失败',
        self::TOKEN_CHECK_ERROR => 'token验证失败',
        self::YOUR_REQUEST_IS_FORBIDDEN => '请求参数错误',
    );

    /**
     * 老错误码，为了兼容以前的一些错误码，现在不允许在此处新增错误码
     * @var array
     */
    private static $old_errCode =  array(
        self::Error => '未知错误',
        self::PARAM_ERROR => '参数错误',
    );

    /**
     * 合并错误代码
     */
    public static function mergeErrCode($errCode){
        foreach ($errCode as $k => $v) {
            self::$errCode[$k] = $v;
        }
        //将新老错误码合并
        $config_array = self::$errCode+self::$old_errCode;
        return $config_array;
    }
    public static function getErrMsg($err=0){
        if($err){
            return self::$errCode[$err]?self::$errCode[$err]:self::$errCode[18001];
        }
        return self::$errCode[18001];
    }
}
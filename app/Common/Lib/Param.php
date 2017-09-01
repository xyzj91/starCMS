<?php
/**
 * 参数处理类
 */
namespace Common\Lib;
class Param{
	const INT = "int";
	const TIME = "time";
	const STRING = "string";
	const NULL_ = "null";
	const BOOL = "bool";
	const OTHER = "other";
	/**
     * 参数处理函数
     * @param string $key
     * @param string $type 强制转换类型
     * @param mixed $default 默认值(为了兼容之前的代码，所以默认false返回NULL, 其他值原样返回)
     * @return mixed
     */
	public static function reqParam($key="", $type = self::OTHER, $default=false){
		global $_GPP;
		if(isset($_GPP[$key])){
            $val = $_GPP[$key];
            switch ( $type ){
                case self::INT: $val = intval($val); break;
                case self::TIME: $val = strtotime($val); break;
				case self::STRING: $val = "".$val; break;
				case self::BOOL: $val = $key?TRUE:FALSE; break;
                default:
            }
            return $val;
        }
        if ($type === self::STRING) return '';
        if ($type === self::INT) return 0;
        if ($type === self::NULL_) return NULL;
		if ($type === self::BOOL) return FALSE;
        return $default===false ? NULL : $default;
	}
}
?>
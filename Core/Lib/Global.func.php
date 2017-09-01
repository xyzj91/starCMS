<?php
//全局函数，自动初始化
use Redlib\Service\Manage\Notice;
use Redlib\Service\Manage\UserMessage;
use Redlib\Service\Manage\Log;
/**
 * 导入所需的类库 同java的Import 本函数有缓存功能
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @return boolean
 */
function import($class, $baseUrl = '', $ext=EXT) {
    static $_file = array();
    $class = str_replace(array('.', '#'), array('/', '.'), $class);
    if (isset($_file[$class . $baseUrl]))
        return true;
    else
        $_file[$class . $baseUrl] = true;
    $class_strut     = explode('/', $class);
    if (empty($baseUrl)) {
        if ('@' == $class_strut[0] || MODULE_NAME == $class_strut[0]) {
            //加载当前模块的类库
            $baseUrl = MODULE_PATH;
            $class   = substr_replace($class, '', 0, strlen($class_strut[0]) + 1);
        }elseif ('Common' == $class_strut[0]) {
            //加载公共模块的类库
            $baseUrl = COMMON_PATH;
            $class   = substr($class, 7);
        }elseif (in_array($class_strut[0],array('Think','Org','Behavior','Com','Vendor')) || is_dir(LIB_PATH.$class_strut[0])) {
            // 系统类库包和第三方类库包
            $baseUrl = LIB_PATH;
        }else { // 加载其他模块的类库
            $baseUrl = APP_PATH;
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl    .= '/';
    $classfile       = $baseUrl . $class . $ext;
    if (!class_exists(basename($class),false)) {
        // 如果类不存在 则导入类库文件
        return require_cache($classfile);
    }
    return null;
}
/**
 * 基于命名空间方式导入函数库
 * load('@.Util.Array')
 * @param string $name 函数库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @return void
 */
function load($name, $baseUrl='', $ext='.php') {
    $name = str_replace(array('.', '#'), array('/', '.'), $name);
    if (empty($baseUrl)) {
        if (0 === strpos($name, '@/')) {//加载当前模块函数库
            $baseUrl    =   ROOT_DIR;
            $name       =   substr($name, 2);
        } else { //加载其他模块函数库
            $array      =   explode('/', $name);
            $baseUrl    =   REDLIB_PATH . array_shift($array);
            $name       =   implode('/',$array);
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl       .= '/';
    require_cache($baseUrl . $name . $ext);
}
/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename) {
	static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require_once $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}
/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type=0) {
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}
/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

/**
 * 自动加载common中的类
 */
function autoLoadCommon($path=""){
    $path = $path?$path:APP_COMMON_PATH;
    $handle = opendir($path);
    if(!empty($handle)) {
        while($dir = readdir($handle)) {
            if($dir != '.' && $dir != '..') {
                $dir = $path."/".$dir;
                if(is_dir($dir)){
                    autoLoadCommon($dir);
                }else if(is_file($dir)){
                    _loads($dir);
                }
            }
        }
    }
    return ;
}
//函数对应加载
function _loads($dir){
    if($dir != '.' && $dir != '..' && strexists($dir, 'Controller.php')) {
        require_cache($dir);
    }
    if($dir != '.' && $dir != '..' && strexists($dir, '.func.php')) {
        require_cache($dir);
    }
    if($dir != '.' && $dir != '..' && strexists($dir, '.inc.php')) {
        require_cache($dir);
    }
    if($dir != '.' && $dir != '..' && strexists($dir, '.class.php')) {
        require_cache($dir);
    }
}


/**
 * 定义配置文件为全局配置
 * @param $config
 */
function _LoadConfigToCommon($config){
    foreach ($config as $k => $v){
        if(!is_array($v)){
            if(!defined($k)){
                define($k,$v);
            }
        }
    }
}
 


function istripslashes($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[stripslashes($key)] = istripslashes($value);
		}
	} else {
		$var = stripslashes($var);
	}
	return $var;
}

/**
 * 字符串转义
 */
function ihtmlspecialchars($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[htmlspecialchars($key)] = ihtmlspecialchars($value);
		}
	} else {
		$var = trim(param_filter(str_replace('&amp;', '&', htmlspecialchars($var, ENT_QUOTES))));

	}
	return $var;
}

/**
 * 去掉函数空格
 * @param $var
 * @return array|string
 */
function htmlParamTrim($var){
    if (is_array($var)) {
        foreach ($var as $key => $value) {
            $var[htmlspecialchars($key)] = htmlParamTrim($value);
        }
    } else {
        $var = trim($var);

    }
	return $var;
}

function param_filter($value){// 过滤查询特殊字符
    if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i',$value)){
        $value .= ' ';
    }
    return $value;
}

function isetcookie($key, $value, $expire = 0, $httponly = false) {
	global $_W;
	$expire = $expire != 0 ? (TIMESTAMP + $expire) : 0;
	$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
	return setcookie($_W['config']['cookie']['pre'] . $key, $value, $expire, $_W['config']['cookie']['path'], $_W['config']['cookie']['domain'], $secure, $httponly);
}


function getip() {
	static $ip = '';
	$ip = $_SERVER['REMOTE_ADDR'];
	if(isset($_SERVER['HTTP_CDN_SRC_IP'])) {
		$ip = $_SERVER['HTTP_CDN_SRC_IP'];
	} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] AS $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	}
	return $ip;
}

function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}


function scriptname() {
	global $_W;
	$_W['script_name'] = basename($_SERVER['SCRIPT_FILENAME']);
	if(basename($_SERVER['SCRIPT_NAME']) === $_W['script_name']) {
		$_W['script_name'] = $_SERVER['SCRIPT_NAME'];
	} else {
		if(basename($_SERVER['PHP_SELF']) === $_W['script_name']) {
			$_W['script_name'] = $_SERVER['PHP_SELF'];
		} else {
			if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $_W['script_name']) {
				$_W['script_name'] = $_SERVER['ORIG_SCRIPT_NAME'];
			} else {
				if(($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
					$_W['script_name'] = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $_W['script_name'];
				} else {
					if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
						$_W['script_name'] = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
					} else {
						$_W['script_name'] = 'unknown';
					}
				}
			}
		}
	}
	return $_W['script_name'];
}


function utf8_bytes($cp) {
	if ($cp > 0x10000){
				return	chr(0xF0 | (($cp & 0x1C0000) >> 18)).
		chr(0x80 | (($cp & 0x3F000) >> 12)).
		chr(0x80 | (($cp & 0xFC0) >> 6)).
		chr(0x80 | ($cp & 0x3F));
	}else if ($cp > 0x800){
				return	chr(0xE0 | (($cp & 0xF000) >> 12)).
		chr(0x80 | (($cp & 0xFC0) >> 6)).
		chr(0x80 | ($cp & 0x3F));
	}else if ($cp > 0x80){
				return	chr(0xC0 | (($cp & 0x7C0) >> 6)).
		chr(0x80 | ($cp & 0x3F));
	}else{
				return chr($cp);
	}
}


/**
 * aes解密
 */
function aes_decode($message, $encodingaeskey = '', $appid = '') {
	$key = base64_decode($encodingaeskey . '=');

	$ciphertext_dec = base64_decode($message);
	$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
	$iv = substr($key, 0, 16);

	mcrypt_generic_init($module, $key, $iv);
	$decrypted = mdecrypt_generic($module, $ciphertext_dec);
	mcrypt_generic_deinit($module);
	mcrypt_module_close($module);
	$block_size = 32;

	$pad = ord(substr($decrypted, -1));
	if ($pad < 1 || $pad > 32) {
		$pad = 0;
	}
	$result = substr($decrypted, 0, (strlen($decrypted) - $pad));
	if (strlen($result) < 16) {
		return '';
	}
	$content = substr($result, 16, strlen($result));
	$len_list = unpack("N", substr($content, 0, 4));
	$contentlen = $len_list[1];
	$content = substr($content, 4, $contentlen);
	$from_appid = substr($content, $xml_len + 4);
	if (!empty($appid) && $appid != $from_appid) {
		return '';
	}
	return $content;
}
/**
 * aes 加密
 */
function aes_encode($message, $encodingaeskey = '', $appid = '') {
	$key = base64_decode($encodingaeskey . '=');
	$text = random(16) . pack("N", strlen($message)) . $message . $appid;

	$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
	$iv = substr($key, 0, 16);

	$block_size = 32;
	$text_length = strlen($text);
		$amount_to_pad = $block_size - ($text_length % $block_size);
	if ($amount_to_pad == 0) {
		$amount_to_pad = $block_size;
	}
		$pad_chr = chr($amount_to_pad);
	$tmp = '';
	for ($index = 0; $index < $amount_to_pad; $index++) {
		$tmp .= $pad_chr;
	}
	$text = $text . $tmp;
	mcrypt_generic_init($module, $key, $iv);
		$encrypted = mcrypt_generic($module, $text);
	mcrypt_generic_deinit($module);
	mcrypt_module_close($module);
		$encrypt_msg = base64_encode($encrypted);
	return $encrypt_msg;
}

/**
 * 字符串格式化过滤，防止XSS攻击
 */
function ihtml_entity_decode($str) {
	$str = str_replace('&nbsp;', '#nbsp;', $str);
	return str_replace('#nbsp;', '&nbsp;', html_entity_decode(urldecode($str)));
}

function iarray_change_key_case($array, $case = CASE_LOWER){
	if (!is_array($array) || empty($array)){
		return array();
	}
	$array = array_change_key_case($array, $case);
	foreach ($array as $key => $value){
		if (empty($value) && is_array($value)) {
			$array[$key] = '';
		}
		if (!empty($value) && is_array($value)) {
			$array[$key] = iarray_change_key_case($value, $case);
		}
	}
	return $array;
}

/**
 * 字符串处理，防止sql注入
 * @param $values
 * @param string $type
 * @return array|string
 */
function strip_gpc($values, $type = 'g') {
	$filter = array(
		'g' => "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)",
		'p' => "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)",
		'c' => "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)",
	);
	if (!isset($values)) {
		return '';
	}
	if(is_array($values)) {
		foreach($values as $key => $val) {
			$values[addslashes($key)] = strip_gpc($val, $type);
		}
	} else {
		if (preg_match("/".$filter[$type]."/is", $values, $match) == 1) {
			$values = '';
		}
	}
	return $values;
}



//调试输出
function dump() 
{
	$args = func_get_args();
	foreach ($args as $val ) 
	{
		echo '<pre style="color: red">';
		var_dump($val);
		echo '</pre>';
	}
	exit();
}

/**
 * 自动加载对应模块的Common
 */
function autoLoadInclude($path=""){
	$classsPath = ROOT_DIR."/include/class";
	$libPath = ROOT_DIR."/include/lib";
	$classHandle = opendir($classsPath);
	$libHandle = opendir($libPath);

	if(!empty($classHandle)) {
		while($dir = readdir($classHandle)) {
			if($dir != '.' && $dir != '..') {
				$dir = $classsPath."/".$dir;
				if(is_file($dir)){
					_loads($dir);
				}
			}
		}
	}
	if(!empty($libHandle)) {
		while($dir = readdir($libHandle)) {
			if($dir != '.' && $dir != '..') {
				$dir = $libPath."/".$dir;
				if(is_file($dir)){
					_loads($dir);
				}
			}
		}
	}
//	return ;
}


/**
 * 加载配置文件
 */
function loadConfig($fileName,$param=null){
	if(!$fileName)return FALSE;
	$config = require_once DATA_CONFIG_PATH.$fileName.EXT;
	return $param?$config[$param]:$config;
}

/**
 * 加载模块  *******现在不允许直接加载模块******
 */
function loadModel($modelName){
	return false;
}
/**
 * 实例化验证类 格式：[模块名/]验证器名
 * @param string $name         资源地址
 * @param string $layer        验证层名称
 * @param bool   $appendSuffix 是否添加类名后缀
 * @param string $common       公共模块名
 * @return Object|false
 * @throws ClassNotFoundException
 */
function validate($name = '', $layer = 'Validate', $appendSuffix = false, $common = 'common')
{
    global $INSTANCE;
    $name = $name ?: "";
    if (empty($name)) {
        return new Validate;
    }
    $guid = $name . $layer;
    if (isset($INSTANCE[$guid])) {
        return $INSTANCE[$guid];
    }
    if (strpos($name, '\\')) {
        $class = $name;
    } else {
        if (strpos($name, '/')) {
            list($module, $name) = explode('/', $name);
        } else {
            $module = MODULE_NAME;
        }
        $class = parseClass($module, $layer, $name, $appendSuffix);
    }
    if (class_exists($class)) {
        $validate = new $class;
    } else {
        $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);
        if (class_exists($class)) {
            $validate = new $class;
        } else {
            throw new Exception('class not exists:' . $class, 1);
        }
    }
    $INSTANCE[$guid] = $validate;
    return $validate;
}

/**
 * 解析应用类的类名
 * @param string $module 模块名
 * @param string $layer  层名 controller model ...
 * @param string $name   类名
 * @param bool   $appendSuffix
 * @return string
 */
function parseClass($module, $layer, $name, $appendSuffix = false)
{
    $name  = str_replace(['/', '.'], '\\', $name);
    $array = explode('\\', $name);
    $class = parse_name(array_pop($array), 1) . (true || $appendSuffix ? ucfirst($layer) : '');
    $path  = $array ? implode('\\', $array) . '\\' : '';
    return "Redlib\\". $layer . '\\' . $path . $class;
}

/**
 * 根據參數生成url
 * @param $param array ['path'=>'modulue/controller/action','back_url'=>true,'param1'=>xxxx,'param2'=>xxxx]
 */
function URL($param=[]){
    $path = $param['path'];
    if(!$path){
        return "";
    }
    if($param['back_url']){
        $param['back_url'] = base64_encode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    }
    unset($param['path']);
    $depr   =   URL_PATHINFO_DEPR;
    $urlMode =  URL_MODEL;
    $path = strtolower($path);
    $pathArr = array_filter(explode($depr,$path));
    if(count($pathArr)>2){
        $path = implode($depr,$pathArr);
    }elseif(count($pathArr)>1){
        $path = parse_name(MODULE_NAME).$depr.implode($depr,$pathArr);
    }else{
        $path = parse_name(MODULE_NAME).$depr.parse_name(CONTROLLER_NAME).$depr.implode($depr,$pathArr);
    }
    $tmpstr = "";
    $a = 0;
    foreach ($param as $k =>  $v ){
        if($urlMode == URL_COMPAT||$urlMode == URL_REWRITE){// 兼容模式判断
            if($a==0){
                $tmpstr = '?'.$k."=".$param[$k];
            }else{
                $tmpstr.="&".$k."=".$param[$k];
            }
        }else {
            $tmpstr=$depr.$k.$depr.$param[$k];
        }
        $a++;
    }
    $path = $tmpstr?$depr.$path.$tmpstr:$depr.$path;
    return $path;
}

/**
 * 获取权限验证地址
 * @return string
 */
function getAuthUrl(){
    $depr   =   URL_PATHINFO_DEPR;
    return $depr.parse_name(MODULE_NAME).$depr.parse_name(CONTROLLER_NAME).$depr.parse_name(ACTION_NAME);
}

/**
 * 验证手机号码是否正确
 * @param $mobile
 * @return bool
 */
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,3,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}


/**
 * json输出
 * @param $data
 */
function show_json($data){
    //允许跨域，便于app端缓存模板
    header('Access-Control-Allow-Origin:*');
    exit(json_encode($data));
}


/**
 * 获取到指定时间的起始和结束时间戳
 */
function getDayStarEndTime($time){
    $t = is_int($time)?$time:strtotime($time);
    $data = [];
    $data[] = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
    $data[] = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
    return $data;
}


function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key != '' ? $key : $GLOBALS['_W']['config']['setting']['authkey']);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }

}

function sizecount($size) {
    if($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' GB';
    } elseif($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' MB';
    } elseif($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' KB';
    } else {
        $size = $size . ' Bytes';
    }
    return $size;
}


function array2xml($arr, $level = 1) {
    $s = $level == 1 ? "<xml>" : '';
    foreach ($arr as $tagname => $value) {
        if (is_numeric($tagname)) {
            $tagname = $value['TagName'];
            unset($value['TagName']);
        }
        if (!is_array($value)) {
            $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
        } else {
            $s .= "<{$tagname}>" . array2xml($value, $level + 1) . "</{$tagname}>";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
    return $level == 1 ? $s . "</xml>" : $s;
}

function xml2array($xml) {
    if (empty($xml)) {
        return array();
    }
    $result = array();
    $xmlobj = isimplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    if($xmlobj instanceof SimpleXMLElement) {
        $result = json_decode(json_encode($xmlobj), true);
        if (is_array($result)) {
            return $result;
        } else {
            return '';
        }
    } else {
        return $result;
    }
}

/**
 * 数据存储(文件形式)
 * @param $key key
 * @param string $val val
 * @param bool $del 是否是删除操作，否则为返回数据
 * @param int $lost_time 失效时间
 * @return array|bool|mixed|void
 */
function file_storage($key,$val="",$del=false,$lost_time=0){
    $ext=".storage";
    $basekey = "storge_";
    $file = $basekey.$key.$ext;
    $path = FILE_STORAGE_PATH;
    if(!is_dir($path)){//文件夹不存在
        @mkdir($path,0777);//创建文件夹
    }
    $data = ["key"=>$key,"val"=>$val];
    if($lost_time){
        $data["lost_time"] = time()+intval($lost_time);
    }
    //删除文件
    if($del&&$val==""){
        unlink($path.$file);//删除文件
        return;
    }elseif ($val==""){//否则为返回数据
        $temp = @file_get_contents($path.$file);
        if(!$temp){
            return "";
        }
        $data = unserialize($temp);
        unset($temp);
        //如果超过失效时间
        if(isset($data["lost_time"])&&$data["lost_time"]<time()){
            return "";
        }
        return $data['val'];
    }
    //插入数据
//    $storageFile = fopen($path.$file, "w") or die("write cookie error");
//    fwrite($storageFile,serialize($data));//写入文件
//    fclose($storageFile);
    writefile($path,$file,serialize($data));//写入文件
    return true;
}

/**
 * 写入文件
 * @param $path 文件地址
 * @param $file 文件
 * @param $data 数据
 */
function writefile($path,$file,$data,$type="w"){
    //插入数据
    $storageFile = fopen($path.$file, $type) or die("write data error");
    fwrite($storageFile,$data);//写入文件
    fclose($storageFile);
}

/**
 * 写入日志文件
 * @param $filename
 * @param $data
 */
function writeLogFile($filename,$data){
    $ext=".log";
    $basekey = "log_";
    $file = $basekey.$filename.$ext;
    $path = LOG_PATH;
    if(!is_dir($path)){//文件夹不存在
        mkdir($path,0777);//创建文件夹
    }
    writefile($path,$file,$data,"a");//写入文件
}

/**
 * 写入日志
 */
function dumplog()
{
    $path = LOG_PATH;
    $ext = ".log";
    try{
        $args = func_get_args();
        $filename = array_shift($args);
        $path = $path.date("Y-m-d",time())."/";
        if(!is_dir($path)){
            mkdir($path,"0777",true);//创建文件夹
            chmod($path,0777);//设置权限
        }
        $myfile = @fopen("{$path}{$filename}{$ext}", "a");
        @fwrite($myfile, "************************************\r\n");
        foreach ($args as $val )
        {
            if(gettype($val)!="string"){
                $val = json_encode($val);
            }
            @fwrite($myfile, $val."\r\n");
        }
        @fwrite($myfile, "***********************************************\r\n");
        @fclose($myfile);
    }catch (Exception $e){
        dumplog("errorlog","{$path}{$filename} 写入失败");
    }
}

<?php
/**
 * 数据基类对象
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 下午 3:14
 */

namespace Common\Bean;



abstract class Bean
{
    protected $param_config = [];//参数配置
    private $strict_mode;//严格模式


    /**
     * Bean constructor.
     * @param array|string $obj_param 初始化对象所用的值
     * @param bool $strict_mode 参数匹配模式 严格模式 为true则严格验证$obj_param中的参数(多一个就会抛出异常),为false则只会取$obj_param中对象所对应的参数
     */
    public function __construct($obj_param,$strict_mode=true)
    {
        //如果不是数组则将数据转换为数组
        if(!is_array($obj_param)){
            $obj_param = self::jsonToArray($obj_param);
        }
        $this->setStrictMode($strict_mode);//设置参数匹配模式
        //检查必传参数
        $this->checkMustParam($obj_param);
        $this->initParam($obj_param);//初始化参数
    }





    /**
     * 获取整体的配置项
     * @param null $key
     * @return array|mixed
     */
    public function getParamConfig($key=null)
    {
        return $key?$this->param_config[$key]:$this->param_config;
    }

    /**
     * 添加必要的参数声明,此项里面存在的项才会被toArray
     * @param array $all_param
     */
    public function setParamConfig(array $all_param)
    {
        $this->param_config = array_merge($this->getParamConfig(),$all_param);
    }





    /**
     * 检查必传参数 并为属性赋值
     * @param $obj_param
     * @throws \Exception
     */
    protected function checkMustParam($obj_param){
        if(!is_array($obj_param)){
            throw new \Exception("Create Object ".get_class($this)." Error Param Must Be Array! Your Param: ".$obj_param);
        }
        $strictMode = $this->getStrictMode();//参数匹配模式
        if($strictMode){//严格匹配模式
            $this->strictParamModel($obj_param);
        }else{//普通模式
            $this->normalParamModel($obj_param);
        }
    }

    /**
     * 普通参数匹配模式
     * @param $obj_param
     * @throws \Exception
     */
    protected function normalParamModel($obj_param){
        $param_list = $this->getParamConfig();
        foreach ($param_list as $key => $value){
            $must = false;//是否必须
            //如果配置是数组,则认为里面有多项配置,则从中分别取出参数
            if(is_array($value)){
                $must = $value["must"];//是否必须
            }else{
                $must = $value;
            }
            //如果指定参数不存在,则抛出异常
            if(@$must&&$obj_param[$key]===null){
                throw new \Exception("{$key} Not Null!");
            }
        }
    }

    /**
     * 严格参数匹配模式
     * @param $obj_param
     * @throws \Exception
     */
    protected function strictParamModel($obj_param){
        foreach ($obj_param as $key => $value){
            $param_conf = $this->getParamConfig($key);//参数配置
            $must = false;//是否必须
            //如果配置是数组,则认为里面有多项配置,则从中分别取出参数
            if(is_array($param_conf)){
                $must = $param_conf["must"];//是否必须
            }else{
                $must = $this->getParamConfig($key);
            }
            //如果指定参数不存在,则抛出异常
            if(@$must&&$value===null){
                throw new \Exception("{$key} Not Null!");
            }
        }
    }

    /**
     * 初始化值(没有值则设置一个默认值)
     * @param $obj_param
     */
    private function initParam($obj_param){
        $param_list = $this->getParamConfig();
        foreach ($param_list as $key => $value){
            $default = "";//默认参数
            //如果配置是数组,则认为里面有多项配置,则从中分别取出参数
            if(is_array($value)){
                $default = $value["default"];//默认参数
            }
            //如果参数不存在,则设置一个默认参数
            @$val = isset($obj_param[$key])&&$obj_param[$key]!==null?$obj_param[$key]:$default;

            //自动将参数设置到对象中
            $this->autoSet($key,$val);
        }
    }

    /**
     * @return mixed
     */
    public function getStrictMode()
    {
        return $this->strict_mode;
    }

    /**
     * @param mixed $strict_mode
     */
    public function setStrictMode($strict_mode)
    {
        $this->strict_mode = $strict_mode;
    }

    /**
     * 自动设置参数
     * @param $key
     * @param $val
     * @throws Exception
     */
    public function autoSet($key,$val){
        $func_name = "set".parse_name($key,true);
        if(!method_exists($this,$func_name)){
            $path = get_class($this);
            throw new \Exception("Function {$func_name} Not Exists, {$path} !");
        }else{
            call_user_func(array($this,$func_name),$val);
        }
    }

    /**
     * 自动获取参数
     * @param $key
     * @return mixed|null
     * @throws Exception
     */
    public function autoGet($key){
        $func_name = "get".parse_name($key,true);
        if(!method_exists($this,$func_name)){
            $path = get_class($this);
            throw new \Exception("Function {$func_name} Not Exists {$path}!");
        }else{
            return call_user_func(array($this,$func_name));
        }
        return null;
    }


    /**
     * 将基类对象转换成数组
     * @param array $key_array 需要获取的参数列表,为空则获取所有参数
     * @param array $un_inArray 不需要出现在结果中的参数
     * @return array
     */
    public function toArray($key_array=[],$un_inArray=[]){
        $tmp = [];
        $key_list = $key_array?$key_array:$this->getParamConfig();
        foreach ($key_list as $key => $must){
            //如果存在在不需要出现的列表中
            if(in_array($key,$un_inArray)){
                continue;
            }
            //如果数组的key为数字则证明为一维数组,则直接取值作为key
            if(is_numeric($key)){
                $key = $must;
            }
            $getParam = $this->autoGet($key);
            $param_conf = $this->getParamConfig($key);//参数配置
            $default = "";
            if(is_array($param_conf)){
                $default = $param_conf["default"];//默认参数
            }
            $getParam = $getParam?$getParam:$default;//设置默认参数
            //如果当前返回值为对象,则调用其中的toArray方法转为数组先
            if(is_object($getParam)&&strpos(get_class($getParam),"Bean")){
                $getParam = $getParam->toArray();
            }elseif(is_object($getParam)){
                $getParam = $getParam;//其他对象先不处理了,防止mongoID类型被转为字符串
            }
            //对mongo id进行特殊处理
            if($key=="_id"&&!$getParam){
                continue;
            }
            $tmp[$key] = $getParam;
        }
        return $tmp;
    }

    /**
     * 将基类对象转换为Json字符串
     * @param array $key_array 需要获取的参数列表,为空则获取所有参数
     * @return string
     */
    public function toJsonString($key_array=[]){
        return self::arrayToJson($this->toArray($key_array));
    }

    /**
     * 将数据转换为array
     * @param $json
     * @return bool|mixed
     */
    public static function jsonToArray($json){
        return json_decode($json,true)?json_decode($json,true):false;
    }

    /**
     * 将数据转换为json
     * @param $array
     * @return string
     */
    public static function arrayToJson($array){
        return json_encode($array);
    }

    /**
     * 初始化Bean类
     * @param $class 类
     * @param array $param 参数
     * @param bool $strictMode 是否严格模式
     * @param string $child_class 子类(listBean类中需要穿此参数)
     * @return mixed|array
     */
    public static function createBean($class,$param=[],$strictMode=true,$child_class=""){
        if($child_class){
            return instanceClass($class,[$param,$strictMode,$child_class]);
        }
        if($param){
            if(!is_object($param)){
                return instanceClass($class,[$param,$strictMode]);
            }else{
                return $param;
            }
        }
        return [];
    }




    /**
     * * 根据参数自动生成class
     * @param $class_name 生成的类名称
     * @param $data 参数
     * @return string
     */
    public static function generateClass($class_name,$data){
        $private_param="";
        $param_config="";
        $function_list = "";
        foreach ($data as $k => $v){
            $default_type = "string";//默认类型
            switch (gettype($v)){
                case "integer":
                    $default=0;
                    $default_type = "int";
                    break;
                case "double":
                    $default=0.00;
                    $default_type = "float";
                    break;
                case "array":
                    $default="[]";
                    $default_type = "array";
                    break;
                case "boolean":
                    $default="false";
                    $default_type = "boolean";
                    break;
                case "object":
                    $default_type = null;
                    $default = '""';
                    break;
                default:
                    $default_type = "string";
                    $default = '""';
                    break;
            }
            $private_param.="
    private \${$k};";

            $param_config.="
            \"{$k}\"=>[
                \"must\"=>false,
                \"default\"=>{$default}
            ],";

            $function_name = parse_name($k,true);
            $set = $default_type===null?"":"settype(\${$k},'{$default_type}');";
            $return_type = $default_type===null?"mixed":$default_type;

            $function_list .= "
    /**
     * 获取参数
     * @return {$return_type}
     */
    public function get{$function_name}()
    {
        return \$this->{$k};
    }

    /**
     * 设置参数 参数类型强制转换
     * @param {$return_type} \${$k}
     */
    public function set{$function_name}(\${$k})
    {
         {$set}   
         \$this->{$k} = \${$k};
    }
            ";
        }

        return self::createBeanClass($class_name,$private_param,$param_config,$function_list);
    }

    private static function createBeanClass($bean_name,$private_param,$param_config,$function_list){
        $date = date("Y/m/d H:i:s",time());
        $function_body = "
&lt;?php
/**
 * 此类为系统自动生成,实际使用中请根据实际情况调整下
 * Created by taping.
 * User: System Auto Create
 * Date: {$date}
 */
 
namespace Redlib\Bean;

class {$bean_name} extends Bean
{
    {$private_param}
    
    public function __construct(\$data, \$strict_mode = true)
    {
        //设置参数
        self::setParamConfig([
            {$param_config}
        ]);
        parent::__construct(\$data, \$strict_mode);
    }
    
    {$function_list}
 }";

        return $function_body;
    }





}
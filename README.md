#StarCMS介绍
##1.目录结构：
    根目录
       -comlib
    	-Redlib
    	    -Core
    	        -Init.inc.php  框架入口文件 常量初始化，全局函数加载，命名空间自动注册，错误监听
    	        -StarCMS.php   核心文件 控制路由
    	        -Dispatcher.php 框架路由类
    	        -Validate      自动验证类
    	      	-Common
    		     -Config
    		          -Config.inc.php 全局配置文件（框架级）
    		     -Lib
    	                  -File.func.php 全局文件 （框架级） 文件和目录的添加删除
    		          -Global.func.php 全局函数 （框架级）
    		     -Class 全局类目录（框架级）自动加载
    			Test.class.php
        -Validate 自动验证类目录
            -UserValidate.php  验证规则，一个模型对应一个规则类
    
       -manage 应用目录
            -index.php  应用入口文件
    	-Common   公共模块（应用级）
    	     -Config 配置文件目录 （应用级）
    	     -Controller 控制器（应用级）
    	     -Lib  依赖库 （应用级）
            -Module  模块目录
    	    -Aaa 模块名称
    		-TestController.php 控制器
    
    	-Template 应用模版目录
    	    -Aaa 模块名称
          		-Test 控制器名称
    		     -index.tpl模版名称
    		     
    		     
##2.全局函数

按照當前URL模式生成完整URL鏈接 modulue不設置默認為當前模塊名,controller不設置默認為當前控制器

    URL(['path'=>'modulue/controller/action','param1'=>xxxx,'param2'=>xxxx])
    
    在模板中可以這樣使用
    
    <{URL path='/saler/pre_sale' data_id=$val._id }>

打印输出
    
    dump(["Test"=>"test"]);  
        
获取配置文件中的配置      
 
    loadConfig("ConfigFileName",<Key>);
    
获取或者设置全局配置文件中的配置项值,当value不为空时则为设置

    globalConfig("key","val");

参数过滤函数，防止XSS攻击
    
    ihtmlspecialchars($param);

获取当前客户端的IP
    
    getip();

查询字符串是否存在

    strexists($string, $find);

AES解密

    aes_decode($message, $encodingaeskey = '', $appid = '');
    
AES加密

    aes_encode($message, $encodingaeskey = '', $appid = '');

将数组的键转换成大写或者小写
    
    iarray_change_key_case($array, $case = CASE_LOWER);

    
    
获取字符串末尾出现的数字
    
    getLastNum($str);
    
增加mongodb模糊搜索时字符串转义
    
    escapeStr($targetStr, $charList = '.?*,/()[]{}<>-');
    

    
数组字段过滤 $data 源数组  $field 保留的KEYS
    
    filterField( $data, $field = [] );
    
模版消息推送 pushNoticeTemplateToList

    /**
     * 将模版推送消息写入到队列中异步推送
     * @param $to_user_id 用户ID
     * @param string $tpl_name 模版名称
     * @param array $replace_data 模版变量替换数组
     * @param array $param 透传内容
     * @param array $messageSetting 扩展内容
     * @param array $messageFactoryName 消息发送对象名 默认为Notice::SALER_NOTICE 可选值Notice::SALER_NOTICE (用户消息推送),Notice::SALESMAN_NOTICE(销售系统消息推送)
     */
    function pushNoticeTemplateToList($to_user_id,$tpl_name="",$replace_data=[],$param=[],$messageSetting=[],$messageFactoryName=Notice::SALER_NOTICE)
 
普通消息推送 pushNoticeToList

    /**
     * 将推送消息写入到队列中异步推送
     * @param $to_user_id 用户ID
     * @param string $message 消息内容
     * @param array $param 透传内容
     * @param array $message_setting 扩展内容
     * @param array $messageFactoryName 消息发送对象名 默认为Notice::SALER_NOTICE 可选值Notice::SALER_NOTICE (用户消息推送),Notice::SALESMAN_NOTICE(销售系统消息推送)
     */
    function pushNoticeToList($to_user_id,$message="",$param = [],$message_setting=[],$messageFactoryName=Notice::SALER_NOTICE)
    
发送短信 sendMessageToList

    /**
     * 将短信写入到队列中
     * @param $mobile 手机号码
     * @param string $tpl_name 短信模版名称
     * @param array $tpl_param 短信模版替换内容
     * @param $messageFactoryName 消息发送对象名 默认为Notice::SALER_NOTICE 可选值Notice::SALER_NOTICE (用户消息推送),Notice::SALESMAN_NOTICE(销售系统消息推送)
     */
    function sendMessageToList($mobile,$tpl_name="", $tpl_param = [],$messageFactoryName=Notice::SALER_NOTICE)

    
格式化价格
    
    formatPrice($price);
    
格式化重量
    
    formatWeight($weight);
    
数据格式转换
    
    tranCode($str);
    
过滤掉emoji表情
    
    filterEmoji($str);
    
处理正则
    
    searchRegexData($str);
    
英文字符串截取
    
    cut_str($str, $start, $len);
    
中文字符串截取
    
    getOfFirstIndex($str, $start);
    
通过get请求获取epet接口数据
    
    getEpetApiData($param_arr, $url, $type = "get");
    
通信加密
    
    setToken($param, $key = 'd9f5l38hk29h182j32kl');
    
get请求网络
    
    getRequest($url, $timeout = 60);
    
post 请求网络
    
    postRequest($url, $data, $timeout = 60);
    
get请求 $param_arr 请求参数  请求url
    
    getHttpData($param_arr, $url);
    
将数据格式整理之后导出为csv格式 
    
    /**
     * 将数据格式整理之后导出为csv格式
     * @param  string $filename   文件名
     * @param  array $header_arr 文件头
     * @param  array $data       数据数组
     * @return [type]             [description]
     */
    exportCsvData($filename, $header_arr = '', $data);
    
在浏览器中导出csv
    
    exportHtmlCsv($filename,$string);
    
获取域名
    
    get_api($api_key);
    
获取中文字符拼音首字母
    
    getFirstCharter($str);
    
电话号码加*
    
    mobileChange($mobile);
    
地址加*
    
    addressChange($address);
    
去掉字符串空格
    
    clearStringSpace($str);
    
获取字符串最后一位
    
    endchar($str);
    
获取当前微秒数
    
    microtime_float();
    
是否是json数据格式判断
    
    is_json($string);
    
手机号码是否正确 isMobile

    isMobile($mobile)
    
###位置信息处理
Redlib\Service\Common\GeoLocation 类专门用来处理位置信息

####inCircle
求出一个点是否在圆圈内

     /**
         * 求出一个点是否在圆圈内
         * @param $lng float 经度
         * @param $lat float 纬度
         * @param $circle array("lat"=>xxx,"lng"=>xxx,"radius"=>2000) 参考中心点坐标经纬度和参考圆圈半径
         * @return bool
         */
        public function inCircle($lng,$lat, $circle)
        
####getBaiduLoc
将坐标转换为百度坐标

    /**
         * 将坐标转换为百度坐标
         * @param $points array(array("lat"=>xxx,"lng"=>xxx)) 二维坐标组
         * @return array
         */
        public function getBaiduLoc($points)

####getAddressLocation
反地理编码

    /**
         * 反地理编码
         * @param $address_detail String 地址
         * @param $point  坐标接收参数
         * @param $map_type 坐标类型
         *
         *
         * @param int $timeout 请求超时时间
         *
         * @return bool
         */
        public function  getAddressLocation($address_detail, &$point,$map_type = "baidu",$timeout = self::OUT_TIME)
    
####getAddressInfoByQqApis
根据地址获取地址详细信息 腾讯地图

    /**
         * 根据地址获取地址详细信息 腾讯地图
         * @param $address_detail  String 地址
         * @param int $timeout 超时时间
         * @return bool|mixed
         */
        public function getAddressInfoByQqApis($address_detail, $timeout = self::OUT_TIME)
        
####getAddressInfoFromPoint
根据经纬度获取位置详细信息 腾讯地图

     /**
         * 根据经纬度获取位置详细信息 腾讯地图
         * @param $point array("lat"=>xxx,"lng"=>xxx); 坐标
         * @param int $timeout 超时时间
         * @return bool
         */
        public function getAddressInfoFromPoint($point,$timeout = self::OUT_TIME)

###坐标位置hash值(GeoHash)计算
Redlib\Service\Common\GeoHash;专门用来处理geohash

####decode
geoHash 解码

    /**
    	 * geoHash 解码 
    	 * $hash String geoHash
    	 */
    	public function decode($hash)
    	
####encode
geoHash 编码

    /**
    	 * geoHash 编码
    	 * $lat float 纬度值
    	 * $long float 经度值
    	 * return string
    	 */
    	public function encode($lat, $long)

####neighbors
获取当前geohash附近的8个位置的hash 来拼成一个完整的位置

    /**
    	 * 获取当前geohash附近的8个位置的hash 来拼成一个完整的位置
    	 * $srcHash Hash码 $precision精度
    	 * 精度9 （范围：10平方米） 8 （范围：350平方米）  7（ 范围：0.1平方公里）6（ 范围：3.87平方公里）5（ 范围：110.25平方公里）4（ 范围：3962.87平方公里）3（ 范围：112026.43平方公里）
    	 */
    	public function neighbors($srcHash,$precision=9) 
    	
    	
####getDistance
根据两点间的经纬度计算距离

    /** 
    	*  根据两点间的经纬度计算距离 
    	*  @param float $latitude1 纬度值1 
    	*  @param float $longitude1 经度值 1
         * *  @param float $latitude2 纬度值2
         *  @param float $longitude2 经度值 2
    	 * return float
    	*/  
    	public function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
    	

    
##3.访问规则
本框架采用单入口多应用的访问模式，采用mvcs的架构模式架构，一个应用只有一个入口文件，例如我们的manmage后台
管理应用的入口文件就是manage/index.php。
后台的所有的访问必须通过这个入口进去。
####（1）系统架构模式讲解
* M(模块) 框架采用多模块设计，一个应用下面可以有多个模块。每个模块之间功能相互独立
* m(模型) 框架中将数据对象抽象为一个个模型，对数据库的所有基本操作全部在这里
* v(显示) 框架采用smarty模版机制，所有的视图显示全部在这里
* c(控制器) 框架使用控制器来进行操作。调用server层进行数据处理，调用v层进行数据显示
* s(服务层) 框架采用服务层来进行逻辑处理，通过调用m层M进行数据处理，并返回数据给c层

#### （2）访问规则
框架采用静态目录结构访问，可兼容动态目录结构，基础访问参数：

    m:Module 模块
    c:Controller 控制器
    a:Action 动作
    
动态访问规则：

    http://demo.com/index.php?m=test&c=test&a=index&param1=1&param2=2
    
静态访问规则:

    http://demo.com/test/test/index/param1/1/param2/2
    
    或者
    
    http://demo.com/test/test/index?param1=1&param2=2
    
### Module 模块
一个模块代表一个独立的业务功能的集合。模块与模块之间相互独立。

全局获取模块名
    
    MODULE_NAME
    
### Controller 控制器
一个控制器代表一个相同的业务功能的集合

全局获取操作名

    CONTROLLER_NAME
### Action 动作
一个单独的操作

全局获取操作名

    ACTION_NAME

##4.模版
本框架采用smarty模版引擎进行模版显示。模版的存放路径为：

    Template\模块名\控制器名\操作名.tpl
    
模版赋值
    
    $this->assign("KEY","Value");    
    
模版显示,其中参数Template可以为空，为空则自动根据模块和操作名取模版，也可以自己指定，自己指定模版如果是在当前模块下面只需要指定控制器名称和操作名称即可

    $this->display(Tepmlate);

##5.格式化返回

ErrorCode 错误定义类，里面定义了需要的所有错误常量
Response 格式化返回类


错误输出$this->errorResponse(错误码,错误内容，数据，增加的数据)：

    public function errorResponse($errno = 0, $errmsg = '', $data = [], $adddata = [])
    
例如：

    $this->errorResponse(ErrorCode::PARAM_ERROR);
    
正确输出：$this->successResponse(数据，增加数据，正确提示内容);

    public function successResponse($data = [], $adddata = [],$successMsg="")
    
例如：

    $this->successResponse(["test"=>112222]);


##参数获取
框架采用Param::reqParam("参数名"，“转换的数据类型”)或者是$this->reqParam("参数名"，“转换的数据类型”)来进行获取
可选的转换数据类型有：

    const INT = "int";  //强制转换为int类型
    const TIME = "time";//强制转换为时间类型
    const STRING = "string";//强制转换为字符串类型
    const NULL_ = "null";//为空则转换为null
    const BOOL = "bool";//强制转换为bool类型
    const OTHER = "other";//类型不转换，根据传入的参数类型来
    	

    public function reqParam($key="", $type = Param::OTHER, $default=false)

##脚本运行
脚本开头require入口文件即可。

例如：

    <?php
    /**
     * @author Alen
     * create_time 2017/04/10
     */
    use Redlib\Core\Cache;
    use Redlib\Service\Common\Tool;
    
    require __DIR__.'/../../index.php';//新框架里面引入此文件即可
    
##6.自动验证
本框架采用独立的Redlib\Core\Validate类或者验证器进行验证
###独立验证
    任何时候都可以采用Validate类进行独立验证，例如：
    $validate = new Validate([
        'name'  => 'require|max:25',
        'email' => 'email'
    ]);
    $data = [
        'name'  => '',
        'email' => '@qq.com'
    ];
    if (!$validate->check($data)) {
        dump($validate->getError());
    }
    
###验证器
    为具体的验证场景或者数据表定义好验证器类，直接调用验证类的check方法即可完成验证，下面是一个例子：
    
    我们定义一个\app\index\validate\User验证器类用于User的验证。
    
    namespace app\index\validate;
    
    use Redlib\Core\Validate;
    
    class User extends Validate
    {
        protected $rule = [
            'name'  =>  'require|max:25',
            'email' =>  'email',
        ];
    
    }

在需要进行User验证的地方，添加如下代码即可：

        $data = [
            'name'=>'tapin',
            'email'=>'tapin@qq.com'
        ];
        
        $validate = validate('User');
        
        if(!$validate->check($data)){
            dump($validate->getError());
        }
        
###设置规则
可以在实例化Validate类的时候传入验证规则，例如： 

    $rules = [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
    ];
    $validate = new Validate($rules);
    
也可以使用rule方法动态添加规则，例如：

    $rules = [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
    ];
    $validate = new Validate($rules);
    $validate->rule('zip', '/^\d{6}$/');
    $validate->rule([
        'email'   => 'email',
    ]);
    
###自定义验证规则
系统内置了一些常用的规则，如果还不够用，可以自己扩展验证规则。

如果使用了验证器的话，可以直接在验证器类添加自己的验证方法，例如：

    namespace Redlib\Validate;
    
    use RedLib\Core\Validate;
    
    class User extends Validate
    {
        protected $rule = [
            'name'  =>  'checkName:tapin',
            'email' =>  'email',
        ];
    
        protected $message = [
            'name'  =>  '用户名必须',
            'email' =>  '邮箱格式错误',
        ];
    
        // 自定义验证规则
        protected function checkName($value,$rule,$data)
        {
            return $rule == $value ? true : '名称错误';
        }
    }
验证方法可以传入的参数共有5个（后面三个根据情况选用），依次为：

* 验证数据
* 验证规则
* 全部数据（数组）
* 字段名
* 字段描述

并且需要注意的是，自定义的验证规则方法名不能和已有的规则冲突。

接下来，就可以这样进行验证：

    $validate = Loader::validate('User');
        if(!$validate->check($data)){
            dump($validate->getError());
        }
        
如果没有使用验证器类，则支持使用extend方法扩展验证规则，例如：
 
    $validate = new Validate(['name' => 'checkName:1']);
    $validate->extend('checkName', function ($value, $rule) {
        return $rule == $value ? true : '名称错误';
    });
    $data   = ['name' => 1];
    $result = $validate->check($data);
    
支持批量注册验证规则，例如：
   
    $validate = new Validate(['name' => 'checkName:1']);
       $validate->extend([
           'checkName'=> function ($value, $rule) {
           return $rule == $value ? true : '名称错误';
       },
           'checkStatus'=> [$this,'checkStatus']
       ]);
       $data   = ['name' => 1];
       $result = $validate->check($data);
    
    
###验证规则和提示信息一起定义
可以支持验证规则和错误信息一起定义的方式，如下：

    $rule = [
        ['name','require|max:25','名称必须|名称最多不能超过25个字符'],
        ['age','number|between:1,120','年龄必须是数字|年龄必须在1~120之间'],
        ['email','email','邮箱格式错误']
    ];
    
    $data = [
        'name'  => 'tapin',
        'age'   => 121,
        'email' => 'tapin@qq.com',
    ];
    $validate = new Validate($rule);
    $result   = $validate->check($data);
    if(!$result){
        echo $validate->getError();
    }
    
###验证场景
 可以在定义验证规则的时候定义场景，并且验证不同场景的数据，例如：
 
    $rule = [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',
    ];
    $msg = [
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',
    ];
    $data = [
        'name'  => 'tapin',
        'age'   => 10,
        'email' => 'tapin@qq.com',
    ];
    $validate = new Validate($rule);
    $validate->scene('edit', ['name', 'age']);
    $result = $validate->scene('edit')->check($data);
    
表示验证edit场景（该场景定义只需要验证name和age字段）。

如果使用了验证器，可以直接在类里面定义场景，例如：

    namespace Redlib\Validate;
    
    use Redlib\Core\Validate;
    
    class User extends Validate
    {
        protected $rule =   [
            'name'  => 'require|max:25',
            'age'   => 'number|between:1,120',
            'email' => 'email',    
        ];
    
        protected $message  =   [
            'name.require' => '名称必须',
            'name.max'     => '名称最多不能超过25个字符',
            'age.number'   => '年龄必须是数字',
            'age.between'  => '年龄只能在1-120之间',
            'email'        => '邮箱格式错误',    
        ];
    
        protected $scene = [
            'edit'  =>  ['name','age'],
        ];
    
    }
    
然后再需要验证的地方直接使用 scene 方法验证

    $data = [
        'name'  => 'thinkphp',
        'age'   => 10,
        'email' => 'thinkphp@qq.com',
    ];
    
    $validate = new Validate($rule);
    $result = $validate->scene('edit')->check($data)

可以在定义场景的时候对某些字段的规则重新设置，例如：

    namespace Redlib\Validate;
        
    use Redlib\Core\Validate;
    
    class User extends Validate
    {
        protected $rule =   [
            'name'  => 'require|max:25',
            'age'   => 'number|between:1,120',
            'email' => 'email',    
        ];
    
        protected $message  =   [
            'name.require' => '名称必须',
            'name.max'     => '名称最多不能超过25个字符',
            'age.number'   => '年龄必须是数字',
            'age.between'  => '年龄只能在1-120之间',
            'email'        => '邮箱格式错误',    
        ];
    
        protected $scene = [
            'edit'  =>  ['name','age'=>'require|number|between:1,120'],
        ];
    
    }
    
##格式验证类
###require

 验证某个字段必须，例如：
 
     'name'=>'require'
     
 ###number 或者 integer
     
 验证某个字段的值是否为数字（采用filter_var验证），例如：
 
     'num'=>'number'
 ###float
 验证某个字段的值是否为浮点数字（采用filter_var验证），例如：
 
     'num'=>'float'
 ###boolean
 验证某个字段的值是否为布尔值（采用filter_var验证），例如：
 
    'num'=>'boolean'
### email
 验证某个字段的值是否为email地址（采用filter_var验证），例如：
 
    'email'=>'email'
 ###array
 验证某个字段的值是否为数组，例如：
 
    'info'=>'array'
 ###accepted
 验证某个字段是否为为 yes, on, 或是 1。这在确认"服务条款"是否同意时很有用，例如：
 
    'accept'=>'accepted'
 ###date
 验证值是否为有效的日期，例如：
 
    'date'=>'date'
 会对日期值进行strtotime后进行判断。
 
 ###alpha
 验证某个字段的值是否为字母，例如：
 
    'name'=>'alpha'
 ###alphaNum
 验证某个字段的值是否为字母和数字，例如：
 
    'name'=>'alphaNum'
### alphaDash
 验证某个字段的值是否为字母和数字，下划线_及破折号-，例如：
 
    'name'=>'alphaDash'
### chs
 验证某个字段的值只能是汉字，例如：
 
     'name'=>'chs'
### chsAlpha
 验证某个字段的值只能是汉字、字母，例如：
 
     'name'=>'chsAlpha'
### chsAlphaNum
 验证某个字段的值只能是汉字、字母和数字，例如：
 
     'name'=>'chsAlphaNum'
 ###chsDash
 验证某个字段的值只能是汉字、字母、数字和下划线_及破折号-，例如：
 
    'name'=>'chsDash'
### activeUrl
 验证某个字段的值是否为有效的域名或者IP，例如：
 
    'host'=>'activeUrl'
 ###url
 验证某个字段的值是否为有效的URL地址（采用filter_var验证），例如：
 
     'url'=>'url'
### ip
 验证某个字段的值是否为有效的IP地址（采用filter_var验证），例如：
 
    'ip'=>'ip'
 支持验证ipv4和ipv6格式的IP地址。
 
 ###dateFormat:format
 验证某个字段的值是否为指定格式的日期，例如：
 
    'create_time'=>'dateFormat:y-m-d'
 ##长度和区间验证类
 
 ###in
 验证某个字段的值是否在某个范围，例如：
 
    'num'=>'in:1,2,3'
 ###notIn
 验证某个字段的值不在某个范围，例如：
 
    'num'=>'notIn:1,2,3'
 ###between
 验证某个字段的值是否在某个区间，例如：
 
    'num'=>'between:1,10'
 ###notBetween
 验证某个字段的值不在某个范围，例如：
 
     'num'=>'notBetween:1,10'
 ###length:num1,num2
 验证某个字段的值的长度是否在某个范围，例如：
 
    'name'=>'length:4,25'
 或者指定长度
 
    'name'=>'length:4'
 如果验证的数据是数组，则判断数组的长度。
 如果验证的数据是File对象，则判断文件的大小。
 
###max:number
 验证某个字段的值的最大长度，例如：
 
    'name'=>'max:25'
 如果验证的数据是数组，则判断数组的长度。
 如果验证的数据是File对象，则判断文件的大小。
 
 ###min:number
 验证某个字段的值的最小长度，例如：
 
    'name'=>'min:5'
 如果验证的数据是数组，则判断数组的长度。
 如果验证的数据是File对象，则判断文件的大小。
 
 ###after:日期
 验证某个字段的值是否在某个日期之后，例如：
 
    'begin_time' => 'after:2016-3-18',
 ###before:日期
 验证某个字段的值是否在某个日期之前，例如：
 
    'end_time'   => 'before:2016-10-01',
 ###expire:开始时间,结束时间
 验证当前操作（注意不是某个值）是否在某个有效日期之内，例如：
 
    'expire_time'   => 'expire:2016-2-1,2016-10-01',
 ###allowIp:allow1,allow2,...
 验证当前请求的IP是否在某个范围，例如：
 
    'name'   => 'allowIp:114.45.4.55',
 该规则可以用于某个后台的访问权限
 
 ###denyIp:allow1,allow2,...
 验证当前请求的IP是否禁止访问，例如：
 
    'name'   => 'denyIp:114.45.4.55',
 字段比较类
 
### confirm
 验证某个字段是否和另外一个字段的值一致，例如：
 
    'repassword'=>'require|confirm:password'
 增加了字段自动匹配验证规则，如password和password_confirm是自动相互验证的，只需要使用
 
    'password'=>'require|confirm'
 会自动验证和password_confirm进行字段比较是否一致，反之亦然。
 
###different
 验证某个字段是否和另外一个字段的值不一致，例如：
 
    'name'=>'require|different:account'
 ###egt 或者 >=
 验证是否大于等于某个值，例如：
 
    'score'=>'egt:60'
    'num'=>'>=:100'
###gt 或者 >
 验证是否大于某个值，例如：
 
    'score'=>'gt:60'
    'num'=>'>:100'
###elt 或者 <=
 验证是否小于等于某个值，例如：
 
    'score'=>'elt:100'
    'num'=>'<=:100'
### lt 或者 <
 验证是否小于某个值，例如：
 
     'score'=>'lt:100'
     'num'=>'<:100'
### eq 或者 = 或者 same
 验证是否等于某个值，例如：
 
    'score'=>'eq:100'
    'num'=>'=:100'
    'num'=>'same:100'
### filter验证
 
 支持使用filter_var进行验证，例如：
 
    'ip'=>'filter:validate_ip'
### 正则验证
 
 支持直接使用正则验证，例如：
 
     'zip'=>'\d{6}',
    // 或者
    'zip'=>'regex:\d{6}',
 如果你的正则表达式中包含有|符号的话，必须使用数组方式定义。
 
    'accepted'=>['regex'=>'/^(yes|on|1)$/i'],
 也可以实现预定义正则表达式后直接调用，例如：
 
## 上传验证
 
###file
 验证是否是一个上传文件
 
    image:width,height,type
 验证是否是一个图像文件，width height和type都是可选，width和height必须同时定义。
 
 ###fileExt:允许的文件后缀
 验证上传文件后缀
 
### fileMime:允许的文件类型
 验证上传文件类型
 
### fileSize:允许的文件字节大小
 验证上传文件大小
##静态调用
 如果需要使用内置的规则验证单个数据，可以使用静态调用的方式
    
    // 日期格式验证
    Validate::dateFormat('2016-03-09','Y-m-d'); // true
    // 验证是否有效的日期
    Validate::is('2016-06-03','date'); // true
    // 验证是否有效邮箱地址
    Validate::is('tapin@qq.com','email'); // true
    // 验证是否在某个范围
    Validate::in('a',['a','b','c']); // true
    // 验证是否大于某个值
    Validate::gt(10,8); // true
    // 正则验证
    Validate::regex(100,'\d+'); // true
 
静态验证的返回值为布尔值，错误信息需要自己处理。

##控制器验证
在控制器中可以调用控制器类提供的validate方法进行验证，如下：

    $data = [
                'name'  => 'tapin',
                'age'   => 20,
                'email' => 'tapinqq.com',
            ];
     dump($this->validate($data,'User'));
            

还可以指定验证场景

    $data = [
                'name'  => 'tapin',
                'age'   => 20,
                'email' => 'tapinqq.com',
            ];
    dump($this->validate($data,'User.edit'));
<?php
/**
 * 全局控制器  框架级跨应用的操作放到此处
 */
namespace Common\Lib;
defined('ACCESS') or exit('Access Denied');//入口检测
class Controller{

    /***
     * 判断返回常量
     */
    const SUCESS_NAME = '成功';
    const ERROR_NAME  = '失败';



    /***
     * 数字零
     */
    const  NUM_ZERO   =    0;

    /***
     * 权限判断
     * @var bool
     */
    public $flag   =   false;

    /***
     * 当前第几页
     * @var
     */
    protected $page;

    /***
     * 每页显示的条数
     * @var
     */
    protected $perpage;


	public $Module = null;//模块名
	public $Controller = null;//控制器名
	public $Action = null;//动作名
	public $Auth_Str = null;//权限识别串
	public $_GPP = null;
	public static $PARAM=null;
	public function __construct(){
		global $module;
		global $controller;
		global $action;
		global $_GPP;
		$this->Module = MODULE_NAME;
		$this->Controller = CONTROLLER_NAME;
		$this->Action = ACTION_NAME;
		$this->_GPP = $_GPP;
		$this->Auth_Str = getAuthUrl();
		if(method_exists($this,'_initialize'))
            $this->_initialize();
	}

	/**
	 * 向模版中输入参数
	 */
	public function assign($key,$value){
		Template::assign($key,$value);
	}

    /**
     * 显示模板
     * @param String $tpl_file 模板名称
     * @param array $v  传递参数
     * @param String $cache_id 缓存ID
     */
	public function display($tpl_file=null,$v=array(), $cache_id=null)
    {
        $conName = str_replace("Controller", "", $this->Controller);
        $ext = TEMPLATE_EXT;
		if($tpl_file){
		    $pathCount = count(explode("/",$tpl_file));
			if($pathCount==2){//两层时自动加上模块名
				$tpl_file = "{$this->Module}/".$tpl_file;
			}else if($pathCount<=1){//只有文件名时自动加上模块和控制器名
                $tpl_file = "{$this->Module}/{$conName}/{$tpl_file}";
            }else{
			    if(strpos($tpl_file,"///")!==false){//根目錄中文件
                    $tpl_file = str_replace("///","",$tpl_file);
                }
            }
            $tpl_file = strpos($tpl_file,$ext)===false?$tpl_file.$ext:$tpl_file;//判断是否有加上扩展名,没有加则自动加上
		}

		$tpl_file = $tpl_file?$tpl_file:"{$this->Module}/{$conName}/{$this->Action}{$ext}";
		Template::display($tpl_file,$v, $cache_id);
	}
	
	/**
	 * 正确Json返回
	 * $data 返回内容 Array
	 * $adddata 额外添加的返回内容 Array
	 */
	public function successResponse($data = [], $adddata = [],$successMsg="ok",$jumpurl=""){
		Response::getInstance()->successResponse($data, $adddata,$successMsg,$jumpurl);
	}
	/**
	 * 错误Json返回
	 * $errno 错误码 int
	 * $errmsg 错误提示内容 String
	 * $data 返回内容 Array
	 * $adddata 额外添加的返回内容 Array
	 */
	public function errorResponse($errno = 0,$errmsg = '',$data = [], $adddata = [],$jumpurl=""){
		$errmsg = $errmsg?$errmsg:ErrorCode::getErrMsg($errno);
		Response::getInstance()->errorResponse($errno, $errmsg,$jumpurl, $data, $adddata);
	}

    /***
     * 等到每页的数据
     */
    public function getperPage()
    {
        $page = $perpage = self::NUM_ZERO;
        if (isset($this->_GPP['page'])){
            $page = (int)$this->_GPP['page'];
        }
        $this->page = $page?$page:self::PAGE;

        if (isset($this->_GPP['perpage'])){
            $perpage = $this->_GPP['perpage'];
        }
        $this->perpage = $perpage?$perpage:self::PERPAGE;

    }


    /**
     * 参数处理函数
     * @param string $key
     * @param string $type 强制转换类型
     * @param mixed $default 默认值(为了兼容之前的代码，所以默认false返回NULL, 其他值原样返回)
     * @return mixed
     */
    public function reqParam($key="", $type = Param::OTHER, $default=false){
        return Param::reqParam($key, $type, $default);
    }

    /**
     * 验证数据
     * @access protected
     * @param array        $data     数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array        $message  提示信息
     * @param bool         $batch    是否批量验证
     * @param mixed        $callback 回调方法（闭包）
     * @return array|string|true
     * @throws ValidateException
     */
    public function validate($data, $validate, $message = [], $batch = false, $callback = null)
    {
        if (is_array($validate)) {
            $v = new Validate($validate);
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $v = validate($validate);
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }
        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        if (is_array($message)) {
            $v->message($message);
        }

        if ($callback && is_callable($callback)) {
            call_user_func_array($callback, [$v, &$data]);
        }

        if (!$v->check($data)) {
            if ($this->failException) {
                throw new Exception($v->getError());
            } else {
                return $v->getError();
            }
        } else {
            return true;
        }
    }

	/**
	 * 是否ajax请求(jquery)
	 * @return bool
	 */
	public function isAjax()
	{
		return (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
	}



	
}
?>
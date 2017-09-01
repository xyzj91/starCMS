<?php
namespace Common\Lib;
class Response{
    private static $ins = null;
    protected $_types = array();
	const SUCCESS = 1;
	const ERROR = 0;
    public function __construct(){
    }
	public static function getInstance(){
        if (!self::$ins) {
            self::$ins = new self();
        }
        return self::$ins;
    }
    public function successResponse($data = [], $adddata = [],$successMsg="",$jumpurl=""){
        $this->baseResponse(self::SUCCESS,$data, $adddata,0,$successMsg,$jumpurl);
    }
    public function errorResponse($errno = 0, $errmsg = '',$jumpurl="", $data = [], $adddata = []){
        $this->baseResponse(self::ERROR, $data, $adddata,$errno, $errmsg,$jumpurl);
    }



    public function getSuccessResponse($data = [], $adddata = [],$successMsg="",$jumpurl=""){
        return $this->getBaseResponse(self::SUCCESS,$data, $adddata,0,$successMsg,$jumpurl);
    }
    public function getErrorResponse($errno = 0, $errmsg = '',$jumpurl="", $data = [], $adddata = []){
        return $this->getBaseResponse(self::ERROR, $data, $adddata,$errno, $errmsg,$jumpurl);
    }
    /**
	 * 返回函数 
	 * $status 状态码 int
	 * $data 返回内容 Array
	 * $adddata 额外添加的返回内容 Array
	 * $errno 错误码 int
	 * $errmsg 错误提示内容 String
	 */
    public function baseResponse($status = self::ERROR, $data = [], $adddata = [], $errno = 0, $errmsg = '',$jumpurl=""){
        $rs['status'] = $status;
        $rs['data'] = $data;
        if ($status){
            $rs["successmsg"] =  $errmsg;
        }else{
            $rs['errormsg'] = ['errcode' => $errno, 'errmsg'=> $errmsg];
        }
        if($jumpurl){
            $rs['jumpurl'] = $jumpurl;
        }
        if(!empty($adddata)) $rs = array_merge($rs, $adddata);
        $this->response($rs);
    }
    /**
     * 返回函数
     * $status 状态码 int
     * $data 返回内容 Array
     * $adddata 额外添加的返回内容 Array
     * $errno 错误码 int
     * $errmsg 错误提示内容 String
     */
    public function getBaseResponse($status = self::ERROR, $data = [], $adddata = [], $errno = 0, $errmsg = '',$jumpurl=""){
        $rs['status'] = $status;
        $rs['data'] = $data;
        if ($status){
            $rs["successmsg"] =  $errmsg;
        }else{
            $rs['errormsg'] = ['errcode' => $errno, 'errmsg'=> $errmsg];
        }
        if($jumpurl){
            $rs['jumpurl'] = $jumpurl;
        }
        if(!empty($adddata)) $rs = array_merge($rs, $adddata);
        return $rs;
    }
    
    /**
     * 输出返回数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @param integer $code HTTP状态
     * @return void
     */
    public function response($data, $type = 'json', $code = 200) {
        $this->sendHttpStatus($code);
        exit($this->encodeData($data,strtolower($type)));
    }

    // 发送Http状态信息
    public function sendHttpStatus($code) {
        static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if(isset($_status[$code])) {
            if(!APP_DEBUG){
                //允许跨域，便于app端缓存模板
                header('Access-Control-Allow-Origin:*');
                header('HTTP/1.1 '.$code.' '.$_status[$code]);
                // 确保FastCGI模式下正常
                header('Status:'.$code.' '.$_status[$code]);
            }
        }
    }
    
    /**
     * 编码数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @return void
     */
    public function encodeData($data,$type='') {
        if(empty($data))  return '';
        if('json' == $type) {
            // 返回JSON数据格式到客户端 包含状态信息
            $data = json_encode($data);
        }elseif('xml' == $type){
            // 返回xml格式数据
            $data = xml_encode($data);
        }elseif('php'==$type){
            $data = serialize($data);
        }// 默认直接输出
        $this->setContentType($type);
        //header('Content-Length: ' . strlen($data));
        return $data;
    }
    
    /**
     * 设置页面输出的CONTENT_TYPE和编码
     * @access public
     * @param string $type content_type 类型对应的扩展名
     * @param string $charset 页面输出编码
     * @return void
     */
    private function setContentType($type, $charset='utf-8'){
        if(headers_sent()) return;
        $type = strtolower($type);
        if(isset($this->_types[$type])) //过滤content_type
            header('Content-Type: '.$this->_types[$type].'; charset='.$charset);
    }
}
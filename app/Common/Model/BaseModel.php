<?php

/**
 * 基础模型
 * Created by taping.
 * User: Alen
 * Date: 2017/8/27
 * Time: 12:25
 */
namespace Common\Model;
use Common\Lib\Database;
class BaseModel
{
    public $sqlServer = null;
    protected $modelName = null;//模型名称
    public function __construct()
    {
        $this->sqlServer = Database::instance();
        if(!$this->modelName){
            $this->modelName = $this->getModelName();
        }
    }

    /**
     * 获取到当前模型对应的类名
     */
    private function getModelName(){
        $arr = explode("\\",get_class($this));
        return parse_name(str_replace("Model","",$arr[count($arr)-1]));
    }

    /**
     * 初始化模型类
     */
    private function initModel(){
        Database::setModel($this->modelName);
    }

    public function add($data){
        $this->initModel();
        return $this->sqlServer->add($data);
    }

    public function del($where){
        $this->initModel();
        return $this->sqlServer->del($where);
    }

    public function edit($data,$where){
        $this->initModel();
        return $this->sqlServer->edit($data,$where);
    }

    public function select($columns,$where,$join=null){
        $this->initModel();
        return $this->sqlServer->select($columns,$where,$join);
    }
    public function select_limit($columns,$where,$page=1,$page_size=20,$orderby=[],$join=null){
        $this->initModel();
        return $this->sqlServer->select_limit($columns,$where,$page,$page_size,$orderby,$join);
    }

    public function get($columns,$where){
        $this->initModel();
        return $this->sqlServer->get($columns,$where);
    }
    public function has($where,$join=[]){
        $this->initModel();
        return $this->sqlServer->has($where,$join);
    }
    public function count($columns,$where,$join=[]){
        $this->initModel();
        return $this->sqlServer->count($columns,$where,$join);
    }
    public function max($columns,$where,$join=[]){
        $this->initModel();
        return $this->sqlServer->max($columns,$where,$join);
    }
    public function min($columns,$where,$join=[]){
        $this->initModel();
        return $this->sqlServer->min($columns,$where,$join);
    }
    public function avg($columns,$where,$join=[]){
        $this->initModel();
        return $this->sqlServer->avg($columns,$where,$join);
    }
    public function sum($columns,$where,$join=[]){
        $this->initModel();
        return $this->sqlServer->sum($columns,$where,$join);
    }
    public function getlastsql(){
        return $this->sqlServer->getlastsql();
    }
    public function getDatabase(){
        return (Database::instance())->getDb();
    }
}
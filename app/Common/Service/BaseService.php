<?php

/**
 * Created by taping.
 * User: Alen
 * Date: 2017/8/27
 * Time: 14:59
 */
namespace Common\Service;
use Common\Lib\Database;

class BaseService
{
    protected $MD = null;
    public $mdObj = [];
    public $thisMd = null;
    public  function __construct()
    {
        if($this->MD && !isset($this->mdObj[$this->MD])){
            $name = "\\Common\\Model\\{$this->MD}";
            $this->mdObj[$this->MD] = new $name();
        }
    }

    public function getModel(){
        return $this->mdObj[$this->MD];
    }

    /**
     * 增加数据
     * @param $data
     * @return mixed
     */
    public function add($data){
        return $this->mdObj[$this->MD]->add($data);
    }

    /**
     * 删除数据
     * @param $where
     * @return mixed
     */
    public function del($where){
        return $this->mdObj[$this->MD]->del($where);
    }

    /**
     * 修改数据
     * @param $where
     * @param $data
     */
    public function edit($where,$data){
        return $this->mdObj[$this->MD]->edit($data,$where);
    }

    /**
     * 查询数据
     * @param $where
     * @return mixed
     */
    public function select($columns,$where,$join=null){
        return $this->mdObj[$this->MD]->select($columns,$where,$join);
    }
    public function select_limit($columns,$where,$page=1,$page_size=20,$orderby=[],$join=null){
        return $this->mdObj[$this->MD]->select_limit($columns,$where,$page,$page_size,$orderby,$join);
    }
    public function get($columns,$where){
        return $this->mdObj[$this->MD]->get($columns,$where);
    }
    public function has($where,$join=[]){
        return $this->mdObj[$this->MD]->has($where,$join);
    }
    public function count($columns,$where,$join=[]){
        return $this->mdObj[$this->MD]->count($columns,$where,$join);
    }
    public function max($columns,$where,$join=[]){
        return $this->mdObj[$this->MD]->max($columns,$where,$join);
    }
    public function min($columns,$where,$join=[]){
        return $this->mdObj[$this->MD]->min($columns,$where,$join);
    }
    public function avg($columns,$where,$join=[]){
        return $this->mdObj[$this->MD]->avg($columns,$where,$join);
    }
    public function sum($columns,$where,$join=[]){
        return $this->mdObj[$this->MD]->sum($columns,$where,$join);
    }
    public function getlastsql(){
        return $this->mdObj[$this->MD]->getlastsql();
    }

    public function getDatabase(){
        return (Database::instance())->getDb();
    }

    /**
     * 返回最后插入的行
     * @return mixed
     */
    public function getLastId(){
        return $this->getDatabase()->id();
    }

}
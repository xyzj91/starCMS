<?php
/**
 * Created by taping.
 * User: Alen
 * Date: 2017/8/15 0015
 * Time: 下午 6:09
 */
use App\Common\BaseController;
class IndexController extends BaseController{
    public function index(){
        dump(666);
    }
    public function test(){
        $this->successResponse([],[],"ok");
    }
}
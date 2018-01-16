<?php
/**
 * Created by PhpStorm.
 * User: Alen
 * Date: 2018/1/16 0016
 * Time: 上午 11:13
 */
require __DIR__."/app/index/index.php";//加载框架入口文件

if(php_sapi_name() != 'cli'){
    dump("请在命令行模式下运行");
}
//loadService("/test1");

//loadModel("@/test1");

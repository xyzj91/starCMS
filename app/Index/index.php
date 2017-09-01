<?php
//入口文件
define('ROOT_PATH', (dirname(dirname(dirname(__FILE__)))));
define('CORE_PATH',ROOT_PATH."/Core/");
define('APP_NAME', "Index");
define('APP_DEBUG',true);
require CORE_PATH.'Init.inc.php';//加载框架核心文件

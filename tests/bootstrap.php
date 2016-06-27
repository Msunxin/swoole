<?php

/*
 * 单元测试入口文件
 */

require(__DIR__ . '/../vendor/autoload.php');


//单元测试根目录
define('TEST_ROOT_DIR', __DIR__);
//单元测试资源文件目录，注意，末尾不带斜杠
define('TEST_RESOURCES_DIR', __DIR__ . '/resources');

//
$testServerconfigs = require(__DIR__ . '/../SOAServer/configs/server.unittest.config.php');
//logger的 arguments 里使用了匿名函数，phpunit会报错，暂时去掉logger配置，稍后解决此问题
unset($testServerconfigs['services']['logger']);
//
(new mkf\ApplicationTestServer($testServerconfigs))->run();


<?php

/*
 * mkf AppServer 开发配置
 */

$configs = array(
    //服务器配置
    'serverAddress' => '0.0.0.0',
    'serverPort' => 9501,
    'serverDaemonize' => false,
    'serverWorkerNum' => 4,
    'serverMaxRequestPerWorker' => 500,
    //应用配置 - 基本配置
    'appName' => 'SOAServer',                       //应用名称（必须填写正确）
    'basePath' => dirname(__DIR__),
    //应用配置 - 数据库配置
    'dbDriver' => 'pdo_mysql',
    'dbHost' => '10.8.4.241',
    'dbPort' => 3306,
    'dbName' => 'cbdadmin0317_db',
    'dbUser' => 'cbdadmin',
    'dbPassword' => 'cbdadmin',
    'dbCharset' => 'utf8',
    'dbTablePrefix' => 'cbd_',
    //应用配置 - 缓存配置 - Redis配置
    'redisHost' => '10.8.4.233',
    'redisPort' => '6379',
    'redisAuth' => 'redis123',
    //应用配置 - 服务对象容器（依赖注入）配置
    'services' => require(__DIR__ . '/services.config.php'),
);

$localConfigs = file_exists(__DIR__ . '/local.config.php') ? require(__DIR__ . '/local.config.php') : array();

return array_merge($configs, $localConfigs);

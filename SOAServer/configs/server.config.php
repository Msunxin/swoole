<?php

/*
 * mkf AppServer 正式配置
 */

return array(
    //服务器配置
    'serverAddress' => '0.0.0.0',
    'serverPort' => 9501,
    'serverDaemonize' => true,
    'serverWorkerNum' => 50,
    'serverMaxRequestPerWorker' => 5000,
    //应用配置 - 基本配置
    'appName' => 'SOAServer',                       //应用名称（必须填写正确）
    'basePath' => dirname(__DIR__),
    //应用配置 - 数据库配置
    'dbDriver' => 'pdo_mysql',
    'dbHost' => '',
    'dbPort' => 3306,
    'dbName' => '',
    'dbUser' => '',
    'dbPassword' => '',
    'dbCharset' => 'utf8',
    'dbTablePrefix' => '',
    //应用配置 - 缓存配置 - Redis配置
    'redisHost' => '',
    'redisPort' => '6379',
    'redisAuth' => '',
    //应用配置 - 服务对象容器（依赖注入）配置
    'services' => require(__DIR__ . '/services.config.php'),
);

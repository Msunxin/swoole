<?php

namespace mkf;

use mkf\di\Di;
use mkf\log\Logger;
use mkf\caching\Cache;
use mkf\db\Connection;

/**
 * 应用类
 * 
 * @property array $configs 应用配置
 * @property Request $request 当前请求
 * @property Logger $logger 日志记录器
 * @property Cache $cache 缓存对象
 * @property Connection $connection 数据库连接
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Application {

    /**
     * @var array
     */
    private $configs;

    /**
     * @var Request 当前请求
     */
    private $request;

    public function __construct($configs) {
        $this->configs = $configs;
        $this->initialize();
    }

    private function initialize() {
        //初始化依赖注入容器
        $serviceConfigs = isset($this->configs['services'])
                ? $this->configs['services'] : array();
        Di::initialize($serviceConfigs);
        //创建Request对象
        $this->request = new Request();
    }

    /**
     * 返回应用配置
     * @return array
     */
    public function getConfigs() {
        return $this->configs;
    }

    /**
     * 返回当前请求对象
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * 返回日志记录器
     * @return Logger
     */
    public function getLogger() {
        return Di::get('logger');
    }

    /**
     * 返回缓存实例
     * @return Cache
     */
    public function getCache() {
        return Di::get('cache');
    }

    /**
     * 返回数据库连接
     * @return Connection 数据库连接
     */
    public function getConnection() {
        return Di::get('connectionManager')->getConnection();
    }

    public function __get($name) {
        $getterMethod = 'get' . ucfirst($name);
        if (!method_exists($this, $getterMethod)) {
            trigger_error("property \"{$name}\" not found", E_USER_ERROR);
        }
        return $this->$getterMethod();
    }

}

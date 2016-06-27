<?php

namespace mkf;

use mkf\di\Di;
use mkf\Mkf;
use mkf\log\Logger;
use mkf\caching\Cache;

/**
 * 控制器基类
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Controller {

    /**
     * 取得服务对象
     * @param string $serviceName 服务名
     * @return Object
     */
    public function get($serviceName) {
        return Di::get($serviceName);
    }

    public function getParam($paramName) {
        return Mkf::$app->getRequest()->getParam($paramName);
    }

    /**
     * 返回全部参数
     * @return array
     */
    public function getParams() {
        return Mkf::$app->getRequest()->getParams();
    }

    /**
     * 返回日志记录器
     * @return Logger
     */
    public function getLogger() {
        return Mkf::$app->getLogger();
    }

    /**
     * 返回缓存实例
     * @return Cache
     */
    public function getCache() {
        return Mkf::$app->getCache();
    }

}

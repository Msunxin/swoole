<?php

namespace mkf\caching;

/**
 * Redis缓存
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class RedisCache extends CacheBase {

    private $handler;

    public function __construct() {
        $appConfigs = \mkf\Mkf::$app->configs;

        $this->handler = new \Redis;
        $this->handler->pconnect($appConfigs['redisHost'], $appConfigs['redisPort']);
        $this->handler->auth($appConfigs['redisAuth']);
    }

    public function get($key) {
        return $this->handler->get($key);
    }

    public function set($key, $value, $expire = null) {
        if (is_int($expire) && $expire) {
            $result = $this->handler->setex($key, $expire, $value);
        } else {
            $result = $this->handler->set($key, $value);
        }
        return $result;
    }

    public function rm($key) {
        return $this->handler->delete($key);
    }

    public function clear() {
        return $this->handler->flushDB();
    }

    public function getHandler() {
        return $this->handler;
    }

}

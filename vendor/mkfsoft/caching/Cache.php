<?php

namespace mkf\caching;

/**
 * 缓存接口
 * @author zhengluming <luming.zheng@baozun.cn>
 */
interface Cache {

    /**
     * 从缓存中取得一个值
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * 向缓存中存入一个值
     * @param string $key
     * @param mixed $value
     * @param null|int $expire
     * @return boolean 成功返回true，失败返回false
     */
    public function set($key, $value, $expire = null);

    /**
     * 从缓存中删除一个值
     * @param string $key
     * @return boolean
     */
    public function rm($key);

    /**
     * 清除缓存中的所有数据
     * @return boolean
     */
    public function clear();

    /**
     * 取得原生底层对象
     * @return Object
     */
    public function getHandler();
}

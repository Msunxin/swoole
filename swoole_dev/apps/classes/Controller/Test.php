<?php

namespace Controller;

class Test {

    public function __construct() {
        echo "这里是构造器\n";
    }

    public function cacheTest() {
        $swoole = \Swoole::getInstance();
        $redis = $swoole->rediscluster('master');
        $time = time();
        $r1 = $redis->set('test1', $time);
        $r2 = $redis->get('test1');
        var_dump($time, $r1, $r2);
    }

    /**
     * 实例方法
     */
    public function fuck($who, $times) {
        return "fuck {$who} for {$times} times";
    }

    /**
     * 操作数据库示例
     * @return type
     */
    public function getGoodsName($goodsId) {
        $goodsModel = model('goods');
        $goods = $goodsModel->get($goodsId);
        return $goods->get();
    }

}

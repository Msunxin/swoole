<?php

define('DEBUG', 'on');
define('WEBPATH', dirname(__DIR__));
//require __DIR__ . '/libs/lib_config.php';
//require __DIR__ . '/../vendor/autoload.php';


require_once __DIR__ . '/libs/Swoole/Swoole.php';
require_once __DIR__ . '/libs/Swoole/Loader.php';
/**
 * 注册顶层命名空间到自动载入器
 */
Swoole\Loader::addNameSpace('Swoole', __DIR__ . '/libs/Swoole');
spl_autoload_register('\\Swoole\\Loader::autoload');

$cloud = Swoole\Client\SOA::getInstance();
$cloud->addServers(array('127.0.0.1:9501'));

$s = microtime(true);
$ok = $err = 0;
for ($i = 0; $i < 1; $i++) {
    $s2 = microtime(true);
    $a = 1;
    if ($a == 1) {
        //商品信息
        $ret1 = $cloud->sync('/Goods/getGoodsInfo', array('goodsId' => array(982, 33014), 'field' => array(
            'goods_id', 'goods_type', 'goods_name', 'saletype', 'buyupnum', 'buydownnum', 'open_time',
            'close_time', 'surplus', 'is_pack_goods', 'is_fbs', 'fbs_version', 'random_pack'
        )));

        //规格信息
        $ret2 = $cloud->sync('/Spec/getGoodsSpec', array('goodsId' => array(982, 33014), 'field' => array(
            'spec_id', 'goods_id', 'goods_no', 'stocknum', 'crazy_price', 'skusize', 'color', 'random_num'
        )));

        //商品图片
        $ret3 = $cloud->sync('/Goods/getGoodsImg', array('goodsId' => array(982, 33014), 'field' => array(
            'goods_id', 'img120_url', 'img320_url', 'img640_url', 'img1080_url', 'is_cover', 'favs'
        )));

        //商品状态
        $ret4 = $cloud->sync('/Goods/getGoodsStatus', array('goodsId' => array(982, 33014), 'field' => array(
            'goods_id', 'state', 'status'
        )));

//        $n = $cloud->wait();
//        if ($n == 4) {
            var_dump($ret1->data, $ret2->data, $ret3->data, $ret4->data);
            echo "finish\n";
            $ok++;
//        }
//        echo "result 1: ", json_encode($ret1->data), "\n";
//        echo "result 2: ", json_encode($ret2->data), "\n";
//        echo "result 3: ", json_encode($ret3->data), "\n";
        echo "send " . (microtime(true) - $s2) * 1000, " ms\n";
    } else {
        $ret4 = $cloud->callApi('/goods/getGoodsDetail', array(
            'goodsId' => [982, 33014],
            'goodsImg' => 2,
            'goodsSpec' => true,
        ));
        var_dump($ret4->data);
        echo "finish\n";
        $ok++;
//        echo "result 4: ", json_encode($ret4->data), "\n";
//        echo "send " . (microtime(true) - $s2) * 1000, " ms\n";
    }

}
echo "failed=$err.\n";
echo "success=$ok.\n";
echo "use " . (microtime(true) - $s) * 1000, " ms\n";
unset($cloud, $ret1, $ret2);

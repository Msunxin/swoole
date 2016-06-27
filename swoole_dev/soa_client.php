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
    $ret1 = $cloud->sync("Controller\\Test::test", array());

    $ret2 = $cloud->sync("Task\\Test::test", array());
    
    $ret3 = $cloud->callApi('/test/fuck', array('your sister', 8));
    
    $ret4 = $cloud->callApi('/test/getGoodsName', array(982));

    var_dump($ret1->data, $ret2->data, $ret3->data, $ret4->data);
    echo "send " . (microtime(true) - $s2) * 1000, "\n";

//    $n = $cloud->wait(0.5); //500ms超时
//    die;
    //表示全部OK了
//    if ($n === 8) {
//        var_dump($ret1->data, $ret2->data, $ret3->data, $ret4->data, $ret5->data, $ret6->data, $ret7->data, $ret8->data);
//        echo "finish\n";
//        $ok++;
//    } else {
//        echo "#{$i} \t";
//        echo $ret1->code . '|' . $ret2->code . '|' . $ret3->code . '|' . $ret4->code . '|' . $ret5->code . '|' . $ret6->code . '|' . $ret7->code . '|' . $ret8->code . '|' . "\n";
//        $err++;
//        exit;
//    }
//    unset($ret1, $ret2, $ret3, $ret4, $ret5, $ret6, $ret7, $ret8);
}
echo "failed=$err.\n";
echo "success=$ok.\n";
echo "use " . (microtime(true) - $s) * 1000, "ms\n";
unset($cloud, $ret1, $ret2);

<?php

namespace SOAServer\controllers;

use mkf\Controller;
use mkf\Mkf;

/**
 * HelloWorld控制器
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class HelloWorldController extends Controller {

    public function sayHelloWorld() {
        $request = Mkf::$app->getRequest();
        return 'hello world，尼玛！！！' . $request->getParam('words');
    }

    public function getGoodsInfo() {
        $helloWorldModel = new \SOAServer\models\HelloWorld();

        Mkf::$app->logger->log('添加HelloWorld');
        $helloWorldModel->addHelloWorld();
        $helloWorldModel->updateHelloWorld();
        $helloWorldModel->removeHelloWorld();

        $goodsId = $this->getParam('goods_id');
        $goodsModel = new \SOAServer\models\Goods();
        $goodsInfo = $goodsModel->getGoodsInfo($goodsId);
        return $goodsInfo;
    }

    public function getUserName() {
        $cache = Mkf::$app->getCache();
        $cache->set('name', '张三 : ' . time());
        return $cache->get('name');
    }

}

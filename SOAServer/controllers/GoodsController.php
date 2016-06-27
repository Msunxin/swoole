<?php
namespace SOAServer\controllers;

use mkf\Controller;
use mkf\Mkf;

class GoodsController extends Controller
{
    /**
     * 获取商品详情
     * @return array
     */
    public function getGoodsDetail()
    {
        $goodsId = $this->getParam('goodsId');
        $imgType = $this->getParam('imgType');
        $goodsSpec = $this->getParam('goodsSpec');
        $goodsModel = new \SOAServer\models\Goods();
        return $goodsModel->getGoodsDetail($goodsId, $imgType, $goodsSpec);
    }

    /**
     * 获取商品信息
     * @return array
     */
    public function getGoodsInfo()
    {
        $goodsId = $this->getParam('goodsId');
        $field = $this->getParam('field');
        $goodsModel = new \SOAServer\models\Goods();
        return $goodsModel->getGoodsInfo($goodsId, $field);
    }

    /**
     * 获取商品状态
     * @return array
     */
    public function getGoodsStatus()
    {
        $goodsId = $this->getParam('goodsId');
        $field = $this->getParam('field');
        $goodscollectModel = new \SOAServer\models\Goodscollect();
        return $goodscollectModel->getGoodsStatus($goodsId, $field);
    }

    /**
     * 获取商品图片
     * @return array
     */
    public function getGoodsImg()
    {
        $goodsId = $this->getParam('goodsId');
        $imgType = $this->getParam('imgType');
        $field = $this->getParam('field');
        $imgModel = new \SOAServer\models\Imgfavs();
        return $imgModel->getGoodsImgs($goodsId, $imgType, $field);
    }
    
    public function sayHello($name, $age) {
        return array('name' => $name, 'age' => $age);
    }
}
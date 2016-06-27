<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/2
 * Time: 9:46
 */

namespace SOAServer\controllers;

use mkf\Controller;

class SpecController extends Controller
{
    /**
     * 获取规格信息
     * @return array
     */
    public function getGoodsSpec()
    {
        $goodsId = $this->getParam('goodsId');
        $field = $this->getParam('field');
        $specModel = new \SOAServer\models\Spec();
        return $specModel->getGoodsSpec($goodsId, $field);
    }
}
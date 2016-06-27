<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/3
 * Time: 16:39
 */

namespace SOAServer\controllers;

use mkf\Controller;
use SOAServer\business\cart\GoodsHelper;
use SOAServer\models\Cart;

class CartController extends Controller
{
    public function activityCalculator($result)
    {
        $userId = $result['userId'];
        $couponCode = $result['couponCode'];
        $goodsId = $result['goodsId'];
        $specId = $result['specId'];
        $num = $result['num'];
        $activityId = $result['activityId'];

        $goodsIds = explode(',', $goodsId);
        $specIds = explode(',', $specId);
        $nums = explode(',', $num);
        $activityIds = explode(',', $activityId);
//        $activityIds = array(
//            3700,   //捆绑销售
//            3589,   //累计折扣
//            3135,   //满额赠
//            3674,   //多件起售
//            'crazyCoin',    //疯狂币
//            'coupon',   //优惠券
//        );

        $activityCalculatorData = new \SOAServer\business\cart\activity\ActivityCalculatorData();

        $activityCalculatorData->setUserId($userId);
        $activityCalculatorData->setCouponCode($couponCode);

        //设置商品
        foreach ($goodsIds as $key => $value) {
            $activityCalculatorData->addGoodsData($value, $specIds[$key], $nums[$key]);
        }

        $list = [];
        $r = null;
        foreach ($activityIds as $activityId) {
            $activityCalculatorData->setActivityId($activityId);
            $activityCalculator = new \SOAServer\business\cart\activity\ActivityCalculatorProxy($r);
            $r = $activityCalculator->calculate($activityCalculatorData);
            if ($r) {
                $list[$activityId] = $r->toArray();
            }
        }
        return $list;
    }

    public function cartInit($result)
    {
        $userId = $result['userId'];
        $goodsId = $result['goodsId'];
        $specId = $result['specId'];
        $num = $result['num'];
        $os = $result['os'];

        if (!is_array($goodsId)) {
            $goodsId = explode(',', $goodsId);
        }
        if (!is_array($specId)) {
            $specId = explode(',', $specId);
        }
        if (!is_array($num)) {
            $num = explode(',', $num);
        }
        $cartModel = new Cart();
        $goodsInfo = $cartModel->getCartGoodsInfo($goodsId, $specId, $num);
        var_dump($goodsInfo);

        $activity = $this->activityInit($goodsId);
        var_dump($activity);
    }

    private function activityInit($goodsId)
    {
        $goodsHelper = new GoodsHelper();
        $activity = $goodsHelper->getSpecialsWhichBelongTo($goodsId);
        $activityResult = array();
        foreach ($activity as $value) {
            $array = array(
                
            );
        }

        return $activityResult;
    }

}
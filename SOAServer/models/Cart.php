<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/7
 * Time: 20:38
 */

namespace SOAServer\models;

use mkf\Model;

class Cart extends Model
{
    public function activityCalculator($userId, $goodsIds, $specIds, $nums, $activityIds, $check, $couponCode)
    {
        $activityCalculatorData = new \SOAServer\business\cart\activity\ActivityCalculatorData();

        $activityCalculatorData->setUserId($userId);
        $activityCalculatorData->setCouponCode($couponCode);

        //设置商品
        foreach ($goodsIds as $key => $value) {
            $activityCalculatorData->addGoodsData($value, $specIds[$key], $nums[$key]);
        }

        $list = [];
        $r = null;
        foreach ($activityIds as $key => $activityId) {
            if ($check[$key] == 0) {
                continue;
            }
            $activityCalculatorData->setActivityId($activityId);
            $activityCalculator = new \SOAServer\business\cart\activity\ActivityCalculatorProxy($r);
            $r = $activityCalculator->calculate($activityCalculatorData);
            if ($r) {
                $list[$activityId] = $r->toArray();
            }
        }
        return $list;
    }

    public function getCartGoodsInfo($goodsIds, $specIds, $nums)
    {

        $goodsModel = new Goods();
        $goods = $goodsModel->getCartGoodsInfo($goodsIds);

        $specModel = new Spec();
        $spec = $specModel->getCartSpec($specIds);

        $imgfavsModel = new Imgfavs();
        $img = $imgfavsModel->getCartImgs($goodsIds);

        $result = array();
        foreach ($goodsIds as $key => $goodsId) {
            $result[$goodsId][$specIds[$key]] = array_merge(
                (array)$goods[$goodsId],
                (array)$spec[$specIds[$key]],
                (array)$img[$goodsId]
            );
            if (isset($nums[$key])) {
                $result[$goodsId][$specIds[$key]]['goodsNum'] = $nums[$key];
            }
        }

        $goodsInfo = array();
        foreach ($result as $goodsId => $goods) {
            foreach ($goods as $specId => $spec) {
                $goodsInfo[] = $spec;
            }
        }

        return $goodsInfo;
    }
}
<?php

namespace SOAServer\business\cart\activity;

/**
 * 限时折扣<br />
 * 来自：cbdroot/mobileapi_app/library/Model/Activity/Subtract.class.php
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class LimitedTimeDiscountActivityCalculator extends ActivityBase {

    private $specialActivityModel;

    public function setSpecialActivityModel($specialActivityModel) {
        $this->specialActivityModel = $specialActivityModel;
    }

    public function calculate(ActivityCalculatorData $activityCalculatorData) {
        $now = time();
        $activity = $this->getActivity($activityCalculatorData);
        $goodsData = $activityCalculatorData->getGoodsData();
        $specialGoods = $activityCalculatorData->getActivityGoods();

        if ($now < $activity['subtract_start_time'] || $now > $activity['subtract_end_time']) {
            return null;
        }
        $discount = $activity['activity_label'] * 10;
        foreach ($goodsData as $goods_id => $vo) {
            foreach ($vo as $spec_id => $v) {
                if (in_array($goods_id, $specialGoods)) {
                    $activity['goods_id'][] = $goods_id;
                    $goodsData[$goods_id][$spec_id]['activityId'] = $activity['special_id'];
                }
            }
        }
        if (!$activity['goods_id']) {
            return null;
        }

        $activityCalculatorResult = $this->activityCalculatorResult;
        if (!$activityCalculatorResult) {
            $activityCalculatorResult = new ActivityCalculatorResult();
        }

        foreach ($goodsData as $goods_id => $vo) {
            foreach ($vo as $spec_id => $v) {
                if ($v['activityId'] == $activity['special_id']) {
                    $discount_single = (double) bcmul(($v['crazy_price'] - $activityCalculatorResult->getGoodsDiscountPrice($v['goods_id'], $v['spec_id'])) * $v['num'], (100 - $discount) / 100, 2);
                    $activityCalculatorResult->addDiscountPrice($goods_id, $spec_id, $discount_single);
                }
            }
        }

        $activityCalculatorResult->setActivityTitle($activity['title']);


        return $activityCalculatorResult;
    }

    private function getActivity(ActivityCalculatorData $activityCalculatorData) {
        $activity = $activityCalculatorData->getActivity();

        $specialActivity = $this->specialActivityModel
                ->getSpecialActivityBySpecialId($activity['special_id']);

        $activity['subtract_start_time'] = $specialActivity['activity_start_time'];
        $activity['subtract_end_time'] = $specialActivity['activity_end_time'];
        $activity['activity_label'] = $specialActivity['activity_label'];

        return $activity;
    }

}

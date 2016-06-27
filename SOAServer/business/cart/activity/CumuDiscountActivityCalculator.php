<?php

namespace SOAServer\business\cart\activity;


/**
 * 累计折扣活动计算
 * Class CumuDiscountActivityCalculator
 * @package SOAServer\business\cart\activity
 */
class CumuDiscountActivityCalculator extends ActivityBase
{

    /**
     * 计算购物车商品价格
     * @param ActivityCalculatorData $activityCalculatorData
     * @return ActivityCalculatorResult
     */
    public function calculate(ActivityCalculatorData $activityCalculatorData)
    {
        $activityCalculatorResult = $this->activityCalculatorResult;

        $activity = $activityCalculatorData->getActivity();
        $goodsData = $activityCalculatorData->getGoodsData();
        $specialGoods = $activityCalculatorData->getActivityGoods();

        $discountGoodsNum = 0;
        $discountText = unserialize($activity['discount_text']);
        $discountAmount = 0;
        $activityGoods = array();
        //整理参加活动的商品
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                if (in_array($goodsId, $specialGoods)) {
                    $activityGoods[$goodsId][$specId]['num'] = $vo['num'];
                    $discountGoodsNum += $vo['num'];
                }
            }
        }
        $oriDiscount = 10;
        $discount = 0;
        foreach ($discountText as $discountSingle) {
            if (!$discountSingle['num'] || !$discountSingle['discount']) {
                continue;
            }
            if ($discountGoodsNum >= $discountSingle['num']) {
                $oriDiscount = $discountSingle['discount'];
                $discount = $discountSingle['discount'] / 10;
            }
        }
        if ($discountGoodsNum) {
            foreach ($activityGoods as $goodsId => $value) {
                foreach ($value as $specId => $vo) {
                    $discountPrice = ($goodsData[$goodsId][$specId]['crazy_price'] - bcmul($goodsData[$goodsId][$specId]['crazy_price'], $discount, 2)) * $vo['num'];
                    $discountAmount += $discountPrice;
                    $activityCalculatorResult->addDiscountPrice($goodsId, $specId, $discountPrice);
                }
            }

            if ($discount) {
                $title = $activity['title'] . " 已满{$discountGoodsNum}件，打{$oriDiscount}折";
                $activityCalculatorResult->setActivityTitle($title);
            } else {
                $title = $activity['title'];
                $activityCalculatorResult->setActivityTitle($title);
            }
        }

        return $activityCalculatorResult;
    }
}
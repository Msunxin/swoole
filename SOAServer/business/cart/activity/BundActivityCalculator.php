<?php

namespace SOAServer\business\cart\activity;

/**
 * 捆绑销售活动计算
 * Class BundActivityCalculator
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
class BundActivityCalculator extends ActivityBase
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
        $discountAmount = 0;
        $bundNum = $activity['bund_num'];   //捆绑数量
        $isSaleBund = $activity['is_sale_bund']; //0：不满数量不可购买，1：不满数量原价购买
        $bundAmount = $activity['bund_amount']; //捆绑总价
        $bundGoodsNum = 0;
        $activityGoods = array();
        //整理参加活动的商品
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                if (in_array($goodsId, $specialGoods)) {
                    $activityGoods[$goodsId][$specId]['num'] = $vo['num'];
                    $bundGoodsNum += $vo['num'];
                }
            }
        }
        if (!$bundGoodsNum) {
            $activityCalculatorResult->setActivityTitle($activity['title']);
            return $activityCalculatorResult;
        }

        //计算数量限制
        if (!$isSaleBund && ($bundGoodsNum - $bundNum) < 0) { //低于捆绑商品数量，不能购买
            $title = $activity['title'] . " <font color='red'>已满{$bundGoodsNum}件，还缺" . abs($bundGoodsNum - $bundNum) . "件</font>";
            $activityCalculatorResult->setActivityTitle($title);
            $activityCalculatorResult->offSale();
            return $activityCalculatorResult;
        }
        //单价
        $singePrice = bcdiv($bundAmount, $bundNum, 2);
        //扣减总价
        if ($bundGoodsNum >= $bundNum) {
            foreach ($activityGoods as $goodsId => $value) {
                foreach ($value as $specId => $vo) {
                    $discountPrice = ($goodsData[$goodsId][$specId]['crazy_price'] - $singePrice) * $goodsData[$goodsId][$specId]['num'];
                    $discountAmount += $discountPrice;
                    $activityCalculatorResult->addDiscountPrice($goodsId, $specId, $discountPrice);
                }
            }
            $title = $activity['title'] . " 已满{$bundGoodsNum}件，共节省{$discountAmount}元";
            $activityCalculatorResult->setActivityTitle($title);
        }

        return $activityCalculatorResult;
    }
}
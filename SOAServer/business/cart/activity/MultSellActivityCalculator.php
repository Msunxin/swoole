<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/3
 * Time: 20:11
 */

namespace SOAServer\business\cart\activity;

/**
 * 多件起售活动计算
 * Class MultSellActivityCalculator
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
class MultSellActivityCalculator extends ActivityBase
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

        $sellGoodsNum = 0;
        $sellNum = $activity['sell_num'];                     //起售数量
        $isOriginalPrice = $activity['is_original_price'];   //多件起售形式：1--商品原价，2--统一价
        $singePrice = $activity['sale_amount'];            //商品单价
        $shortIsSell = $activity['is_sell_num'];          //低于起售商品数量时，按原价购买
        $activityGoods = array();
        $discountAmount = 0;

        //整理参加活动的商品
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                if (in_array($goodsId, $specialGoods)) {
                    $activityGoods[$goodsId][$specId]['num'] = $vo['num'];
                    $sellGoodsNum += $vo['num'];
                }
            }
        }

        if (!$sellGoodsNum) {
            $activityCalculatorResult->setActivityTitle($activity['title']);
            return $activityCalculatorResult;
        }

        //计算数量限制
        if (($isOriginalPrice == 1 && $sellGoodsNum < $sellNum) || ($isOriginalPrice == 2 && $shortIsSell == 0 && $sellGoodsNum < $sellNum)) { //低于商品数量，不能购买
            $title = $activity['title'] . " <font color='red'>已满{$sellGoodsNum}件，还缺" . abs($sellNum - $sellGoodsNum) . "件</font>";
            $activityCalculatorResult->setActivityTitle($title);
            $activityCalculatorResult->offSale();
            return $activityCalculatorResult;
        }
        //扣减总价
        if ($sellGoodsNum >= $sellNum) {
            foreach ($activityGoods as $goodsId => $value) {
                foreach ($value as $specId => $vo) {
                    if ($isOriginalPrice == 1) {
                        $discountPrice = 0;
                    } else {
                        $discountPrice = ($goodsData[$goodsId][$specId]['crazy_price'] - $singePrice) * $vo['num'];
                    }
                    $discountAmount += $discountPrice;
                    $activityCalculatorResult->addDiscountPrice($goodsId, $specId, $discountPrice);
                }
            }

            $title = $activity['title'] . " 已满{$sellGoodsNum}件，共节省{$discountAmount}元";
            $activityCalculatorResult->setActivityTitle($title);
        } else {
            $title = $activity['title'] . " <font color='red'>已满{$sellGoodsNum}件，还缺" . abs($sellNum - $sellGoodsNum) . "件</font>";
            $activityCalculatorResult->setActivityTitle($title);
        }

        return $activityCalculatorResult;
    }

}
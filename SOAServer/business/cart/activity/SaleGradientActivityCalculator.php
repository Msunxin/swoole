<?php

namespace SOAServer\business\cart\activity;

use \mkf\helpers\RowsHelper;

/**
 * 多件阶梯<br />
 * 即：满X1件Y1元，满X2件Y2元，满X3件Y3元
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class SaleGradientActivityCalculator extends ActivityBase {

    public function calculate(ActivityCalculatorData $activityCalculatorData) {
        $activity = $activityCalculatorData->getActivity();
        $goodsData = $activityCalculatorData->getGoodsData();
        $specialGoods = $activityCalculatorData->getActivityGoods();

        $activityCalculatorResult = $this->activityCalculatorResult;
        if (!$activityCalculatorResult) {
            $activityCalculatorResult = new ActivityCalculatorResult();
        }

        $spec = $this->getSpecList($goodsData);     //所有规格列表
        //按价格字段正序排序
        RowsHelper::sortRowsByField($spec, 'crazy_price');

        $is_sale_not_enough = $activity['is_sale_gradient_original_price'];    //不满足条件可以按原价购买
        $saleGradientItems = $this->formatGradientData(unserialize($activity['sale_gradient_text']));   //阶梯配置数据

        $count = $this->getActivityGoodsCount($spec, $specialGoods);     //参加此活动的商品(规格)数量

        if ($count == 0) {
            $activityCalculatorResult->setActivityTitle($activity['title']);
            return $activityCalculatorResult;
        }

        $max = $this->getMaxGradientItem($count, $saleGradientItems);

        //不满足条件不能购买
        if ($is_sale_not_enough == 0 && $max == 0) {
            $activityCalculatorResult->offSale();
        }

        $activity_spec = array();       //参与了活动的规格列表
        $activity_amount = 0;
        foreach ($spec as $vo) {
            if (in_array($vo['goods_id'], $specialGoods) && $max > 0) {
                $num = $max - $vo['num'] >= 0 ? $vo['num'] : $max;
                $last_goods_id = $vo['goods_id'];
                $last_spec_id = $vo['spec_id'];
                $vo['special_id'] = $activity['special_id'];
                $vo['activity_num'] = $num;
                $activity_spec[] = $vo;
                $activity_amount += ($vo['crazy_price'] * $num) - $activityCalculatorResult->getGoodsDiscountPrice($vo['goods_id'], $vo['spec_id']);
                $max -= $num;
            }
        }

        $discount_amount = 0;       //扣减金额总额

        $enough = false;
        foreach ($saleGradientItems as $saleGradientItem) {
            if ($count >= $saleGradientItem['num']) {
                $enough = true;
                $discount_amount = $activity_amount - $saleGradientItem['amount'];
            }
        }

        if ($enough) {
            $reduction_price = $discount_amount;        //扣减金额里边的剩余部分
            foreach ($activity_spec as $vo) {
                if ($vo['goods_id'] == $last_goods_id && $vo['spec_id'] == $last_spec_id) {
                    $discount = $reduction_price;
                } else {
                    $discount = bcmul(($vo['crazy_price'] * $vo['num'] - $activityCalculatorResult->getGoodsDiscountPrice($vo['goods_id'], $vo['spec_id'])) / $activity_amount, $discount_amount, 2);         //按比例分配扣减金额
                    $reduction_price -= $discount;
                }
                $activityCalculatorResult->addDiscountPrice($vo['goods_id'], $vo['spec_id'], $discount);
            }
        }

        $activityCalculatorResult->setActivityTitle($activity['title']);

        return $activityCalculatorResult;
    }

    /**
     * 从商品数据中捞出商品规格列表
     * @param array $goodsData 购物车商品数据(蛋疼的二维数组)
     * @return array 规格列表
     */
    private function getSpecList($goodsData) {
        $specList = array();
        foreach ($goodsData as $goods_id => $specMap) {
            foreach ($specMap as $spec_id => $spec) {
                $specList[] = $spec;
            }
        }
        return $specList;
    }

    /**
     * 格式化阶梯数据，按照“件数”正序排序返回
     * @param array $rawGradientData 原始阶梯数据
     * @return array
     */
    private function formatGradientData($rawGradientData) {
        $saleGradientItems = array();
        foreach ($rawGradientData as $item) {
            if ($item['count'] && $item['amount']) {
                $saleGradientItems[] = array(
                    'num' => $item['count'],
                    'amount' => $item['amount'],
                );
            }
        }

        RowsHelper::sortRowsByField($saleGradientItems, 'num');

        return $saleGradientItems;
    }

    /**
     * 计算出参加此活动的商品(规格)数量
     * @param array $specList 购物车所有规格列表
     * @param array $specialGoodsIdList 此活动(专题)的所有商品id列表
     * @return type
     */
    private function getActivityGoodsCount($specList, $specialGoodsIdList) {
        $count = 0;
        foreach ($specList as $spec) {
            if (in_array($spec['goods_id'], $specialGoodsIdList)) {
                $count += $spec['num'];
            }
        }
        return $count;
    }

    /**
     * 取得满足条件的最大阶梯项
     * @param int $activityGoodsCount 此活动的商品数
     * @param array $gradientItems 阶梯配置列表
     * @return int 返回 0 则表示未找到满足条件的阶梯项
     */
    private function getMaxGradientItem($activityGoodsCount, $gradientItems) {
        $max = 0;
        foreach ($gradientItems as $gradientItem) {
            if ($activityGoodsCount >= $gradientItem['num']) {
                $max = $gradientItem['num'];
            }
        }
        return $max;
    }

}

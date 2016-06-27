<?php

namespace SOAServer\business\cart\activity;

/**
 * 专区满额减
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class SpecialFullOffActivityCalculator extends ActivityBase {

    public function calculate(ActivityCalculatorData $activityCalculatorData) {
        $activity = $activityCalculatorData->getActivity();
        $goodsData = $activityCalculatorData->getGoodsData();
        $specialGoods = $activityCalculatorData->getActivityGoods();

        $activityCalculatorResult = $this->activityCalculatorResult;
        if (!$activityCalculatorResult) {
            $activityCalculatorResult = new ActivityCalculatorResult();
        }

        $is_full_off_gradient = $activity['is_full_off_gradient']; //是否阶梯
        if ($is_full_off_gradient) {
            $full_off_gradient_text = unserialize($activity['full_off_gradient_text']); //阶梯数据
            //整理阶梯数据
            $full_off_gradient = array();
            foreach ($full_off_gradient_text as $vo) {
                if ($vo['amount'] > 0) {
                    $full_off_gradient[$vo['amount']] = $vo['discount'];
                }
            }
            ksort($full_off_gradient);
        } else {
            $full_off_amount = $activity['full_off_amount'];   //(非阶梯)满足金额
            $full_off_discount = $activity['full_off_discount'];   //(非阶梯)扣减金额
            $is_full_off_cumulative = $activity['is_full_off_cumulative']; //(非阶梯)0:不累加 1:累加
        }

        $amount = 0;
        foreach ($goodsData as $goods_id => $vo) {
            foreach ($vo as $spec_id => $v) {
                if (in_array($goods_id, $specialGoods)) {
                    $last_goods_id = $goods_id;
                    $last_spec_id = $spec_id;
                    $amount += $v['crazy_price'] * $v['num'] - $activityCalculatorResult->getGoodsDiscountPrice($v['goods_id'], $v['spec_id']);
                }
            }
        }
        if ($amount == 0) {
            return null;
        }

        $discount_amount = 0;

        $enough = false;
        if ($is_full_off_gradient) {    //阶梯
            foreach ($full_off_gradient as $gradient_amount => $discount) {
                $short_price = $gradient_amount - $amount;
                if ($amount >= $gradient_amount) {
                    $enough = true;
                    $discount_amount = $discount;
                } else {
                    break;
                }
            }
        } else {    //满减
            if ($amount >= $full_off_amount) {
                $enough = true;
                if ($is_full_off_cumulative) {  //累加
                    $discount_amount = $full_off_discount * floor($amount / $full_off_amount);
                } else {
                    $discount_amount = $full_off_discount;
                }
            } else {
                $short_price = $full_off_amount - $amount;
            }
        }
        if ($enough) {
            $reduction_price = $discount_amount;
            foreach ($goodsData as $goods_id => $vo) {
                foreach ($vo as $spec_id => $v) {
                    if (in_array($goods_id, $specialGoods)) {
                        if ($goods_id == $last_goods_id && $spec_id == $last_spec_id) {
                            $discount = $reduction_price;
                        } else {
                            $discount = bcmul(bcdiv($v['crazy_price'] * $v['num'] - $activityCalculatorResult->getGoodsDiscountPrice($v['goods_id'], $v['spec_id']), $amount, 2), $discount_amount, 2);
                            $reduction_price -= $discount;
                        }
                        $activityCalculatorResult->addDiscountPrice($goods_id, $spec_id, $discount);
                    }
                }
            }
        } else {
            $activity['title'] = $activity['title'] . " <font color='red'>已满{$amount}元，还缺" . abs($short_price) . "元</font>";
        }

        $activityCalculatorResult->setActivityTitle($activity['title']);

        return $activityCalculatorResult;
    }

}

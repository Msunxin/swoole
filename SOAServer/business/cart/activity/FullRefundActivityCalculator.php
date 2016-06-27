<?php

namespace SOAServer\business\cart\activity;

/**
 * 全场满额减
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class FullRefundActivityCalculator extends ActivityBase {

    /**
     * @var \SOAServer\models\Mkfset 
     */
    private $mkfsetModel;

    public function setMkfsetModel($mkfsetModel) {
        $this->mkfsetModel = $mkfsetModel;
    }

    public function calculate(ActivityCalculatorData $activityCalculatorData) {
        $reduction = $this->getFullReduction();
        if (!$this->isFullReductionAvailable($reduction)) {
            return null;
        }

        $activityCalculatorResult = $this->activityCalculatorResult;
        if (!$activityCalculatorResult) {
            $activityCalculatorResult = new ActivityCalculatorResult();
        }

        $goodsData = $activityCalculatorData->getGoodsData();
        $goodsAmount = $this->getGoodsAmount($goodsData);  //订单总金额（原价）
        $amount = $this->getOrderAmount($goodsData, $activityCalculatorResult);         //订单应付金额

        $selectedReduction = null;

        foreach ($reduction['amount'] as $reductionConfig) {
            if ($amount < $reductionConfig['full_reduction']) {
                continue;
            }

            $selectedReduction = $reductionConfig;
            break;
        }

        //没有满足条件的
        if (is_null($selectedReduction)) {
            return null;
        }

        $reduction_price = $selectedReduction['price'];
        $reduction_price_bak = $reduction_price;

        $last_goods_id = array_keys($goodsData)[count($goodsData) - 1];
        $last_spec_id = array_keys($goodsData[$last_goods_id])[count($goodsData[$last_goods_id]) - 1];
        foreach ($goodsData as $goods_id => $vo) {
            foreach ($vo as $spec_id => $v) {
                if ($v['is_gift']) {
                    continue;
                }

                if ($goods_id == $last_goods_id && $spec_id == $last_spec_id) {
                    $discount = $reduction_price;
                } else {
                    $discount = bcmul(bcdiv($v['crazy_price'] * $v['num'], $goodsAmount, 2), $reduction_price_bak, 2);
                    $reduction_price -= $discount;
                }
                $activityCalculatorResult->addDiscountPrice($goods_id, $spec_id, $discount);
            }
        }

        $activityCalculatorResult->setActivityTitle('满' . $selectedReduction['full_reduction'] . '减' . $selectedReduction['price']);
        return $activityCalculatorResult;
    }

    private function getFullReduction() {
        $full_reduction = $this->mkfsetModel->getFullReduction();
        foreach ($full_reduction as $value) {
            switch ($value['setkey']) {
                case 'full_reduction_time' :
                    $data['start_time'] = $value['setvalue'];
                    $data['end_time'] = $value['setvalue1'];
                    break;
                case 'full_reduction_amount' :
                    $amount[$value['setvalue']] = $value['setvalue1'];
                    break;
                case 'full_reduction_open' :
                    $data['open'] = $value['setvalue'];
                    break;
            }
        }

        krsort($amount);         //按价格条件逆序排序(this is very important, haha)

        foreach ($amount as $key => $value) {
            $data['amount'][] = array('full_reduction' => $key, 'price' => $value);
        }
        return $data;
    }

    private function isFullReductionAvailable($fullReduction) {
        $now = time();
        return $fullReduction['open'] && $fullReduction['start_time'] <= $now &&
                $fullReduction['end_time'] >= $now;
    }

    /**
     * 取得商品总价（原价）
     * @param array $goodsData
     * @return float
     */
    private function getGoodsAmount($goodsData) {
        $goodsAmount = 0;
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                $goodsAmount += $vo['crazy_price'] * $vo['num'];
            }
        }
        return $goodsAmount;
    }

    /**
     * 取得订单应付金额
     * @param array $goodsData
     * @param ActivityCalculatorResult $activityCalculatorResult
     * @return float
     */
    private function getOrderAmount($goodsData, ActivityCalculatorResult $activityCalculatorResult) {
        $orderAmount = 0;
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                $discount = $activityCalculatorResult->getGoodsDiscountPrice($goodsId, $specId);
                $orderAmount += ($vo['crazy_price'] * $vo['num']) - $discount;
            }
        }
        return $orderAmount;
    }

}

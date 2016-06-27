<?php


namespace SOAServer\business\cart\activity;

/**
 * 优惠券
 * Class CouponActivityCalculator
 * @package SOAServer\business\cart\activity
 */
class CouponActivityCalculator extends ActivityBase
{
    private $couponModel;
    private $couponCodeModel;

    /**
     * @return mixed
     */
    public function getCouponModel()
    {
        if (!$this->couponModel) {
            $this->couponModel = new \SOAServer\models\Coupon();
        }
        return $this->couponModel;
    }

    /**
     * @param mixed $couponModel
     */
    public function setCouponModel($couponModel)
    {
        $this->couponModel = $couponModel;
    }

    /**
     * @return mixed
     */
    public function getCouponCodeModel()
    {
        if (!$this->couponCodeModel) {
            $this->couponCodeModel = new \SOAServer\models\CouponCode();
        }
        return $this->couponCodeModel;
    }

    /**
     * @param mixed $couponCodeModel
     */
    public function setCouponCodeModel($couponCodeModel)
    {
        $this->couponCodeModel = $couponCodeModel;
    }

    /**
     * 计算购物车商品价格
     * @param ActivityCalculatorData $activityCalculatorData
     * @return ActivityCalculatorResult
     */
    public function calculate(ActivityCalculatorData $activityCalculatorData)
    {
        $activityCalculatorResult = $this->activityCalculatorResult;

        $goodsData = $activityCalculatorData->getGoodsData();
        $couponCode = $activityCalculatorData->getCouponCode();
        $amount = 0;
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                $discount = $activityCalculatorResult->getGoodsDiscountPrice($goodsId, $specId);
                $amount += ($vo['crazy_price'] * $vo['num']) - $discount;
            }
        }

        if ($couponCode) {
            $couponCodeModel = $this->getCouponCodeModel();
            $couponCodeInfo = $couponCodeModel->getCouponCode($couponCode);

            $couponModel = $this->getCouponModel();
            $coupon = $couponModel->getCoupon($couponCodeInfo['coupon_id']);

            $enable = '1';
            $categoryIds = explode(',', $coupon['category_ids']);
            //获取计算优惠券的商品
            $couponGoodsAmount = 0;
            $couponSpec = array();
            $lastGoodsId = $lastSpecId = 0;
            foreach ($goodsData as $goodsId => $goods) {
                foreach ($goods as $specId => $spec) {
                    if (in_array($spec['cate'], $categoryIds)) {
                        $couponSpec[] = $spec;
                        $lastGoodsId = $spec['goods_id'];
                        $lastSpecId = $spec['spec_id'];
                        $discount = 0;
                        if ($activityCalculatorResult) {
                            $discount = $activityCalculatorResult->getGoodsDiscountPrice($goodsId, $specId);
                        }
                        $couponGoodsAmount += ($spec['crazy_price'] * $spec['num']) - $discount;
                    }
                }
            }
            if (!$couponSpec) {
                $enable = '0';
            }
            $couponAmount = 0;
            if ($coupon['coupon_type'] == 0) {  //现金券
                $couponAmount = $coupon['discount'];
            } elseif ($coupon['coupon_type'] == 1) {    //抵用券
                if ($couponGoodsAmount >= $coupon['use_condition_amount']) {
                    if ($coupon['is_accumulate']) { //是否累计
                        $couponAmount = $coupon['discount'] * floor($couponGoodsAmount / $coupon['use_condition_amount']);
                    } else {
                        $couponAmount = $coupon['discount'];
                    }
                } else {
                    $enable = '0';
                }
            }
            if ($couponAmount >= $couponGoodsAmount) {
                if ($couponGoodsAmount == $amount) {
                    $couponAmount = $couponGoodsAmount - 1;
                } else {
                    $couponAmount = $couponGoodsAmount;
                }
            }
            $tmpCouponAmount = $couponAmount;
            if ($enable) {
                foreach ($couponSpec as $value) {
                    if ($value['goods_id'] == $lastGoodsId && $value['spec_id'] == $lastSpecId) {
                        $discountPrice = $tmpCouponAmount;
                    } else {
                        $discountPrice = bcmul(($value['crazy_price'] * $value['num'] - $value['discount_fee']) / $couponGoodsAmount, $couponAmount, 2);
                    }

                    $tmpCouponAmount -= $discountPrice;
                    $activityCalculatorResult->addDiscountPrice($value['goods_id'], $value['spec_id'], $discountPrice);
                }
            }

            $activityCalculatorResult->setActivityTitle($coupon['title']);
        } else {
            $activityCalculatorResult->setActivityTitle('使用优惠券');
        }

        return $activityCalculatorResult;
    }

}
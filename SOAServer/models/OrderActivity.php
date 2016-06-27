<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/9
 * Time: 16:46
 */

namespace SOAServer\models;


use mkf\Model;

class OrderActivity extends Model
{
    private $activityType = array(
        'Bund' => 1,
        'FullRefund' => 2,
        'FullGift' => 3,
        'ShareRefund' => 4,
        'CrazyCoin' => 5,
        'MultSell' => 6,
        'CumuDiscount' => 7,
        'Coupon' => 8,
        'SpecialFullOff' => 9,
        'Together' => 10,
        'SaleGradient' => 11,
    );

    /**
     * 获取活动类型
     * @param $activityType
     * @return mixed
     * @throws \Exception
     */
    public function getActivityType($activityType)
    {
        if (!isset($this->activityType[$activityType])) {
            throw new \Exception("活动类型未设置", -468);
        }
        return $this->activityType[$activityType];
    }

    /**
     * 保存活动记录
     * @param $orderId
     * @param $activityData
     * @throws \Exception
     */
    public function saveActivity($orderId, $activityData)
    {
        foreach ($activityData as $activityId => $activity) {
            $activityPrice = 0;
            $jsonArray = array();
            foreach ($activity['activityDiscountPrice'][$activityId] as $goodsId => $goods) {
                foreach ($goods as $specId => $discountPrice) {
                    $activityPrice += $discountPrice;
                    if (isset($jsonArray[$goodsId])) {
                        $jsonArray[$goodsId] += $discountPrice;
                    } else {
                        $jsonArray[$goodsId] = $discountPrice;
                    }
                }
            }

            if ($activityPrice == 0) {
                continue;
            }

            $addData = array(
                'order_id' => $orderId,
                'activity_title' => $activity['activityTitle'],
                'activity_type' => $this->getActivityType($activity['activityType']),
                'activity_price' => $activityPrice,
                'special_id' => is_numeric($activityId) ? $activityId : 0,
                'goods_id' => json_encode($jsonArray),
            );
            $orderActivityId = $this->add($addData);
            if (!$orderActivityId) {
                throw new \Exception("订单提交失败，请稍后再试。", -447);
            }
        }
    }
}
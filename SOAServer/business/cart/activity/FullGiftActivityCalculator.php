<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/6
 * Time: 11:08
 */

namespace SOAServer\business\cart\activity;

use \SOAServer\models\Spec;
/**
 * 满额赠活动
 * Class TogetherActivityCalculator
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
class FullGiftActivityCalculator extends ActivityBase
{
    /**
     * @var Spec
     */
    private $specModel;

    /**
     * @return mixed
     */
    public function getSpecModel()
    {
        if (!$this->specModel) {
            $this->specModel = new Spec();
        }
        return $this->specModel;
    }

    /**
     * @param mixed $specModel
     */
    public function setSpecModel($specModel)
    {
        $this->specModel = $specModel;
    }


    /**
     * 计算购物车商品价格
     * @param ActivityCalculatorData $activityCalculatorData
     * @return ActivityCalculatorResult
     */
    public function calculate(ActivityCalculatorData $activityCalculatorData)
    {
        $activityCalculatorResult = new ActivityCalculatorFullGiftResult();
        $activityCalculatorResult->setActivityCalculatorResult($this->activityCalculatorResult);

        $activity = $activityCalculatorData->getActivity();
        $goodsData = $activityCalculatorData->getGoodsData();
        $specialGoods = $activityCalculatorData->getActivityGoods();

        $isCumulative = $activity['is_cumulative']; //是否累加
        $fullAmount = $activity['full_amount'];   //满足金额
        $giveGoodsNum = $activity['give_goods_num'];    //赠品数量
        $giveGoodsId = $activity['give_goods'];    //赠品id
        $goodsAmount = 0;
        foreach ($goodsData as $goodsId => $value) {
            if (in_array($goodsId, $specialGoods)) {
                foreach ($value as $specId => $vo) {
                    $discount = $activityCalculatorResult->getGoodsDiscountPrice($goodsId, $specId);
                    $goodsAmount += ($vo['crazy_price'] * $vo['num']) - $discount;
                }
            }
        }

        //初始赠品数量
        $giftNum = 1;
        if ($isCumulative) { //累加
            $giftNum = floor($goodsAmount / $fullAmount);
        }
        $giftNum = $giftNum > $giveGoodsNum ? $giveGoodsNum : $giftNum;

        //获取赠品
        $specModel = $this->getSpecModel();
        $giftGoodsSpec = $specModel->getGoodsStock($giveGoodsId);

        $specStocknum = array();
        $stocknum = 0;
        $giftSpecData = [];
        foreach ($giftGoodsSpec as $value) {
            if ($value['stocknum'] <= 0) {
                continue;
            }
            $giftSpecData[$value['goods_id']][$value['spec_id']] = $value;
            //拼接goods_id-spec_id
            $specStocknum[$value['goods_id'] . '-' . $value['spec_id']] = $value['stocknum'];
            $stocknum += $value['stocknum'];
        }

        if ($stocknum < $giftNum) {
            $giftNum = (int)$stocknum;
        }

        if ($giftNum == 0) {
            $title = $activity['title'] . ' 赠品已被领完';
            $activityCalculatorResult->setActivityTitle($title);
            return $activityCalculatorResult;
        }

        for ($i = 0; $i < $giftNum; $i++) {
            //重新计算key
            $specStocknumKey = array_keys($specStocknum);
            $specStocknumCount = count($specStocknum);
            if ($specStocknumCount) {
                $random = rand(0, $specStocknumCount - 1);

                //随机商品的goods_id-spec_id
                $key = $specStocknumKey[$random];
                list($goodsId, $specId) = explode('-', $key);

                $activityCalculatorResult->addChildGoods($goodsId, $specId, 1, ChildGoodsType::GOODS_TYPE_FULL_GIFT);

                //扣减库存
                $specStocknum[$key]--;
                if ($specStocknum[$key] <= 0) {
                    unset($specStocknum[$key]);
                }
            }
        }
        $title = $activity['title'] . ' 可领取' . $giftNum . '件赠品';
        $activityCalculatorResult->setActivityTitle($title);
        return $activityCalculatorResult;
    }

}
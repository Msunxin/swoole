<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/6
 * Time: 14:12
 */

namespace SOAServer\business\cart\activity;

use \SOAServer\models\UserAccount;

class CrazyCoinActivityCalculator extends ActivityBase
{
    /**
     * @var UserAccount
     */
    private $userAccountModel = null;

    public function setUserAccountModel($userAccountModel) {
        $this->userAccountModel = $userAccountModel;
    }

    public function getUserAccountModel() {
        if (!$this->userAccountModel) {
            $this->userAccountModel = new UserAccount();
        }
        return $this->userAccountModel;
    }

    /**
     * 计算购物车商品价格
     * @param ActivityCalculatorData $activityCalculatorData
     * @return ActivityCalculatorResult
     */
    public function calculate(ActivityCalculatorData $activityCalculatorData)
    {
        $activityCalculatorResult = $this->activityCalculatorResult;

        $userId = $activityCalculatorData->getUserId();
        $goodsData = $activityCalculatorData->getGoodsData();
        $amount = 0;
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                $discount = $activityCalculatorResult->getGoodsDiscountPrice($goodsId, $specId);
                $amount += ($vo['crazy_price'] * $vo['num']) - $discount;
            }
        }

        $userAccountModel = $this->getUserAccountModel();
        $coin = $userAccountModel->getUserCoin($userId);

        if ($coin) {
            if ($amount < 100) {
                $coinMax = 0;
            } elseif ($amount < 200) {
                $coinMax = 10;
            } elseif ($amount < 300) {
                $coinMax = 20;
            } else {
                $coinMax = 30;
            }
            $reward = $coin > $coinMax ? $coinMax : $coin;
            //扣减金额分摊
            $lastGoodsId = array_keys($goodsData)[count($goodsData) - 1];
            $lastSpecId = array_keys($goodsData[$lastGoodsId])[count($goodsData[$lastGoodsId]) - 1];
            $rewardTemp = $reward;
            foreach ($goodsData as $goodsId => $value) {
                foreach ($value as $specId => $vo) {
                    if ($goodsId == $lastGoodsId && $specId == $lastSpecId) {
                        $activityCalculatorResult->addDiscountPrice($goodsId, $specId, $rewardTemp);
                    } else {
                        $discountPrice = bcmul(($vo['crazy_price'] * $vo['num'] - $vo['discount_fee']) / $amount, $reward, 2);
                        $activityCalculatorResult->addDiscountPrice($goodsId, $specId, $discountPrice);
                        $rewardTemp -= $discountPrice;
                    }
                }
            }
        }
        $title = "[疯狂币]满100用10； 满200用20；满300用30";
        $activityCalculatorResult->setActivityTitle($title);

        return $activityCalculatorResult;
    }
}
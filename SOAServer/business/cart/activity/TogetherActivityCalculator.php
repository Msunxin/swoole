<?php

namespace SOAServer\business\cart\activity;

use \SOAServer\models\Together;
/**
 * 群批活动计算
 * Class TogetherActivityCalculator
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
class TogetherActivityCalculator extends ActivityBase
{
    /**
     * @var Together
     */
    private $togetherModel;

    /**
     * @return mixed
     */
    public function getTogetherModel()
    {
        if (!$this->togetherModel) {
            $this->togetherModel = new Together();
        }
        return $this->togetherModel;
    }

    /**
     * @param mixed $togetherModel
     */
    public function setTogetherModel($togetherModel)
    {
        $this->togetherModel = $togetherModel;
    }

    /**
     * 计算购物车商品价格
     * @param ActivityCalculatorData $activityCalculatorData
     * @return ActivityCalculatorResult
     */
    public function calculate(ActivityCalculatorData $activityCalculatorData)
    {
        $activityCalculatorResult = new ActivityCalculatorTogetherResult();
        $activityCalculatorResult->setActivityCalculatorResult($this->activityCalculatorResult);

        $userId = $activityCalculatorData->getUserId();
        $goodsData = $activityCalculatorData->getGoodsData();

        $specIdArray = array();
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                $specIdArray[] = $specId;
            }
        }

        $togetherModel = $this->getTogetherModel();
        $together = $togetherModel->getUserTogether($userId, $specIdArray);

        foreach ($together as $res) {
            //计算单个商品扣减价格
            $togetherGoodsId = $res['goods_id'];
            $togetherSpecId = $res['spec_id'];
            $activityCalculatorResult->setTogetherGoodsId($togetherGoodsId);
            $activityCalculatorResult->setTogetherSpecId($togetherSpecId);
            $activityCalculatorResult->setTogetherId($res['id']);
            $discountPrice = ($goodsData[$togetherGoodsId][$togetherSpecId]['crazy_price'] - $activityCalculatorResult->getGoodsDiscountPrice($togetherGoodsId, $togetherSpecId)) * 0.2;
            $activityCalculatorResult->addDiscountPrice($togetherGoodsId, $togetherSpecId, $discountPrice);
            $activityCalculatorResult->addStockShopId($togetherGoodsId, $togetherSpecId, $res['shop_id']);
            if (!$res['recover'])   //是否需要扣减库存
                $activityCalculatorResult->addNoReduceStock($togetherGoodsId, $togetherSpecId);

            $title = '群批活动8折减' . $discountPrice . '元';

            $activityCalculatorResult->setActivityTitle($title);

            break;
        }

        return $activityCalculatorResult;
    }
}
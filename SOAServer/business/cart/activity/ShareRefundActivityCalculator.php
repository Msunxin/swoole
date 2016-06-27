<?php

namespace SOAServer\business\cart\activity;

/**
 * 分享立减
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ShareRefundActivityCalculator extends ActivityBase {

    /**
     * @var \SOAServer\models\ShareEvent
     */
    private $shareEventModel;

    /**
     * @var \SOAServer\models\ShareList
     */
    private $shareListModel;

    public function setShareEventModel($shareEventModel) {
        $this->shareEventModel = $shareEventModel;
    }

    public function setShareListModel($shareListModel) {
        $this->shareListModel = $shareListModel;
    }

    public function calculate(ActivityCalculatorData $activityCalculatorData) {
        $goodsData = $activityCalculatorData->getGoodsData();
        $userId = $activityCalculatorData->getUserId();
        $goodsIdArr = $this->getGoodsIdList($goodsData);

        $share_event = $this->getCurrentShareEvent();

        if (!$this->isShareEventAvailable($share_event)) {
            return null;
        }

        $activityCalculatorResult = $this->activityCalculatorResult;
        if (!$activityCalculatorResult) {
            $activityCalculatorResult = new ActivityCalculatorResult();
        }

        $totalReward = 0;           //总的扣减金额

        $shareList = $this->getUserGoodsShareList($userId, $goodsIdArr);
        foreach ($shareList as $share) {
            $share_count = $activityCalculatorData->getGoodsNum($share['goods_id']);
            foreach ($goodsData[$share['goods_id']] as $spec_id => $vo) {
                $discount_fee = $share_event['price'] * $goodsData[$share['goods_id']][$vo['spec_id']]['num'];
                $totalReward += $share_event['price'] * $share_count;
                $activityCalculatorResult->addDiscountPrice($share['goods_id'], $vo['spec_id'], $discount_fee);
            }
        }

        $activityCalculatorResult->setActivityTitle($share_event['event_name'] . ' 共减' . ($totalReward) . '元');

        return $activityCalculatorResult;
    }

    private function getGoodsIdList($goodsData) {
        $goodsIdList = array();
        foreach ($goodsData as $goods_id => $specMap) {
            foreach ($specMap as $spec_id => $spec) {
                $goodsIdList[] = $spec['goods_id'];
            }
        }
        return array_unique($goodsIdList);
    }

    private function getCurrentShareEvent() {
        return $this->shareEventModel->getShareEvent();
    }

    private function isShareEventAvailable($shareEvent) {
        $isWap = false;
        return !$isWap && $shareEvent['is_close'] == 0 &&
                $shareEvent['start_time'] <= time() && $shareEvent['end_time'] >= time();
    }

    private function getUserGoodsShareList($uid, $goodsIdArr) {
        return $this->shareListModel->getUserGoodsShareList($uid, $goodsIdArr);
    }

}

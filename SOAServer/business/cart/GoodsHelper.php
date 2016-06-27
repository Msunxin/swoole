<?php

namespace SOAServer\business\cart;
use SOAServer\models\Special;

/**
 * 购物车商品
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class GoodsHelper {

    public function getSpecialsWhichBelongTo($goodsIdArr) {
        $specialGoodsModel = new \SOAServer\models\SpecialGoods();
        $specialIdArr = $specialGoodsModel->getSpecialIdArrByGoodsIdArr($goodsIdArr);
        if (!$specialIdArr) {
            return array();
        }

        $specials = array();
        $specialModel = new Special();
        $specialArr = $specialModel->getSpecialsByIdArr($specialIdArr);
        foreach ($specialArr as $special) {
            if ($this->shouldIncluded($special)) {
                $specials[] = $special;
            }
        }

        return $specials;
    }

    private function shouldIncluded($special) {
        $now = time();
        $typeCondition = $special['type'] == 1 && $special['is_auto_up'] || $special['type'] == 3;
        if (!$typeCondition) {
            return true;
        }
        if ($now < $special['start_time'] || $now > $special['end_time']) {
            return false;
        }
        return true;
    }

}

<?php

namespace SOAServer\models;

use mkf\Model;

class OrderSpec extends Model
{
    /**
     * 创建订单保存order_spec记录
     * @param $orderId
     * @param $ogId
     * @param $spec
     * @param int $noReduceStock
     * @param int $stockShopId
     * @throws \Exception
     */
    public function saveOrderSpec($orderId, $ogId, $spec, $noReduceStock = 0, $stockShopId = 0)
    {
        $map = array();
        $map['order_id'] = $orderId;
        $map['goods_id'] = $spec['goods_id'];
        $map['og_id'] = $ogId;
        $map['goods_no'] = $spec['goods_no'];
        $map['num'] = $spec['num'];
        $map['price'] = $spec['crazy_price'];
        $map['spec_id'] = $spec['spec_id'];
        $map['attr'] = $spec['attr'];
        $map['is_defective'] = $spec['is_defective'];
        $map['supplier_id'] = $spec['supplier_id'];
        $map['bathch'] = $spec['bathch'];
        $map['is_gift'] = (int)$spec['is_gift'];
        $map['gift_special_id'] = (int)$spec['gift_special_id'];
        $map['is_pack_goods'] = (int)$spec['is_pack_goods'];
        $map['is_pack'] = (int)$spec['is_pack'];
        $map['discount_fee'] = (double)$spec['discount_fee'];
        $map['crazy_coin'] = (double)$spec['crazy_coin'];
        $map['money_ratio'] = (double)$spec['money_ratio'];
        $map['money_ratio_after'] = (double)$spec['money_ratio_after'];
        $map['random_pack'] = (int)$spec['random_pack'];
        $map['is_random'] = (int)$spec['is_random'];
        $map['is_fbs'] = (int)$spec['is_fbs'];
        $map['fbs_version'] = $spec['fbs_version'];
        $map['coupon_price'] = (double)$spec['coupon_price'];
        $map['source_special_id'] = (int)$spec['source_special_id'];
        $map['is_child'] = (int)$spec['is_child'];
        $map['goods_type'] = (int)$spec['goods_type'];
        //spec_stock表扣减库存条件 START
        if ($noReduceStock) {
            $map['shop_id'] = $stockShopId;//更新OrderSpec表shop_id
            $osId = $this->add($map);  //订单规格表
            if ($osId === false) {
                throw new \Exception("订单提交失败，请稍后再试。", -419);
            }
        } else {
            $specStockModel = new SpecStock();
            $shopIds = $specStockModel->getSpecStock($spec['spec_id'], $stockShopId);

            //主键查询条件
            $total = 0;
            foreach ($shopIds as $key => $val) {
                $total += $val['stocknum'];
            }

            if ($total < $spec['num']) {
                throw new \Exception("商品{$spec['goods_name']}库存不足", -456);
            }

            $stockNum = $spec['num'];
            foreach ($shopIds as $va) {
                if ($stockNum <= 0) {
                    break;
                }

                if ($va['stocknum'] >= $stockNum) {
                    $reduceStockNum = $stockNum;
                } else {
                    $reduceStockNum = $va['stocknum'];
                }
                $map['num'] = $reduceStockNum;      //更新OrderSpec表stocknum
                $map['shop_id'] = $va['shop_id'];   //更新OrderSpec表shop_id

                //扣减库存
                $specStockModel->decreaseStockNum($va['stock_id'], $reduceStockNum);

                $stockNum -= $reduceStockNum;

                //添加日志
                $osId = $this->add($map);  //订单规格表

                if ($osId === false) {
                    throw new \Exception("订单提交失败，请稍后再试。", -419);
                }
            }
        }
    }
}
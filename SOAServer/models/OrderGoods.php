<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/9
 * Time: 18:02
 */

namespace SOAServer\models;


use SOAServer\models\Goods;
use mkf\Model;

class OrderGoods extends Model
{

    /**
     * 创建订单保存order_goods记录
     * @param $orderId
     * @param $goods
     * @return int
     * @throws \Exception
     */
    public function saveOrderGoods($orderId, $goods)
    {
        $og_id = $this->add(array(
            'order_id' => $orderId,
            'goods_id' => $goods['goods_id'],
            'amount' => $goods['amount'],
            'num' => $goods['num'],
            'skuorder' => $goods['skuorder'],
            'goods_name' => $goods['goods_name'],
            'supplier_id' => $goods['supplier_id'],
            'bathch' => $goods['bathch'],
        ));
        if (!$og_id) {
            throw new \Exception("订单提交失败，请稍后再试。", -417);
        }
        if ($goods['saletype'] == 3) {
            //TODO: 限量抢购扣减数量
//            $goodsModel = new Goods();
//
//            $map = array();
//            $map['goods_id'] = $value['goodsId'];
//            $map['realnum'] = array('exp', "realnum-{$value['goodsNum']}");
//            if (($value['realnum'] - $value['goodsNum']) < $value['surplus']) {
//                $surplus_num = $value['surplus'] - ($value['realnum'] - $value['goodsNum']);
//                $map['surplus'] = array('exp', "surplus-{$surplus_num}");
//            }
//            $map['realnum'] = array('exp', "realnum-{$data[$value['goodsId']]['num']}");
//            $id = M('Goods')->save($map);
//            if ($id === FALSE) {
//                setapilog(2, 1, base64_encode(json_encode(array('result' => -418, 'msg' => $this->error[-418], 'data' => $map))), 3, 2);
//                $m->rollback();
//                $this->error(-418);
//            }
        }
        return $og_id;
    }
}
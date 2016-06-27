<?php

namespace SOAServer\models;

/**
 * 专题活动model
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Special extends \mkf\Model
{

    public function getSpecialsByIdArr($specialIdArr)
    {
        $fields = 'special_id,title,content,type,is_auto_up,open_time,close_time,start_time,' .
            'end_time,is_reduce_price,reduce_price,is_bunding,bund_num,is_sale_bund,bund_amount,' .
            'bund_max,is_fullgive,full_amount,is_cumulative,give_goods,give_goods_num,is_sell,' .
            'sell_num,is_sell_num,is_discount,discount_text,sale_amount,is_original_price,' .
            'is_full_off,is_full_off_gradient,full_off_amount,full_off_discount,is_full_off_cumulative,' .
            'full_off_gradient_text,is_sale_gradient,sale_gradient_text,is_sale_gradient_original_price,' .
            'special_active_type,is_fullnum_off,is_fullnum_off_gradient,fullnum_off_amount,' .
            'fullnum_off_discount,is_fullnum_off_cumulative,fullnum_off_gradient_text';
        $this->select($fields)->where('app_resource = 0')->addIn('special_id', $specialIdArr);
        return $this->findAll();
    }

    public function getSpecial($specialId)
    {
        $fields = array(
            'special_id', 'title', 'type', 'is_auto_up', 'open_time', 'close_time', 'start_time',
            'end_time', 'is_reduce_price', 'reduce_price', 'is_bunding', 'bund_num', 'is_sale_bund', 'bund_amount',
            'bund_max', 'is_fullgive', 'full_amount', 'is_cumulative', 'give_goods', 'give_goods_num', 'is_sell',
            'sell_num', 'is_sell_num', 'is_discount', 'discount_text', 'sale_amount', 'is_original_price',
            'is_full_off', 'is_full_off_gradient', 'full_off_amount', 'full_off_discount', 'is_full_off_cumulative',
            'full_off_gradient_text', 'is_sale_gradient', 'sale_gradient_text', 'is_sale_gradient_original_price',
            'special_active_type', 'is_fullnum_off', 'is_fullnum_off_gradient', 'fullnum_off_amount',
            'fullnum_off_discount', 'is_fullnum_off_cumulative', 'fullnum_off_gradient_text'
        );
        return $this->select($fields)->where("special_id = :special_id")->setParameter('special_id', $specialId)
            ->find();
    }

    /**
     * 创建订单时扣减赠品总数
     * @param $specialId
     * @param $giftNum
     * @throws \Exception
     */
    public function decreaseGiftNum($specialId, $giftNum)
    {
        $row = $this->where('special_id = :special_id')
            ->setParameter('special_id', $specialId)
            ->save(array(
                'give_goods_num' => array('sql', "give_goods_num - {$giftNum}"),
            ));
        if (!$row) {
            throw new \Exception("订单提交失败，请稍后再试。", -417);
        }
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/6
 * Time: 21:46
 */

namespace SOAServer\business\cart\activity;


class ActivityCalculatorFullGiftResult extends ActivityCalculatorResult
{
    /**
     * @var array 活动生成的商品
     */
    private $childGoods = array();

    /**
     * 增加活动产生的商品信息
     * @param $goodId
     * @param $specId
     * @param $type 1:满额赠赠品
     */
    public function addChildGoods($goodId, $specId, $num, $type)
    {
        $this->childGoods[] = array(
            'goods_id' => $goodId,
            'spec_id' => $specId,
            'num' => $num,
            'type' => $type,
        );
    }

    /**
     * @return array
     */
    public function getChildGoods()
    {
        return $this->childGoods;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['childGoods'] = $this->getChildGoods();
        return $array;
    }
}
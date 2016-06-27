<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/11
 * Time: 22:56
 */

namespace SOAServer\models;


use mkf\Model;

class PackGoods extends Model
{
    /**
     * 获取打包商品
     * @param $packGoodsId
     * @param $packSpecId
     * @return mixed
     */
    public function getPackGoods($packGoodsId, $packSpecId)
    {
        $whereString = 'pack_id = :pack_id';
        $whereString .= ' AND pack_spec_id = :pack_spec_id';
        return $this->select(array('spec_id', 'goods_id'))
            ->where($whereString)
            ->setParameter('pack_id', $packGoodsId)
            ->setParameter('pack_spec_id', $packSpecId)
            ->findAll();
    }
}
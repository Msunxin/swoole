<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/2
 * Time: 10:55
 */

namespace SOAServer\models;

use mkf\Model;

class Goodscollect extends Model
{
    /**
     * 获取商品状态
     * @param int|string|array $goodsId
     * @param string|array $field
     */
    public function getGoodsStatus($goodsId, $field = '*')
    {
        if (!is_array($goodsId)) {
            $goodsId = explode(',', $goodsId);
        }
        $whereString = $this->queryBuilder->expr()->in('goods_id', $goodsId);
        $whereString .= ' and type = :type';
        $goodsStatus = $this->select($field)->where($whereString)->setParameter('type', 0)->findAll();
        $goodsStatusArray = array();
        foreach ($goodsStatus as $value) {
            $goodsStatusArray[$value['goods_id']] = $value;
        }
        return $goodsStatusArray;
    }
}
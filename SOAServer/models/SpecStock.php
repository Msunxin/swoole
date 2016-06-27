<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/9
 * Time: 20:03
 */

namespace SOAServer\models;


use mkf\Model;

class SpecStock extends Model
{

    /**
     * 获取库存
     * @param $specId
     * @param $stockShopId
     * @return array
     */
    public function getSpecStock($specId, $stockShopId)
    {
        $whereString = 'spec_id = :spec_id';
        $whereString .= ' AND stocknum > 0';
        if ($stockShopId) {
            $whereString .= ' AND shop_id = :shop_id';
        } else {
            $whereString .= ' AND shop_id > 0';
        }

        $this->select("*")->where($whereString);
        $this->setParameter('spec_id', $specId);
        if ($stockShopId) {
            $this->setParameter('shop_id', $stockShopId);
        }
        return $this->findAll();
    }

    /**
     * 扣减库存
     * @param $stockId
     * @param $stocknum
     * @throws \Exception
     */
    public function decreaseStockNum($stockId, $stocknum)
    {
        $ssId = $this->where('stock_id = :stock_id')
            ->setParameter('stock_id', $stockId)
            ->save(array(
                'stocknum' => array('sql', "stocknum-{$stocknum}"),
                'ordernum' => array('sql', "ordernum+{$stocknum}"),
            ));
        if (!$ssId) {
            throw new \Exception("订单提交失败，请稍后再试。", -419);
        }
    }
}
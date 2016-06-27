<?php

namespace SOAServer\models;

use mkf\Model;

/**
 * Class Spec
 * @package SOAServer\models
 */
class Spec extends Model
{
    /**
     * @param int|array $goodsId 商品编号
     * @param string|array $field 获取的字段
     * @return array
     */
    public function getGoodsSpec($goodsId, $specId, $field = '*')
    {
        if (!is_array($goodsId)) {
            $goodsId = explode(',', $goodsId);
        }
        $whereString = $this->queryBuilder->expr()->in('goods_id', $goodsId);
        $whereString .= ' and is_putaway = :is_putaway';
        $spec = $this->select($field)->where($whereString)->setParameter('is_putaway', 1)->findAll();
        $specArray = array();
        foreach ($spec as $value) {
            $specArray[$value['goods_id']][$value['spec_id']] = $value;
        }
        return $specArray;
    }

    public function getSpecBySpecId($specId, $field = '*')
    {
        if (!is_array($specId)) {
            $specId = explode(',', $specId);
        }
        $whereString = $this->queryBuilder->expr()->in('spec_id', $specId);
        $whereString .= ' and is_putaway = 1';
        $spec = $this->select($field)->where($whereString)->findAll();
        $specArray = array();
        foreach ($spec as $value) {
            $specArray[$value['spec_id']] = $value;
        }
        return $specArray;
    }

    /**
     * 获取规格金额
     * @param int|array|string $specId
     * @return float|array
     */
    public function getSpecPrice($specId)
    {
        if (is_numeric($specId)) {
            $spec = $this->select('crazy_price')->where('spec_id = :spec_id')->setParameter('spec_id', $specId)->find();
            $result = $spec['crazy_price'];
        } else {
            if (!is_array($specId)) {
                $specId = explode(',', $specId);
            }
            $whereString = $this->queryBuilder->expr()->in('spec_id', $specId);
            $spec = $this->select(array('spec_id', 'crazy_price'))->where($whereString)->findAll();
            $result = array();
            foreach ($spec as $value) {
                $result[$value['spec_id']] = $value['crazy_price'];
            }
        }
        return $result;
    }

    public function getSpecActivityData($specId)
    {
        if (!is_array($specId)) {
            $specId = explode(',', $specId);
        }
        $whereString = $this->queryBuilder->expr()->in('spec_id', $specId);
        $fields = array('goods_id', 'spec_id', 'crazy_price');
        $spec = $this->select($fields)->where($whereString)->findAll();
        $result = array();
        foreach ($spec as $value) {
            $value['discount_fee'] = 0;
            $result[$value['goods_id']][$value['spec_id']] = $value;
        }
        return $result;
    }

    /**
     * @param int|array $goodsId 商品编号
     * @param string|array $field 获取的字段
     * @return array
     */
    public function getGoodsStock($goodsId)
    {
        if (!is_array($goodsId)) {
            $goodsId = explode(',', $goodsId);
        }
        $whereString = $this->queryBuilder->expr()->in('goods_id', $goodsId);
        $whereString .= ' and is_putaway = :is_putaway';
        $spec = $this->select(array('goods_id', 'spec_id', 'stocknum'))
            ->where($whereString)
            ->setParameter('is_putaway', 1)
            ->findAll();
        return $spec;
    }

    /**
     * 根据规格id获取库存
     * @param $specIds
     * @param bool $isPutaway
     * @return array
     */
    public function getStockBySpecId($specIds, $isPutaway = true)
    {
        if (!is_array($specIds)) {
            $specIds = explode(',', $specIds);
        }
        $whereString = $this->queryBuilder->expr()->in('spec_id', $specIds);
        if ($isPutaway) {
            $whereString .= ' and is_putaway = 1';
        }
        $spec = $this->select(array('spec_id', 'stocknum'))
            ->where($whereString)
            ->findAll();
        $specStock = array();
        foreach ($spec as $value) {
            $specStock[$value['spec_id']] = $value['stocknum'];
        }
        return $specStock;
    }

    /**
     * 扣减库存数量
     * @param $specId
     * @param $num
     * @throws \Exception
     */
    public function decreaseStockNum($specId, $num)
    {
        $whereString = 'spec_id = :spec_id';
        $whereString .= ' AND stocknum >= :stocknum';

        $row = $this->where($whereString)
            ->setParameter('spec_id', $specId)
            ->setParameter('stocknum', $num)
            ->save(array(
                'stocknum' => array('sql', "stocknum - {$num}"),
                'ordernum' => array('sql', "ordernum + {$num}"),
            ));
        if (!$row) {
            throw new \Exception("订单提交失败，请稍后再试。", -419);
        }
    }

    public function getCartSpec($specId)
    {
        if (!is_array($specId)) {
            $specId = explode(',', $specId);
        }
        $whereString = $this->queryBuilder->expr()->in('spec_id', $specId);
        $whereString .= ' and is_putaway = 1';
        $field = array('spec_id AS specId', 'goods_id AS goodsId', 'stocknum', 'crazy_price AS crazyPrice',
            'skusize', 'color',
        );
        $spec = $this->select($field)->where($whereString)->findAll();
        $specArray = array();
        foreach ($spec as $value) {
            $specArray[$value['specId']] = $value;
        }
        return $specArray;
    }
}
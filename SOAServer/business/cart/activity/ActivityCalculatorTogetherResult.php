<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/6
 * Time: 21:46
 */

namespace SOAServer\business\cart\activity;


class ActivityCalculatorTogetherResult extends ActivityCalculatorResult
{
    /**
     * @var array 商品的店铺id
     */
    private $stockShopId = array();

    /**
     * @var array 不扣减库存的商品
     */
    private $noReduceStock = array();

    /**
     * @var int 群批活动商品id
     */
    private $togetherGoodsId = 0;

    /**
     * @var int 群批活动规格id
     */
    private $togetherSpecId = 0;

    /**
     * @var int 群批活动id
     */
    private $togetherId = 0;

    /**
     * @return int
     */
    public function getTogetherId()
    {
        return $this->togetherId;
    }

    /**
     * @param int $togetherId
     */
    public function setTogetherId($togetherId)
    {
        $this->togetherId = $togetherId;
    }

    /**
     * @return int
     */
    public function getTogetherGoodsId()
    {
        return $this->togetherGoodsId;
    }

    /**
     * @param int $togetherGoodsId
     */
    public function setTogetherGoodsId($togetherGoodsId)
    {
        $this->togetherGoodsId = $togetherGoodsId;
    }

    /**
     * @return int
     */
    public function getTogetherSpecId()
    {
        return $this->togetherSpecId;
    }

    /**
     * @param int $togetherSpecId
     */
    public function setTogetherSpecId($togetherSpecId)
    {
        $this->togetherSpecId = $togetherSpecId;
    }

    /**
     * @return array
     */
    public function getStockShopId()
    {
        return $this->stockShopId;
    }

    /**
     * @param $goodId
     * @param $specId
     * @param $shopId
     */
    public function addStockShopId($goodId, $specId, $shopId)
    {
        $this->stockShopId[$goodId][$specId] = $shopId;
    }

    /**
     * @return array
     */
    public function getNoReduceStock()
    {
        return $this->noReduceStock;
    }

    /**
     * @param $goodId
     * @param $specId
     */
    public function addNoReduceStock($goodId, $specId)
    {
        $this->noReduceStock[$goodId][$specId] = true;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['stockShopId'] = $this->getStockShopId();
        $array['noReduceStock'] = $this->getNoReduceStock();
        $array['togetherGoodsId'] = $this->getTogetherGoodsId();
        $array['togetherSpecId'] = $this->getTogetherSpecId();
        $array['togetherId'] = $this->getTogetherId();
        return $array;
    }
}
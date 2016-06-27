<?php

namespace SOAServer\business\cart\activity;

/**
 * 活动价格计算返回数据
 * Class ActivityCalculatorResult
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
class ActivityCalculatorResult
{
    /**
     * @var array 商品集合
     */
    private $goodsData = array();

    /**
     * @var string 返回活动标题
     */
    private $activityTitle = '';

    /**
     * @var bool 是否可以购买
     */
    private $isSale = true;

    /**
     * @var string 活动类型
     */
    private $activityType = '';

    /**
     * @var array 每个活动商品扣减的金额
     */
    private $activityDiscountPrice = array();

    /**
     * @var string 活动id
     */
    private $activityId = '';

    /**
     * @param string $activityId
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
    }

    /**
     * @return string
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @return array
     */
    public function getActivityDiscountPrice()
    {
        return $this->activityDiscountPrice;
    }

    /**
     * @param $goodsId
     * @param $specId
     * @param $discountPrice
     */
    public function setActivityDiscountPrice($goodsId, $specId, $discountPrice)
    {
        $activityId = $this->getActivityId();
        if (isset($this->activityDiscountPrice[$activityId][$goodsId][$specId])) {
            $this->activityDiscountPrice[$activityId][$goodsId][$specId] += $discountPrice;
        } else {
            $this->activityDiscountPrice[$activityId][$goodsId][$specId] = $discountPrice;
        }
    }

    /**
     * @return string
     */
    public function getActivityType()
    {
        return $this->activityType;
    }

    /**
     * @param string $activityType
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
    }


    public function getGoodsData()
    {
        return $this->goodsData;
    }

    /**
     * @param $goodsData
     */
    protected function setGoodsData($goodsData)
    {
        $this->goodsData = $goodsData;
    }

    /**
     * @return boolean
     */
    public function getIsSale()
    {
        return $this->isSale;
    }

    /**
     * @param boolean $isSale
     */
    public function setIsSale($isSale)
    {
        $this->isSale = $isSale;
    }


    /**
     * 关闭购买
     */
    public function offSale()
    {
        $this->isSale = false;
    }

    /**
     *
     * @param $goodsId
     * @param $specId
     * @param $discountPrice
     */
    public function addDiscountPrice($goodsId, $specId, $discountPrice)
    {
        if (isset($this->goodsData[$goodsId][$specId]['discount_fee'])) {
            $this->goodsData[$goodsId][$specId]['discount_fee'] += $discountPrice;
        } else {
            $this->goodsData[$goodsId][$specId]['discount_fee'] = $discountPrice;
        }
        $this->setActivityDiscountPrice($goodsId, $specId, $discountPrice);
    }

    public function getGoodsDiscountPrice($goodsId, $specId)
    {
        return isset($this->goodsData[$goodsId][$specId]['discount_fee']) ?
            $this->goodsData[$goodsId][$specId]['discount_fee'] : 0;
    }

    /**
     * @return string
     */
    public function getActivityTitle()
    {
        return $this->activityTitle;
    }

    /**
     * @param string $activityTitle
     */
    public function setActivityTitle($activityTitle)
    {
        $this->activityTitle = $activityTitle;
    }

    public function setActivityCalculatorResult(ActivityCalculatorResult $activityCalculatorResult)
    {
        $this->setActivityTitle($activityCalculatorResult->getActivityTitle());
        $this->setGoodsData($activityCalculatorResult->getGoodsData());
        $this->setIsSale($activityCalculatorResult->getIsSale());
    }

    public function toArray()
    {
        return array(
            'activityId' => $this->getActivityId(),
            'activityType' => $this->getActivityType(),
            'activityTitle' => $this->getActivityTitle(),
            'isSale' => $this->getIsSale(),
            'activityDiscountPrice' => $this->getActivityDiscountPrice(),
            'goodsData' => $this->getGoodsData(),
        );
    }


}
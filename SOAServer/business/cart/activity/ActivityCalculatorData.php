<?php

namespace SOAServer\business\cart\activity;

/**
 * 活动价格计算入参
 * Class ActivityCalculatorData
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
class ActivityCalculatorData
{
    /**
     * @var int|string 活动id（即special_id）
     */
    private $activityId;

    /**
     * @var array 商品集合
     */
    private $goodsData;
    
    /**
     * @var array 商品数量map   goods_id => goods_num
     */
    private $goodsNumMap = array();

    /**
     * @var float 商品实际支付价格
     */
    private $amount;

    /**
     * @var int 用户id
     */
    private $userId;

    /**
     * @var string 请求版本号
     */
    private $version;

    /**
     * @var string 优惠券编号
     */
    private $couponCode;

    /**
     * @var array 活动信息（即special）
     */
    private $activity;

    /**
     * @var string 活动类型
     */
    private $activityType;

    /**
     * @var array 专题商品id数组
     */
    private $activityGoods;
    
    /**
     * @var boolean 是否进行初始化
     */
    private $shouldDoInit = true;

    /**
     * @return string
     */
    public function getActivityType()
    {
        return $this->activityType;
    }

    /**
     * @return array
     */
    public function getActivityGoods()
    {
        return $this->activityGoods;
    }

    /**
     * @param array $activityGoods
     */
    public function setActivityGoods($activityGoods)
    {
        $this->activityGoods = $activityGoods;
    }

    /**
     * @param string $activityType
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
    }

    /**
     * @return int|string
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @param int|string $activityId
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
        if ($this->shouldDoInit) {
            $this->initActivity();
        }
    }

    /**
     * @return array
     */
    public function getGoodsData()
    {
        return $this->goodsData;
    }

    /**
     * 增加商品集合
     * @param $goodsId
     * @param $specId
     * @param $goodsNum
     */
    public function addGoodsData($goodsId, $specId, $goodsNum)
    {
        if (isset($this->goodsData[$goodsId][$specId]['num'])) {
            $this->goodsData[$goodsId][$specId]['num'] += $goodsNum;
        } else {
            $this->goodsData[$goodsId][$specId]['num'] = $goodsNum;
            if ($this->shouldDoInit) {
                $this->initGoods($goodsId, $specId);
            }
        }
        $this->updateGoodsNum($goodsId, $goodsNum);
    }
    
    private function updateGoodsNum($goodsId, $num) {
        if (isset($this->goodsNumMap[$goodsId])) {
            $this->goodsNumMap[$goodsId] += $num;
        } else {
            $this->goodsNumMap[$goodsId] = $num;
        }
    }
    
    public function getGoodsNum($goodsId) {
        return isset($this->goodsNumMap[$goodsId]) ? $this->goodsNumMap[$goodsId] : 0;
    }

    public function setSpecData($goodsId, $specId, $key, $value)
    {
        if (!isset($this->goodsData[$goodsId][$specId][$key])) {
            $this->goodsData[$goodsId][$specId][$key] = $value;
        }
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * @param string $couponCode
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;
    }

    /**
     * @return array
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param array $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    /**
     * 初始化活动信息
     * @param ActivityCalculatorData $activityCalculatorData
     */
    protected function initActivity()
    {
        $activityId = $this->getActivityId();
        if (is_numeric($activityId)) {
            $specialModle = new \SOAServer\models\Special();
            $special = $specialModle->getSpecial($activityId);
            $specialGoodsModel = new \SOAServer\models\SpecialGoods();
            $this->setActivityGoods($specialGoodsModel->getSpecialGoods($activityId));
            $this->setActivityType($this->queryActivityType($special));
            $this->setActivity($special);
        } else {
            $this->setActivityType($this->queryActivityType($activityId));
        }
    }

    /**
     * 从专区信息中获取活动类型
     * @param string|array $special
     * @return string
     */
    protected function queryActivityType($special)
    {
        $activityType = null;
        if ($special) {
            if ($special == 'crazyCoin') {
                $activityType = ActivityType::ACTIVITY_CRAZY_COIN;
            } elseif ($special == 'together') {
                $activityType = ActivityType::ACTIVITY_TOGETHER;
            } elseif ($special == 'coupon') {
                $activityType = ActivityType::ACTIVITY_COUPON;
            } else {
                if ($special['is_bunding']) {
                    $activityType = ActivityType::ACTIVITY_BUND_GOODS;
                } elseif ($special['is_sell']) {
                    $activityType = ActivityType::ACTIVITY_MULT_SELL;
                } elseif ($special['is_discount']) {
                    $activityType = ActivityType::ACTIVITY_CUMU_DISCOUNT;
                } elseif ($special['is_fullgive']) {
                    $activityType = ActivityType::ACTIVITY_FULL_GIFT;
                } elseif ($special['is_full_off']) {
                    $activityType = ActivityType::ACTIVITY_SPECIAL_FULL_OFF;
                }
            }
        }

        return $activityType;
    }

    /**
     * 初始化商品信息
     * @param ActivityCalculatorData $activityCalculatorData
     */
    protected function initGoods($goodsId, $specId)
    {
        $goodsModel = new \SOAServer\models\Goods();
        $goods = $goodsModel->getGoodsActivityData($goodsId);

        foreach ($goods as $goodsId => $value) {
            foreach ($this->goodsData[$goodsId] as $specId => $vo) {
                foreach ($value as $key => $v) {
                    $this->setSpecData($goodsId, $specId, $key, $v);
                }
            }
        }

        $specModel = new \SOAServer\models\Spec();
        $spec = $specModel->getSpecActivityData($specId);
        foreach ($spec as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                foreach ($vo as $key => $v) {
                    $this->setSpecData($goodsId, $specId, $key, $v);
                }
            }
        }
    }
    
    /**
     * 告知当前对象不要做初始化
     */
    public function doNotDoInit() {
        $this->shouldDoInit = false;
    }

}
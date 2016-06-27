<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/7
 * Time: 21:53
 */

namespace SOAServer\models;


use mkf\Model;

class OrderParam extends Model
{
    /**
     * @var int 用户id
     */
    private $userId = 0;

    /**
     * @var int 收件人地址id
     */
    private $addressId = 0;

    /**
     * @var int 支付方式
     */
    private $paymentId = 0;

    /**
     * @var array 商品数据
     */
    private $goodsData = array();

    /**
     * @var array 活动数据
     */
    private $activityData = array();

    /**
     * @var array 商品id
     */
    private $goodsId = array();

    /**
     * @var array 规格id
     */
    private $specId = array();

    /**
     * @var array 数量
     */
    private $num = array();

    /**
     * @var array 活动id
     */
    private $activityId = array();

    /**
     * @var array 活动是否选中
     */
    private $check = array();

    /**
     * @var string 备注
     */
    private $remark = '';

    /**
     * @var int 是否需要发票
     */
    private $isInvoice = 0;

    /**
     * @var string 发票抬头
     */
    private $invoiceTitle = '';

    /**
     * @var string 平台
     */
    private $os = '';

    /**
     * @var string 版本
     */
    private $version = '';

    /**
     * @var string 银行编号
     */
    private $bankCode = '';

    /**
     * @var string 是否代付
     */
    private $proxyType = '';

    /**
     * @var string 优惠券
     */
    private $couponCode = '';

    private $amount = 0;

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
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
        $this->userId = (int)$userId;
    }

    /**
     * @return array
     */
    public function getGoodsData()
    {
        return $this->goodsData;
    }

    /**
     * @param array $goodsData
     */
    public function setGoodsData($goodsData)
    {
        $this->goodsData = $goodsData;
    }

    /**
     * @return array
     */
    public function getActivityData()
    {
        return $this->activityData;
    }

    /**
     * @param array $activityData
     */
    public function setActivityData($activityData)
    {
        $this->activityData = $activityData;
        if (count($activityData)) {
            $lastActivityData = array_pop($activityData);
            foreach ($lastActivityData['goodsData'] as $goodsId => $goods) {
                foreach ($goods as $specId => $spec) {
                    $this->addDiscountFee($goodsId, $specId, $spec['discount_fee']);
                }
            }
            if (isset($lastActivityData['activityDiscountPrice']['crazyCoin'])) {
                foreach ($lastActivityData['activityDiscountPrice']['crazyCoin'] as $goodsId => $goods) {
                    foreach ($goods as $specId => $coin) {
                        $this->addDiscountCoin($goodsId, $specId, $coin);
                    }
                }
            }
        }
    }

    /**
     * @param $goodsId
     * @param $specId
     * @param $coin
     */
    public function addDiscountCoin($goodsId, $specId, $coin)
    {
        if (isset($this->goodsData[$goodsId][$specId]['crazy_coin'])) {
            $this->goodsData[$goodsId][$specId]['crazy_coin'] += $coin;
        } else {
            $this->goodsData[$goodsId][$specId]['crazy_coin'] = $coin;
        }
    }

    /**
     * @param $goodsId
     * @param $specId
     * @param $discountFee
     */
    public function addDiscountFee($goodsId, $specId, $discountFee)
    {
        if (isset($this->goodsData[$goodsId][$specId]['disount_fee'])) {
            $this->goodsData[$goodsId][$specId]['discount_fee'] += $discountFee;
        } else {
            $this->goodsData[$goodsId][$specId]['discount_fee'] = $discountFee;
        }
    }

    /**
     * @return array
     */
    public function getGoodsId()
    {
        return $this->goodsId;
    }

    /**
     * @param string $goodsId
     */
    public function setGoodsId($goodsId)
    {
        $this->goodsId = explode(',', trim((string)$goodsId, " ,"));
    }

    /**
     * @return array
     */
    public function getSpecId()
    {
        return $this->specId;
    }

    /**
     * @param string $specId
     */
    public function setSpecId($specId)
    {
        $this->specId = explode(',', trim((string)$specId, " ,"));
    }

    /**
     * @return array
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * @param string $num
     */
    public function setNum($num)
    {
        $this->num = explode(',', trim((string)$num, " ,"));
    }

    /**
     * @return array
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @param string $activityId
     */
    public function setActivityId($activityId)
    {
        $this->activityId = explode(',', trim((string)$activityId, " ,"));
    }

    /**
     * @return array
     */
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * @param string $check
     */
    public function setCheck($check)
    {
        $this->check = explode(',', trim((string)$check, " ,"));
    }

    /**
     * @return int
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @param int $addressId
     */
    public function setAddressId($addressId)
    {
        $this->addressId = (int)$addressId;
    }

    /**
     * @return int
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param int $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = (int)$paymentId;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return (string)$this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = (string)$remark;
    }

    /**
     * @return int
     */
    public function getIsInvoice()
    {
        return $this->isInvoice;
    }

    /**
     * @param int $isInvoice
     */
    public function setIsInvoice($isInvoice)
    {
        $this->isInvoice = (int)$isInvoice;
    }

    /**
     * @return string
     */
    public function getInvoiceTitle()
    {
        return (string)$this->invoiceTitle;
    }

    /**
     * @param string $invoiceTitle
     */
    public function setInvoiceTitle($invoiceTitle)
    {
        if ($this->getIsInvoice()) {
            if (!$invoiceTitle) {
                $invoiceTitle = '个人';
            }
        }
        $this->invoiceTitle = $invoiceTitle;
    }

    /**
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param string $os
     */
    public function setOs($os)
    {
        $this->os = (string)$os;
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
        $this->version = (string)$version;
    }

    /**
     * @return string
     */
    public function getBankCode()
    {
        return (string)$this->bankCode;
    }

    /**
     * @param string $bankCode
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = (string)$bankCode;
    }

    /**
     * @return string
     */
    public function getProxyType()
    {
        return $this->proxyType;
    }

    /**
     * @param string $proxyType
     */
    public function setProxyType($proxyType)
    {
        $this->proxyType = (int)$proxyType;
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
        $this->couponCode = (string)$couponCode;
    }

    /**
     * 获取商品名称
     * @param $goodsId
     * @param $specId
     */
    public function getGoodsName($goodsId, $specId = null)
    {
        if (!$specId) {
            $specIds = $this->getSpecId();
            $specId = $specIds[array_search($goodsId, $this->getGoodsId())];
        }
        return $this->goodsData[$goodsId][$specId]['goods_name'];
    }

    public function getOrderAmount()
    {
        $amount = 0;
        foreach ($this->getGoodsData() as $goods) {
            foreach ($goods as $spec) {
                if (!empty($spec['is_child'])) {
                    continue;
                }
                $amount += $spec['crazy_price'] * $spec['num'];
            }
        }
        if ($this->getActivityData()) {
            $activityData = array_pop($this->getActivityData());
            foreach ($activityData['goodsData'] as $goods) {
                foreach ($goods as $spec) {
                    $amount -= $spec['discount_fee'];
                }
            }
        }
        $this->setAmount($amount);
        return $amount;
    }

    public function setGoodsSourceSpecialId($specialGoods)
    {
        foreach ($this->goodsData as $goodsId => $goods) {
            foreach ($goods as $specId => $spec) {
                $specialId = isset($specialGoods[$goodsId]) ? $specialGoods[$goodsId] : 0;
                $this->goodsData[$goodsId][$specId]['source_special_id'] = $specialId;
            }
        }
    }

    /**
     * 设置商品计算前的占比
     * @param $goodsId
     * @param $specId
     * @param $moneyRatio
     */
    public function setMoneyRatio($goodsId, $specId, $moneyRatio)
    {
        $this->goodsData[$goodsId][$specId]['money_ratio'] = $moneyRatio;
    }

    /**
     * 设置商品计算后的占比
     * @param $goodsId
     * @param $specId
     * @param $moneyRatioAfter
     */
    public function setMoneyRatioAfter($goodsId, $specId, $moneyRatioAfter)
    {
        $this->goodsData[$goodsId][$specId]['money_ratio_after'] = $moneyRatioAfter;
    }

    /**
     * 增加子商品
     * @param $goodsId
     * @param $specId
     * @param $data
     * @param string $childName
     */
    public function setChildGoods($goodsId, $specId, $data, $childName = 'child')
    {
        if (isset($this->goodsData[$goodsId][$specId])) {
            $this->goodsData[$goodsId][$specId][$childName] = $data;
        } else {
            $this->goodsData[$goodsId][$specId] = $data;
        }
    }

    public function setChildGoodsDiscountPrice($goodsId, $specId, $discountPrice, $childName = 'child')
    {
        if ($this->goodsData[$goodsId][$specId][$childName]) {
            if (isset($this->goodsData[$goodsId][$specId][$childName]['discount_fee'])) {
                $this->goodsData[$goodsId][$specId][$childName]['discount_fee'] += $discountPrice;
            } else {
                $this->goodsData[$goodsId][$specId][$childName]['discount_fee'] = $discountPrice;
            }
        } else {
            if (isset($this->goodsData[$goodsId][$specId]['discount_fee'])) {
                $this->goodsData[$goodsId][$specId]['discount_fee'] += $discountPrice;
            } else {
                $this->goodsData[$goodsId][$specId]['discount_fee'] = $discountPrice;
            }
        }
    }
}
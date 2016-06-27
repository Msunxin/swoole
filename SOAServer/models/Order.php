<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/2
 * Time: 15:02
 */

namespace SOAServer\models;

use \Mkf\Mkf;
use Swoole\Protocol\SOAServer;

class Order extends OrderParam
{
    /**
     * 创建订单
     * @param int $userId 用户id
     * @param int $addressId 收件人地址id
     * @param int $paymentId 支付方式id
     * @param string $goodsId 商品id,多个用英文逗号分隔
     * @param string $num 规格数量,多个用英文逗号分隔
     * @param string $specId 规格id,多个用英文逗号分隔
     * @param string $activityId 活动id,多个用英文逗号分隔
     * @param string $check 勾选活动,多个用英文逗号分隔
     * @param string $remark 备注
     * @param int $isInvoice 是否需要发票
     * @param string $invoiceTitle 发票抬头
     * @param string $os 平台
     * @param string $version 版本
     * @param string $bankCode 银行号
     * @param int $proxyType 是否代付
     * @param string $couponCode 优惠券
     */
    public function createOrder()
    {
        try {
            //验证订单参数
            $this->verifyCreateOrderParams();
            //验证用户是否屏蔽
            $this->verifyUserFilter();
            //恶意订单数量限制
            $this->verifyOrderCount();
            //获取订单需要的商品信息
            $goodsData = $this->getOrderGoods($this->getGoodsId(), $this->getSpecId(), $this->getNum());
            $this->setGoodsData($goodsData);
            //计算价格
            $cartModel = new Cart();
            $activityData = $cartModel->activityCalculator($this->getUserId(), $this->getGoodsId(), $this->getSpecId(),
                $this->getNum(), $this->getActivityId(), $this->getCheck(), $this->getCouponCode());
//            var_dump($activityData);
            $this->setActivityData($activityData);

            //验证商品状态
            $this->verifyGoodsData($goodsData);

            //获取其他商品
            $this->getOtherGoods();
            //获取商品来源专区
            $this->getGoodsSourceSpecialId();
            //获取订单总价
            $amount = $this->getOrderAmount();
            //计算活动后的商品占比
            $this->countGoodsRatio();

            $result['orderAmount'] = (double)$amount;
            //创建订单号
            $orderNo = $this->createNo();
            $payno = $orderNo;
            $result['orderNo'] = $orderNo;
            $result['payno'] = $payno;

            //获取地址信息
            $userAddress = new UserAddress();
            $address = $userAddress->getUserAddressDetail($this->getUserId(), $this->getAddressId());
            if (!$address) {
                throw new \Exception('商品信息异常，请重新选购。', -406);
            }
            list($country, $province, $city, $district) = $userAddress->getAddress($address['country'], $address['province'], $address['city'], $address['district']);

            //开启事物
            Mkf::$app->connection->beginTransaction();

            //新增订单
            $orderId = $this->saveOrder($address, $country, $province, $city, $district, $orderNo, $payno, $amount);

            //新增支付记录
            $paynoLogModel = new PaynoLog();
            $paynoLogModel->saveOrderPaynoLog($orderId, $orderNo, $payno, $this->getPaymentId());

            //新增活动信息
            $orderActivityModel = new OrderActivity();
            $orderActivityModel->saveActivity($orderId, $activityData);

            //群批活动更新
            if (isset($activityData['coupon']['togetherId'])) {
                $togetherModel = new Together();
                $togetherModel->saveOrderTogether($activityData['coupon']['togetherId'], $orderId);
            }

            //添加商品信息
            $orderData = $this->buildOrderData();
//            var_dump($orderData);
            $this->saveOrderInfo($orderId, $orderData);

            //提交
            Mkf::$app->connection->commit();
            $result['orderName'] = $this->getOrderName();
            $result['payTime'] = '1800';
            $result['code'] = 1;
            $result['message'] = '';
            return $result;
        } catch (\Exception $e) {
            if (Mkf::$app->connection->isTransactionActive()) {
                Mkf::$app->connection->rollBack();
            }
            return array(
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => array(),
            );
        }
    }

    /**
     * 获取订单名称
     * @return string
     */
    public function getOrderName()
    {
        $goodsData = $this->getGoodsData();
        $orderName = '';
        foreach ($goodsData as $goods) {
            foreach ($goods as $spec) {
                $orderName = $spec['goods_name'];
                break 2;
            }
        }
        return $orderName;
    }

    /**
     * 保存order表
     * @param array $address
     * @param array $country
     * @param array $province
     * @param array $city
     * @param array $district
     * @param string $orderNo
     * @param string $payno
     * @param float $amount
     * @return int
     * @throws \Exception
     */
    public function saveOrder($address, $country, $province, $city, $district, $orderNo, $payno, $amount)
    {
        $time = time();
        $orderId = $this->add(
            array(
                'address' => $address['address'],
                'zipcode' => $address['zipcode'],
                'receiver' => $address['receiver'],
                'contactperson' => $address['contactperson'],
                'receiverphone' => $address['receiverphone'],
                'receivermobile' => $address['receivermobile'],
                'best_time' => $address['best_time'],
                'country' => $country['another_name'],
                'province' => $province['another_name'],
                'city' => $city['another_name'],
                'district' => $district['another_name'],
                'order_no' => $orderNo,
                'payno' => $payno,
                'order_time' => $time,
                'amount' => $amount,
                'user_id' => $this->getUserId(),
                'address_id' => $this->getAddressId(),
                'payment_id' => $this->getPaymentId(),
                'remark' => $this->getRemark(),
                'order_state' => 0,
                'isProxyPay' => $this->getProxyType() != null ? 1 : 0,
                'is_invoice' => $this->getIsInvoice(),
                'invoice_title' => $this->getInvoiceTitle(),
                'updatetime' => $time,
                'os' => $this->getOs(),
                'close_time' => $time + 1800,
                'bank_code' => $this->getBankCode(),
                'coin' => $this->getCoin(),
                'discount_amount' => $this->getDiscountAmount(),
                'coupon_amount' => $this->getCouponAmount(),
                'version' => $this->getVersion(),
            )
        );

        if (!$orderId) {
            throw new \Exception("订单提交失败，请稍后再试。", -410);
        }

        return $orderId;
    }

    /**
     * 验证创建订单的参数
     * @param int $userId
     * @param int $addressId
     * @param int $paymentId
     * @param string $goodsId
     * @param string $num
     * @param string $specId
     * @param string $activityId
     * @param string $check
     * @param string $os
     * @param string $version
     * @throws \Exception
     */
    private function verifyCreateOrderParams()
    {
        if (!$this->getUserId()) {
            throw new \Exception('用户编号错误', -400);
        }
        if (!$this->getAddressId()) {
            throw new \Exception('收件人地址编号错误', -401);
        }
        if (!$this->getPaymentId()) {
            throw new \Exception('支付方式错误', -402);
        }
        if (!$this->getGoodsId()) {
            throw new \Exception('商品编号不能为空', -403);
        }
        if (!$this->getNum()) {
            throw new \Exception('商品数量不能为空', -404);
        }
        if (!$this->getSpecId()) {
            throw new \Exception('商品规格不能为空', -405);
        }

        if (count($this->getGoodsId()) != count($this->getNum()) || count($this->getGoodsId()) != count($this->getNum())) {
            throw new \Exception('商品数据不完整', -406);
        }

        if (count($this->getActivityId()) != count($this->getCheck())) {
            throw new \Exception('活动数据不完整', -407);
        }
        if (!$this->getOs()) throw new \Exception('平台号不能为空');
        if (!$this->getVersion()) throw new \Exception('版本号不能为空', -407);
    }

    /**
     * 检测用户是否屏蔽
     * @throws \Exception
     */
    private function verifyUserFilter()
    {
        $shieldindModel = new Shielding();
        if ($shieldindModel->isFilter($this->getUserId())) {
            throw new \Exception('很报歉，您的账号已被限制在卖客疯消费，了解更多请联系客服', -408);
        }
    }

    /**
     * 验证用户是否恶意刷单
     */
    private function verifyOrderCount()
    {
        $userId = $this->getUserId();
        $mkfsetModel = new Mkfset();
        $setting = $mkfsetModel->getSetting(11, array('setkey', 'setvalue'));
        if ($setting['setkey'] == '恶意订单限制' && $setting['setvalue']) {
            $count = $this->getUserNoPayCount($userId);
            if ($count >= $setting['setvalue']) {
                throw new \Exception("您累计未支付订单已超过 {$setting['setvalue']} 张，请取消或支付完成后再提交新的订单", -452);
            }
        }
    }

    /**
     * 获取用户未支付数
     * @param int $userId
     * @return int
     */
    private function getUserNoPayCount($userId)
    {
        $whereString = 'user_id = :user_id';
        $whereString .= ' AND pay_state = 0';
        $whereString .= ' AND is_delete = 0';
        $whereString .= ' AND order_state = 0';
        $whereString .= ' AND close_time > :close_time';
        $userCount = $this->select('count(*) AS count')
            ->where($whereString)
            ->setParameter('user_id', $userId)
            ->setParameter('close_time', time())
            ->find();
        return $userCount['count'];
    }

    /**
     * 获取订单商品基础信息
     * @param $goodsIds
     * @param $specIds
     * @param array $nums
     * @return array
     */
    private function getOrderGoods($goodsIds, $specIds, $nums = array())
    {
        $specModel = new Spec();
        $specField = array('spec_id', 'goods_id', 'goods_no', 'stocknum', 'crazy_price', 'skusize', 'color',
            'random_num', 'attr', 'is_defective', 'is_putaway',);
        $spec = $specModel->getSpecBySpecId($specIds, $specField);

        if (!$goodsIds) {
            foreach ($specIds as $specId) {
                $goodsIds[] = $spec[$specId]['goods_id'];
            }
        }

        $goodsModel = new Goods();
        $goodsField = $goodsField = array('goods_id', 'goods_type', 'goods_name', 'saletype', 'buyupnum', 'buydownnum',
            'open_time', 'close_time', 'surplus', 'is_pack_goods', 'is_fbs', 'fbs_version', 'realnum', 'random_pack',
            'skuorder', 'bathch', 'supplier_id', 'cate', 'shop_id'
        );
        $goods = $goodsModel->getGoodsInfo($goodsIds, $goodsField);

        $goodscollectModel = new Goodscollect();
        $goodscollectField = array('goods_id', 'state', 'status');
        $goodsStatus = $goodscollectModel->getGoodsStatus($goodsIds, $goodscollectField);
        $result = array();
        foreach ($goodsIds as $key => $goodsId) {
            $result[$goodsId][$specIds[$key]] = array_merge(
                (array)$goods[$goodsId],
                (array)$spec[$specIds[$key]],
                (array)$goodsStatus[$goodsId]
            );
            if (isset($nums[$key])) {
                $result[$goodsId][$specIds[$key]]['num'] = $nums[$key];
                $result[$goodsId][$specIds[$key]]['goods_amount'] = $result[$goodsId][$specIds[$key]]['crazy_price'] * $nums[$key];
            }
            //初始化一些变量
//            $result[$goodsId][$specIds[$key]]['is_child'] = 0;
//            $result[$goodsId][$specIds[$key]]['money_ratio'] = 0;
//            $result[$goodsId][$specIds[$key]]['money_ratio_after'] = 0;
//            $result[$goodsId][$specIds[$key]]['crazy_coin'] = 0;
//            $result[$goodsId][$specIds[$key]]['discount_fee'] = 0;
//            $result[$goodsId][$specIds[$key]]['gift_special_id'] = 0;
//            $result[$goodsId][$specIds[$key]]['is_pack'] = 0;
        }

        return $result;
    }

    /**
     * 验证商品
     * @throws \Exception
     */
    private function verifyGoodsData()
    {
        //验证群批活动
        $this->verifyTogether();
        //验证限购次数
        $this->limitCount();
        //验证FBS商品是否可售
        $this->verifyFbsGoods();
        //验证商品状态
        $this->goodsStatus();
        //验证优惠券状态
        $this->verifyCoupon();
    }

    //验证群批活动
    private function verifyTogether()
    {
        $activityData = $this->getActivityData();
        if (isset($activityData['togetherGoodsId']) && isset($activityData['togetherSpecId'])) {
            if (array_sum($this->getNum()) > 1) {
                $goodsName = $this->getGoodsName($activityData['togetherGoodsId'], $activityData['togetherSpecId']);
                throw new \Exception("商品{$goodsName}已参加群批活动，请单独购买。", -466);
            }
        }
    }

    //验证限购次数
    private function limitCount()
    {
        if (!in_array($this->getUserId(), array(49, 4566, 276155, 421515, 484124))) {  //49:归;4566:汪燕;276155:COCO;421515:COCO;
            foreach ($this->getGoodsId() as $key => $value) {
                $whereString = 'pay_state = 1';
                $whereString .= ' AND user_id = :user_id';
                $whereString .= ' AND goods_id = :goods_id';
                $whereString .= ' AND order_time > :order_time';
                $dbTablePrefix = \Mkf\Mkf::$app->configs['dbTablePrefix'];
                $payOrderCount = $this->select('count(*) AS count')
                    ->leftJoin("{$dbTablePrefix}order", "{$dbTablePrefix}order_goods", "og", "{$dbTablePrefix}order.order_id=og.order_id")
                    ->where($whereString)
                    ->setParameter('user_id', $this->getUserId())
                    ->setParameter('goods_id', $value)
                    ->setParameter('order_time', strtotime('today'))
                    ->find();
                if ($payOrderCount['count'] >= 3) {
                    $goodsName = $this->getGoodsName($value);
                    throw new \Exception("您所购买的{$goodsName}超过限购次数", -433);
                }
            }
        }
    }

    /**
     * 验证fbs商品是否可售
     */
    private function verifyFbsGoods()
    {
        $goodsData = $this->getGoodsData();
        $fbsGoodsId = array();
        foreach ($goodsData as $goodsId => $goods) {
            foreach ($goods as $specId => $spec) {
                if ($spec['is_fbs']) {
                    $fbsGoodsId[] = $goodsId;
                }
            }
        }
        if ($fbsGoodsId) {
            $specialGoodsModel = new SpecialGoods();
            $arrayFbsSpecial = $specialGoodsModel->getVerifyFbsSpecial($fbsGoodsId);
            foreach ($arrayFbsSpecial as $value) {
                $fbsSpecial[$value['goods_id']] = $value['special_id'];
            }
            foreach ($fbsGoodsId as $goodsId) {
                if (!isset($fbsSpecial[$goodsId])) {
                    $goodsName = $this->getGoodsName($goodsId);
                    throw new \Exception("商品{$goodsName}已下架，请删除该商品后重试。", -465);
                }
            }
        }
    }

    /**
     * 验证商品状态
     * @throws \Exception
     */
    private function goodsStatus()
    {
        //验证其他商品状态
        foreach ($this->getGoodsData() as $goodsId => $goods) {
            foreach ($goods as $specId => $value) {
                if (!empty($value['is_gift'])) {
                    continue;
                }
                if (!empty($value['no_reduce_stock'])) {
                    continue;
                }
                $goodsName = $this->getGoodsName($goodsId);
                if (empty($value['is_child'])) {
                    //验证商品状态
                    if ($value['state'] == 4) {
                        throw new \Exception("商品{$goodsName}已下架，请删除该商品后重试。", -402);
                    }
                    if ($value['status'] != 3) {
                        throw new \Exception("商品{$goodsName}已下架，请删除该商品后重试。", -403);
                    }
                    //验证限量抢购商品数量
                    if ($value['saletype'] == 3) {
                        if ($value['surplus'] <= 0 || $value['realnum'] <= 0) {
                            throw new \Exception("商品{$goodsName}已下架，请删除该商品后重试。", -404);
                        }
                        if ($value['surplus'] < $value['num'] || $value['realnum'] < $value['num']) {
                            throw new \Exception("商品{$goodsName}仅余{$value['stocknum']}件，请调整所购商品数量后重试。", -424);
                        }
                    } elseif ($value['saletype'] == 4) {
                        //验证内卖商品购买时间
                        if ($value['open_time'] > time() || $value['close_time'] < time()) {
                            throw new \Exception("商品{$goodsName}内卖活动未开始或已结束", -430);
                        }
                    }
                    //验证单笔购买数上限
                    if ($value['buyupnum'] < $value['num'] && $value['buyupnum'] > 0) {
                        throw new \Exception("商品{$goodsName}每单限购{$value['buyupnum']}件，请调整商品数量后重试。", -405);
                    }
                    //验证单笔购买数下限
                    if ($value['buydownnum'] > $value['num'] && $value['buydownnum'] > 0) {
                        throw new \Exception("商品{$goodsName}每单最低需购{$value['buydownnum']}件，请调整商品数量后重试。", -429);
                    }
                    //验证规格是否上架
                    if (!$value['is_putaway']) {
                        throw new \Exception("商品{$goodsName}已下架，请删除该商品后重试。", -422);
                    }
                }
                if ($value['stocknum'] <= 0) {
                    if ($value['is_child']) {
                        $goodsNameTmp = $value['parent_goods_name'];
                    } else {
                        $goodsNameTmp = $value['goods_name'];
                    }
                    throw new \Exception("商品{$goodsNameTmp}已售磬，请删除该商品后重试。", -408);
                }
                if ($value['stocknum'] < $value['num']) {
                    if ($value['is_child']) {
                        $goodsNameTmp = $value['parent_goods_name'];
                    } else {
                        $goodsNameTmp = $value['goods_name'];
                    }
                    throw new \Exception("商品{$goodsNameTmp}仅余{$value['stocknum']}件，请调整所购商品数量后重试。", -423);
                }
            }
        }
    }

    /**
     * 验证优惠券
     * @return bool
     * @throws \Exception
     */
    private function verifyCoupon()
    {
        $couponCode = $this->getCouponCode();
        if ($couponCode) {
            $couponCodeModel = new CouponCode();
            $couponDetail = $couponCodeModel->getCouponCode($couponCode);

            $couponModel = new Coupon();
            $coupon = $couponModel->getCoupon($couponDetail['coupon_id']);

            if (!$couponDetail || !$coupon) {
                return true;
            }

            if ($couponDetail['order_id']) {
                throw new \Exception("该优惠券不能使用", -461);
            }
            $time = time();
            if ($time < $coupon['start_time'] || $time > $coupon['end_time']) {
                throw new \Exception("优惠券已过期或未开始", -462);
            }
            $categoryIds = explode(',', $coupon['category_ids']);
            //获取计算优惠券的商品
            $isUser = false;
            foreach ($this->getGoodsData() as $goods) {
                foreach ($goods as $spec) {
                    if (in_array($spec['cate'], $categoryIds)) {
                        $isUser = true;
                        break;
                    }
                }
            }
            if ($isUser == false) {
                throw new \Exception("该优惠券不能使用", -463);
            }
            //TODO 获取优惠券总额
            $activityData = $this->getActivityData();
            $couponAmount = $activityData['coupon']['activityDiscountPrice']['coupon'];
            if ($coupon['coupon_type'] == 1 && $couponAmount == 0) {
                throw new \Exception("未达到优惠券额度使用要求", -464);
            }
        }
        return true;
    }

    /**
     * 创建订单号
     * @param int $rand_length
     * @return string
     */
    private function createNo($rand_length = 2)
    {
        $rand = str_repeat(9, $rand_length);
        $no = date('ymdHis') . substr(microtime(), 2, 6) . sprintf("%0{$rand_length}d", rand(0, $rand));
        return $no;
    }

    /**
     * 获取疯狂币总额
     * @return int
     */
    private function getCoin()
    {
        $coin = 0;
        $activityData = $this->getActivityData();
        if (isset($activityData['crazyCoin']['activityDiscountPrice']['crazyCoin'])) {
            foreach ($activityData['crazyCoin']['activityDiscountPrice']['crazyCoin'] as $goods) {
                foreach ($goods as $discount) {
                    $coin += $discount;
                }
            }
        }
        return $coin;
    }

    /**
     * 获取扣减总金额
     * @return int
     */
    private function getDiscountAmount()
    {
        $discountAmount = 0;
        if ($this->getActivityData()) {
            $activityData = array_pop($this->getActivityData());
            foreach ($activityData['goodsData'] as $goods) {
                foreach ($goods as $spec) {
                    $discountAmount -= $spec['discount_fee'];
                }
            }
        }
        return $discountAmount;
    }

    /**
     * 获取优惠券总金额
     * @return int
     */
    private function getCouponAmount()
    {
        $couponAmount = 0;
        $activityData = $this->getActivityData();
        if (isset($activityData['coupon']['activityDiscountPrice']['coupon'])) {
            foreach ($activityData['coupon']['activityDiscountPrice']['coupon'] as $discount) {
                $couponAmount += $discount;
            }
        }
        return $couponAmount;
    }

    /**
     * 生成创建订单需要的数据
     * @return array
     */
    private function buildOrderData()
    {
        $goodsData = $this->getGoodsData();
        $orderData = array();
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                $isset = false;
                if (isset($orderData[$goodsId])) {
                    $isset = true;
                }
                //goods
                $orderData[$goodsId]['goods_id'] = $vo['goods_id'];
                $orderData[$goodsId]['skuorder'] = $vo['skuorder'];
                $orderData[$goodsId]['goods_name'] = $vo['goods_name'];
                $orderData[$goodsId]['supplier_id'] = $vo['supplier_id'];
                $orderData[$goodsId]['bathch'] = $vo['bathch'];
                $orderData[$goodsId]['saletype'] = $vo['saletype'];
                $orderData[$goodsId]['realnum'] = $vo['realnum'];
                $orderData[$goodsId]['surplus'] = $vo['surplus'];
                if ($isset) {
                    $orderData[$goodsId]['num'] += $vo['num'];
                    $orderData[$goodsId]['amount'] += $vo['num'] * $vo['crazy_price'];
                } else {
                    $orderData[$goodsId]['num'] = $vo['num'];
                    $orderData[$goodsId]['amount'] = $vo['num'] * $vo['crazy_price'];
                }

                //spec
                $orderData[$goodsId]['spec'][] = $this->buildSpecArray($vo);

                if (isset($vo['random'])) {
                    $orderData[$goodsId]['spec'] = $this->buildSpecArray($vo['random']);
                }

                if (isset($vo['pack'])) {
                    $orderData[$goodsId]['spec'] = $this->buildSpecArray($vo['pack']);
                }

                if (isset($vo['child'])) {
                    $orderData[$goodsId]['spec'] = $this->buildSpecArray($vo['child']);
                }
            }
        }
        return $orderData;
    }

    /**
     * 保存订单商品信息
     * @param $orderId
     * @param $orderData
     * @throws \Exception
     */
    private function saveOrderInfo($orderId, $orderData)
    {
        $orderGoodsModel = new OrderGoods();
        $orderSpecModel = new OrderSpec();
        $specialModel = new Special();
        $specModel = new Spec();
//        $ordernumLog = new OrdernumLog();
        foreach ($orderData as $goods) {
            $ogId = $orderGoodsModel->saveOrderGoods($orderId, $goods);
            foreach ($goods['spec'] as $spec) {
                $orderSpecModel->saveOrderSpec($orderId, $ogId, $spec);

                if ($spec['is_gift']) { //扣除满赠活动赠品总数
                    $specialModel->decreaseGiftNum($spec['gift_special_id'], $spec['num']);
                }
                if (!$spec['no_reduce_stock']) {   //判断是否需要扣减库存
                    //扣除规格表商品数量
                    $specModel->decreaseStockNum($spec['spec_id'], $spec['num']);

                    //TODO: 记录日志
//                    E('Order')->saveOrdernum($order_id, $spec['specId'], $spec['goodsNum'], $spec['stocknum'], $spec['ordernum'], M()->_sql(), '商品下单' . VERSION);
                }
            }
        }

    }

    /**
     * 获取商品来源专区
     */
    private function getGoodsSourceSpecialId()
    {
        $goodsIds = $this->getGoodsId();
        $specialGoodsModel = new SpecialGoods();
        $specialGoods = $specialGoodsModel->getGoodsSpecialId($goodsIds);
        $this->setGoodsSourceSpecialId($specialGoods);
    }

    /**
     * 计算活动后的商品占比
     */
    private function countGoodsRatio()
    {
        $goodsData = $this->getGoodsData();
        $orgiAmount = 0;
        foreach ($goodsData as $goodsId => $spec) {
            foreach ($spec as $specId => $value) {
                if (!empty($value['is_pack_goods'])) {
                    continue;
                }
                if (!empty($value['random_pack'])) {
                    continue;
                }
                if (!empty($spec['is_gift'])) {
                    continue;
                }
                $orgiAmount += $value['goods_amount'];
                if (!empty($value['random'])) {
                    $orgiAmount += $value['goods_amount'];
                }
            }
        }
        foreach ($goodsData as $goodsId => $spec) {
            foreach ($spec as $specId => $value) {
                if (!empty($value['is_pack_goods'])) {
                    continue;
                }
                if (!empty($value['random_pack'])) {
                    continue;
                }
                if (!empty($spec['is_gift'])) {
                    continue;
                }
                $moneyRatio = bcdiv($value['goods_amount'], $orgiAmount, 4) * 100;
                $moneyRatioAfter = bcdiv(($value['goods_amount'] - $value['discount_fee']), $this->getAmount(), 4) * 100;
                $this->setMoneyRatio($goodsId, $specId, $moneyRatio);
                $this->setMoneyRatioAfter($goodsId, $specId, $moneyRatioAfter);
                if (!empty($value['random'])) {
                    $moneyRatio = bcdiv($value['random']['goods_amount'], $orgiAmount, 4) * 100;
                    $moneyRatioAfter = bcdiv(($value['random']['goods_amount'] - $value['random']['discount_fee']), $this->getAmount(), 4) * 100;
                    //TODO: 设置
                }
            }
        }
    }

    /**
     * 获取其他商品
     * @throws \Exception
     */
    private function getOtherGoods()
    {
        $goodsData = $this->getGoodsData();
        foreach ($goodsData as $goodsId => $value) {
            foreach ($value as $specId => $vo) {
                if (!empty($vo['is_pack_goods']))
                    $this->getPackGoods($vo);
                if (!empty($vo['random_pack']))
                    $this->getRandomGoods($vo);
                switch ($vo['goods_type']) {
                    case 1:     //福袋
                        $this->getLuckyBag($vo);
                        break;
                }
            }
        }
    }

    private function getPackGoods($goods)
    {
        //打包商品价格
        $pack_price = $tmp_pack_price = $goods['crazy_price'];
        //获取被打包商品
        $packGoodsModel = new PackGoods();
        $pack = $packGoodsModel->getPackGoods($goods['goods_id'], $goods['spec_id']);
        $pack_goods_ids = array();
        $pack_spec_ids = array();
        foreach ($pack as $v) {
            $pack_goods_ids[] = $v['goods_id'];
            $pack_spec_ids[] = $v['spec_id'];
        }
        $pack_goods_data = $this->getOrderGoods(array_unique($pack_goods_ids), array_unique($pack_spec_ids));
        $pack_goods = array();
        foreach ($pack_goods_data as $g) {
            foreach ($g as $s) {
                if ($goods['num'] > $s['stocknum']) {
                    //TODO: 修改打包商品库存
//                M('Spec')->where(array('spec_id' => $goods['specId']))->save(array('stocknum' => $g['stocknum']));
//                M('SpecStock')->where(array('spec_id' => $goods['specId']))->save(array('stocknum' => $g['stocknum']));
                    throw new \Exception("商品{$goods['goods_name']}仅余{$s['stocknum']}件，请调整所购商品数量后重试。", -423);
                }
                $pack_goods[$s['goods_id']] = $s;
            }
        }
        if ($pack_goods) {
            //计算被打包商品总价
            $packed_price = 0;
            foreach ($pack_goods as $v) {
                $packed_price += $v['crazy_price'];
            }
            $i = 0;
            foreach ($pack_goods as $k => $v) {
                //按比例分配金额
                if ($i == (count($pack_goods) - 1)) {
                    if (($v['crazy_price'] - $tmp_pack_price) >= 0) {
                        $v['discount_fee'] = $v['crazy_price'] - $tmp_pack_price;
                    } else {
                        $v['crazy_price'] = $tmp_pack_price;
                    }
                } else {
                    $single_price = bcmul(bcdiv($v['crazy_price'], $packed_price, 2), $pack_price, 2);
                    if (($v['crazy_price'] - $single_price) >= 0) {
                        $v['discount_fee'] = $v['crazy_price'] - $single_price;
                    } else {
                        $v['crazy_price'] = $single_price;
                    }
                    $tmp_pack_price -= $single_price;
                }
                $s['parent_goods_name'] = $goods['goods_name'];
                $v['is_pack'] = 1;
                $v['num'] = $goods['num'];
                $v['goods_amount'] = $v['crazy_price'] * $goods['num'];
                $v['is_child'] = 1;
                $this->setChildGoods($v['goods_id'], $v['spec_id'], $v, 'pack');
                $i++;
            }
            if ($goods['discount_fee']) {
                $discount_fee = $goods['discount_fee'];
                $i = 0;
                foreach ($pack_goods as $k => $v) {
                    if ($i == (count($pack_goods) - 1)) {
                        $this->setChildGoodsDiscountPrice($v['goods_id'], $v['spec_id'], $discount_fee, 'pack');
                    } else {
                        $single_discount = bcmul(bcdiv($v['crazyPrice'], $packed_price, 2), $goods['discount_fee'], 2);
                        $this->setChildGoodsDiscountPrice($v['goods_id'], $v['spec_id'], $single_discount, 'pack');
                        $discount_fee -= $single_discount;
                    }
                    $i++;
                }
            }
        } else {
            throw new \Exception("打包商品不存在，请调整所购商品数量后重试。", -450);
        }
    }

    private function getRandomGoods($goods)
    {
        //闷包商品价格
        $random_num = $goods['random_num'] * $goods['num'];
        $packOms = new PackOms();
        $random_spec = $packOms->getRandomSpec($goods['spec_id'], $goods['shop_id']);
        if (!$random_spec) {
            //TODO: 修改库存为0
//            M('Spec')->where(array('spec_id' => $goods['specId']))->save(array('stocknum' => 0));
//            M('SpecStock')->where(array('spec_id' => $goods['specId']))->save(array('stocknum' => 0));
            throw new \Exception("商品{$goods['goods_name']}仅余0件，请调整所购商品数量后重试。", -423);
        }
        if (array_sum($random_spec) < $goods['random_num']) {
            //TODO: 修改库存为0
//            M('Spec')->where(array('spec_id' => $goods['specId']))->save(array('stocknum' => 0));
//            M('SpecStock')->where(array('spec_id' => $goods['specId']))->save(array('stocknum' => 0));
            $shortNum = floor(array_sum($random_spec) / $goods['random_num']);
            throw new \Exception("商品{$goods['goods_name']}仅余{$shortNum}件，请调整所购商品数量后重试。", -423);
        }
        if (array_sum($random_spec) < $random_num) {
            $shortNum = floor(array_sum($random_spec) / $goods['random_num']);
            throw new \Exception("商品{$goods['goods_name']}仅余{$shortNum}件，请调整所购商品数量后重试。", -423);
        }
        //随机sku
        $spec_count = count($random_spec) - 1;
        $random_specs = array_keys($random_spec);
        //随机选择会不会重复
        $repeat = 1;
        if (($spec_count + 1) >= $random_num) {
            $repeat = 0;
        }
        $select_spec = array();
        for ($i = 0; $i < $random_num; $i++) {
            $j = rand(0, $spec_count);
            if (empty($select_spec[$random_specs[$j]])) {
                $select_spec[$random_specs[$j]] = 0;
            }
            $select_spec[$random_specs[$j]]++;
            if ($repeat == 0) { //不会重复
                $spec_count--;
                unset($random_specs[$j]);
                shuffle($random_specs);
            } else {
                $random_spec[$random_specs[$j]]--;
                if ($random_spec[$random_specs[$j]] <= 0) {
                    $spec_count--;
                    unset($random_specs[$j]);
                    shuffle($random_specs);
                }
            }
        }
        //获取随机sku的商品信息
        $select_data = $this->getOrderGoods(array(), array_keys($select_spec));
        $packed_price = 0;
        $random_goods = array();
        $last_goods_id = 0;
        $last_spec_id = 0;
        //整理闷包商品
        foreach ($select_data as $g) {
            foreach ($g as $v) {
                $v['stock_shop_id'] = $goods['shop_id'];
                $v['parent_goods_name'] = $goods['goods_name'];
                $v['is_random'] = 1;
                $v['is_child'] = 1;
                $v['num'] = $select_spec[$v['spec_id']];
                $v['goods_amount'] = $v['crazy_price'] * $v['num'];
                $random_goods[$v['goods_id']][$v['spec_id']] = $v;
                $last_goods_id = $v['goods_id'];
                $last_spec_id = $v['spec_id'];
                $packed_price += $v['goods_amount'];
            }
        }
        //扣减折扣
        $discount_amount = $tmp_discount = $goods['discount_fee'] + $packed_price - $goods['goods_amount'];
        foreach ($random_goods as $goods_id => $vo) {
            foreach ($vo as $spec_id => $v) {
                if ($v['goodsId'] == $last_goods_id && $v['specId'] == $last_spec_id) {
                    $v['discount_fee'] = $tmp_discount;
                } else {
                    $v['discount_fee'] = bcmul(bcdiv($v['goods_amount'], $packed_price, 2), $discount_amount, 2);
                }
                $tmp_discount -= $v['discount_fee'];
                $this->setChildGoods($v['goods_id'], $v['spec_id'], $v, 'random');
            }
        }
    }

    /**
     * 福袋
     * @param $goods
     * @throws \Exception
     */
    private function getLuckyBag($goods)
    {
        $luckyBagModel = new LuckyBag();
        $bag = $luckyBagModel->getLuckBag($goods['spec_id']);
        if (!$bag) {
            return;
        }
        $bag_info = $bag_ids = [];
        foreach ($bag as $value) {
            $bag_info[$value['bag_id']] = $value['random_num'] * $goods['num'];
            $bag_ids[] = $value['bag_id'];
        }
        $luckyBagDetailModel = new LuckyBagDetail();
        $bag_detail_tmp = $luckyBagDetailModel->getLuckyBagDetail($bag_ids);
        $bag_detail = $bag_detail_spec_ids = [];
        foreach ($bag_detail_tmp as $value) {
            $bag_detail_spec_ids[] = $value['spec_id'];
        }
        //验证库存
        $specModel = new Spec();
        $spec = $specModel->getStockBySpecId($bag_detail_spec_ids, false);
        foreach ($bag_detail_tmp as $value) {
            if ($spec[$value['spec_id']] > 0) {
                $bag_detail[$value['bag_id']][$value['spec_id']] = $spec[$value['spec_id']];
                $bag_detail_spec_ids[] = $value['spec_id'];
            }
        }
        foreach ($bag_info as $bag_id => $value) {
            if ($value > array_sum($bag_detail[$bag_id])) {
                $shortNum = floor(array_sum($bag_detail[$bag_id]) / ($value / $goods['num']));
                throw new \Exception("商品{$goods['goods_name']}仅余{$shortNum}件，请调整所购商品数量后重试。", -423);
            }
        }
        //随机sku
        $select_spec = [];
        foreach ($bag_info as $bag_id => $random_num) {
            $spec_count = count($bag_detail[$bag_id]) - 1;
            $random_specs = array_keys($bag_detail[$bag_id]);
            //随机选择会不会重复
            $repeat = 1;
            if (($spec_count + 1) >= $random_num) {
                $repeat = 0;
            }
            for ($i = 0; $i < $random_num; $i++) {
                $j = rand(0, $spec_count);
                if (empty($select_spec[$random_specs[$j]])) {
                    $select_spec[$random_specs[$j]] = 0;
                }
                $select_spec[$random_specs[$j]]++;
                if ($repeat == 0) { //不会重复
                    $spec_count--;
                    unset($random_specs[$j]);
                    shuffle($random_specs);
                } else {
                    $bag_detail[$bag_id][$random_specs[$j]]--;
                    if ($bag_detail[$bag_id][$random_specs[$j]] <= 0) {
                        $spec_count--;
                        unset($random_specs[$j]);
                        shuffle($random_specs);
                    }
                }
            }
        }
        //获取随机sku的商品信息
        $select_data = $this->getOrderGoods(array(), array_keys($select_spec));
        $packed_price = 0;
        $random_goods = [];
        $last_goods_id = $last_spec_id = 0;
        //整理闷包商品
        foreach ($select_data as $value) {
            foreach ($value as $v) {
                $v['parent_goods_name'] = $goods['goods_name'];
                $v['is_child'] = 1;
                $v['num'] = $select_spec[$v['spec_id']];
                $v['goods_amount'] = $v['crazy_price'] * $v['num'];
                $random_goods[$v['goods_id']][$v['spec_id']] = $v;
                $last_goods_id = $v['goods_id'];
                $last_spec_id = $v['spec_id'];
                $packed_price += $v['goods_amount'];
            }
        }
        //扣减折扣
        $discount_amount = $tmp_discount = $goods['discount_fee'] + $packed_price - $goods['goods_amount'];
        foreach ($random_goods as $goods_id => $vo) {
            foreach ($vo as $spec_id => $v) {
                if ($v['goods_id'] == $last_goods_id && $v['spec_id'] == $last_spec_id) {
                    $v['discount_fee'] = $tmp_discount;
                } else {
                    $v['discount_fee'] = bcmul(bcdiv($v['goods_amount'], $packed_price, 2), $discount_amount, 2);
                }
                $tmp_discount -= $v['discount_fee'];
                $this->setChildGoods($v['goods_id'], $v['spec_id'], $v);
            }
        }
    }

    /**
     * 创建规格数据
     * @param $data
     * @return array
     */
    public function buildSpecArray($data)
    {
        $array = array('goods_id', 'goods_no', 'num', 'stocknum', 'goods_name', 'crazy_price', 'spec_id', 'attr',
            'is_defective', 'supplier_id', 'bathch', 'shop_id', 'is_gift', 'gift_special_id', 'is_pack', 'is_pack_goods',
            'discount_fee', 'crazy_coin', 'money_ratio', 'money_ratio_after', 'random_pack', 'is_random',
            'stock_shop_id', 'no_reduce_stock', 'is_fbs', 'fbs_version', 'coupon_price', 'source_special_id',
            'is_child', 'goods_type');
        $specArray = array();
        foreach ($array as $key) {
            if (isset($data[$key])) {
                $specArray[$key] = $data[$key];
            } else {
                $specArray[$key] = 0;
            }
        }
        return $specArray;
    }
}
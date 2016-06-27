<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/2
 * Time: 11:32
 */

namespace SOAServer\controllers;

use mkf\Controller;

class OrderController extends Controller
{
    /**
     * 创建订单
     * @return array
     */
    public function add($request)
    {
        $orderModel = new \SOAServer\models\Order();
        $orderModel->setUserId($request['userId']);
        $orderModel->setAddressId($request['addressId']);
        $orderModel->setPaymentId($request['paymentId']);
        $orderModel->setGoodsId($request['goodsId']);
        $orderModel->setSpecId($request['specId']);
        $orderModel->setNum($request['num']);
        $orderModel->setActivityId($request['activityId']);
        $orderModel->setCheck($request['check']);
        $orderModel->setRemark($request['remark']);
        $orderModel->setIsInvoice($request['isInvoice']);
        $orderModel->setInvoiceTitle($request['invoiceTitle']);
        $orderModel->setOs($request['os']);
        $orderModel->setVersion($request['version']);
        $orderModel->setBankCode($request['bankCode']);
        $orderModel->setProxyType($request['proxyType']);
        $orderModel->setCouponCode($request['couponCode']);
        return $orderModel->createOrder();
    }

}
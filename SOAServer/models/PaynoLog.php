<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/9
 * Time: 15:31
 */

namespace SOAServer\models;


use mkf\Model;

class PaynoLog extends Model
{

    /**
     * 记录支付流水号
     * @param $orderId
     * @param $orderNo
     * @param $payno
     * @param $paymentId
     * @return mixed
     * @throws \Exception
     */
    public function saveOrderPaynoLog($orderId, $orderNo, $payno, $paymentId)
    {
        $paynoLogId = $this->add(array(
            'order_id' => $orderId,
            'order_no' => $orderNo,
            'payno' => $payno,
            'payment_id' => $paymentId,
            'create_time' => time(),
        ));

        if (!$paynoLogId) {
            throw new \Exception("订单提交失败，请稍后再试。", -410);
        }

        return $paymentId;
    }
}
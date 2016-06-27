<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/9
 * Time: 21:25
 */

namespace SOAServer\models;

use mkf\Model;

class OrdernumLog extends Model
{
    public function saveOrdernum($order_id, $spec_id, $num, $stocknum, $ordernum, $sql, $remark)
    {
        $data = array(
            'order_id' => $order_id,
            'spec_id' => $spec_id,
            'num' => $num,
            'stocknum' => $stocknum,
            'ordernum' => $ordernum,
            'sql' => $sql,
            'create_time' => time(),
            'remark' => $remark,
        );
        if (C("MKF_LOG_UPDATE")) {
            $url = 'Ordernum/addordernum';
            getCurl($url, $data);
        } else {
            M('OrdernumLog')->add($data);
        }
    }
}
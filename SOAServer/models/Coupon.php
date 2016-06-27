<?php

namespace SOAServer\models;


use mkf\Model;

class Coupon extends Model
{
    public function getCoupon($couponId){
        return $this->select('*')
            ->where('coupon_id = :coupon_id')
            ->setParameter('coupon_id', $couponId)
            ->find();
    }
}
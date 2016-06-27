<?php

namespace SOAServer\models;

use mkf\Model;

class CouponCode extends Model
{
    public function getCouponCode($code)
    {
        return $this->select('*')
            ->where('code = :code')
            ->setParameter('code', $code)
            ->find();
    }
}
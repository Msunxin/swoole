<?php

namespace SOAServer\models;

use mkf\Model;

class UserAccount extends Model
{

    /**
     * 获取用户疯狂币数量
     * @param $userId
     * @return mixed
     */
    public function getUserCoin($userId)
    {
        $whereString = 'user_id = :user_id';
        $whereString .= ' AND is_out = 0';
        $whereString .= ' AND amount > 0';
        $coinData = $this->select('SUM(amount) AS coin')
            ->where($whereString)
            ->setParameter('user_id', $userId)
            ->find();
        return $coinData['coin'];
    }
}
<?php

namespace SOAServer\models;

use mkf\Model;

/**
 * Class Together
 * @package SOAServer\models
 */
class Together extends Model
{
    /**
     * 获取用户群批活动
     * @param int $userId
     * @param array $specId
     * @return array
     */
    public function getUserTogether($userId, $specId)
    {
        $whereString = $this->queryBuilder->expr()->in('spec_id', $specId);
        $whereString .= ' AND user_id = :user_id';
        $whereString .= ' AND order_id = 0';
        $whereString .= ' AND progress > 4';
        $together = $this->select('*')
            ->where($whereString)
            ->setParameter('user_id', $userId)
            ->orderBy('create_time', 'DESC')
            ->findAll();

        return $together;
    }

    /**
     * 保存群批活动订单号
     * @param $togetherId
     * @param $orderId
     * @throws \Exception
     */
    public function saveOrderTogether($togetherId, $orderId)
    {
        $this->where('id = :id')->setParameter('id', $togetherId);
        $togetherResponse = $this->save(array(
            'order_id' => $orderId,
        ));
        if ($togetherResponse === false) {
            throw new \Exception("订单提交失败，请稍后再试。", -467);
        }
    }
}
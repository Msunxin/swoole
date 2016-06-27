<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/8
 * Time: 21:18
 */

namespace SOAServer\models;


use mkf\Model;

class UserAddress extends Model
{
    /**
     * 获取用户地址详情
     * @param $userId
     * @param $addressId
     * @return array
     */
    public function getUserAddressDetail($userId, $addressId)
    {
        $field = array('country', 'province', 'city', 'district', 'address', 'zipcode', 'consignee AS receiver',
            'consignee AS contactperson', 'tel AS receiverphone', 'mobile AS receivermobile', 'best_time');
        return $this->select($field)
            ->where('user_id = :user_id AND address_id = :address_id')
            ->setParameter('user_id', $userId)
            ->setParameter('address_id', $addressId)
            ->find();
    }

    /**
     * getAddress 获取地址
     * @example
     * list($country, $province, $city, $district) = $userAddress->getAddress($order['country'], $order['province'], $order['city'], $order['district']);
     * @return array
     */
    public function getAddress()
    {
        $args_arr = func_get_args(); //使用func_get_args函数将当前传入的参数保存进$args_arr数组中
        $this->queryBuilder->resetQueryPart('from');
        $dbTablePrefix = \Mkf\Mkf::$app->configs['dbTablePrefix'];
        $whereString = $this->queryBuilder->expr()->in('region_id', $args_arr);
        $region = $this->select('*')
            ->from("{$dbTablePrefix}china_region")
            ->where($whereString)
            ->findAll();
        $tmp = array();
        foreach ($region as $value) {
            $tmp[$value['region_id']] = $value;
        }
        $data = array();
        foreach ($args_arr as $value) {
            $data[] = $tmp[$value];
        }
        return $data;
    }
}
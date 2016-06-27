<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/11
 * Time: 23:29
 */

namespace SOAServer\models;

use mkf\Model;

class PackOms extends Model
{
    /**
     * 获取闷包的商品
     * @param $specId
     * @param $shopId
     * @return array
     */
    public function getRandomSpec($specId, $shopId)
    {
        $dbTablePrefix = \Mkf\Mkf::$app->configs['dbTablePrefix'];
        $po =  "{$dbTablePrefix}pack_oms";
        $whereString = 'pack_spec_id = :pack_spec_id';
        $whereString .= ' AND stocknum > 0';
        $whereString .= ' AND shop_id = :shop_id';
        $result = $this->select(array('ss.spec_id', 'ss.stocknum'))
            ->leftJoin($po, "{$dbTablePrefix}spec_stock", "ss", "{$po}.spec_id=ss.spec_id")
            ->where($whereString)
            ->setParameter('pack_spec_id', $specId)
            ->setParameter('shop_id', $shopId)
            ->findAll();
        $randomSpec = array();
        foreach ($result as $value) {
            $randomSpec[$value['spec_id']] = $value['stocknum'];
        }
        return $randomSpec;
    }
}
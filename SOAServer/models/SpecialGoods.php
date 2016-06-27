<?php

namespace SOAServer\models;

/**
 * 专题商品model
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class SpecialGoods extends \mkf\Model
{

    public function getSpecialIdArrByGoodsIdArr($goodsIdArr)
    {
        $dbTablePrefix = \Mkf\Mkf::$app->configs['dbTablePrefix'];
        $this->queryBuilder->resetQueryPart('from');
        $this->where('gc.type <> 0 AND gc.state IN (1,2,3) AND gc.status = 3');
        $this->addIn('sg.goods_id', $goodsIdArr);
        $this->join('sg', "{$dbTablePrefix}goodscollect", 'gc', 'sg.special_id = gc.goods_id');
        $this->from("{$dbTablePrefix}special_goods", 'sg')->select('sg.special_id');
        $rows = $this->findAll();
        if (empty($rows)) {
            return array();
        }

        $specialIdArr = array();
        foreach ($rows as $row) {
            $specialIdArr[] = $row['special_id'];
        }
        return $specialIdArr;
    }

    public function getSpecialGoods($specialId)
    {
        $fields = array('goods_id');
        $specialGoods = $this->select($fields)->where('special_id = :special_id')->setParameter('special_id', $specialId)
            ->findAll();
        $result = array();
        foreach ($specialGoods as $value) {
            $result[] = $value['goods_id'];
        }
        return $result;
    }

    /**
     * 获取fbs商品的专题信息
     * @param $fbsGoodsId
     * @return mixed
     */
    public function getVerifyFbsSpecial($fbsGoodsId)
    {
        $time = time();
        $whereString = $this->queryBuilder->expr()->in('sg.goods_id', $fbsGoodsId);
        $whereString .= " AND s.start_time >= {$time}";
        $whereString .= " AND s.end_time <= {$time}";
        $whereString .= " AND gc.type IN (1, 3)";
        $whereString .= " AND gc.state <> 4";
        $whereString .= " AND gc.status = 3";
        $field = array('sg.goods_id', 'sg.special_id');
        $dbTablePrefix = \Mkf\Mkf::$app->configs['dbTablePrefix'];
        $this->queryBuilder->resetQueryPart('from');
        $fbsSpecial = $this->select($field)
            ->from("{$dbTablePrefix}special_goods", 'sg')
            ->leftJoin('sg', "{$dbTablePrefix}special", "s", "sg.special_id = s.special_id")
            ->leftJoin('sg', "{$dbTablePrefix}goodscollect", "gc", "gc.goods_id=s.special_id")
            ->where($whereString)
            ->findAll();
        return $fbsSpecial;
    }

    /**
     * 获取商品来源专区
     * @param $goodsIds
     */
    public function getGoodsSpecialId($goodsIds)
    {
        $dbTablePrefix = \Mkf\Mkf::$app->configs['dbTablePrefix'];
        $sg =  "{$dbTablePrefix}special_goods";
        $field = array("{$sg}.special_id", "{$sg}.goods_id");
        $whereString = $this->queryBuilder->expr()->in("{$sg}.goods_id", $goodsIds);
        $whereString .= " AND s.app_resource = 0";
        $specialGoods = $this->select($field)
            ->leftJoin("{$sg}", "{$dbTablePrefix}special", "s", "{$sg}.special_id = s.special_id")
            ->where($whereString)
            ->orderBy('id', 'DESC')
            ->findAll();
        $goodsSpecial = array();
        foreach ($specialGoods as $value) {
            if (!isset($goodsSpecial[$value['goods_id']])) {
                $goodsSpecial[$value['goods_id']] = $value['special_id'];
            }
        }
        return $goodsSpecial;
    }

}

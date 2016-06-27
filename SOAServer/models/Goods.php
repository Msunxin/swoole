<?php

namespace SOAServer\models;

use mkf\Model;

/**
 * 商品model
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Goods extends Model
{

    /**
     * 获取商品信息
     * @param int|array $goodsId 商品编号
     * @param string|array $field 商品字段
     * @return array
     */
    public function getGoodsInfo($goodsIds, $field = '*')
    {
        if (!is_array($goodsIds)) {
            $goodsIds = explode(',', $goodsIds);
        }
        $goodsInfo = $this->select($field)->where(
            $this->queryBuilder->expr()->in('goods_id', $goodsIds)
        )->findAll();
        $goodsData = array();
        foreach ($goodsInfo as $value) {
            $goodsData[$value['goods_id']] = $value;
        }
        return $goodsData;
    }

    /**
     * 获取完整商品详细
     * @param int|array $goodsId 商品编号
     * @param int $getGoodsImg 是否获取商品图集
     * @param bool $getGoodsSpec 是否获取规格
     * @return array
     */
    public function getGoodsDetail($goodsId, $imgType = false, $goodsSpec = false)
    {
        $goodsField = array('goods_id', 'goods_type', 'goods_name', 'saletype', 'buyupnum', 'buydownnum', 'open_time',
            'close_time', 'surplus', 'is_pack_goods', 'is_fbs', 'fbs_version', 'random_pack'
        );
        $goodsDetail = $this->getGoodsInfo($goodsId, $goodsField);
        if ($imgType) {
            $imgModel = new Imgfavs();
            $imgField = array('goods_id', 'img120_url', 'img320_url', 'img640_url', 'img1080_url', 'is_cover', 'favs');
            $img = $imgModel->getGoodsImgs($goodsId, $imgType, $imgField);
            foreach ($goodsDetail as $key => $value) {
                $goodsDetail[$key]['image'] = $img[$value['goods_id']];
            }
        }
        if ($goodsSpec) {
            $specModel = new Spec();
            $specField = array('spec_id', 'goods_id', 'goods_no', 'stocknum', 'crazy_price', 'skusize', 'color',
                'random_num');
            $spec = $specModel->getGoodsSpec($goodsId, $specField);
            foreach ($goodsDetail as $key => $value) {
                $goodsDetail[$key]['spec'] = $spec[$value['goods_id']];
            }
        }
        return $goodsDetail;
    }

    public function getGoodsActivityData($goodsId)
    {
        if (!is_array($goodsId)) {
            $goodsId = explode(',', $goodsId);
        }
        $whereString = $this->queryBuilder->expr()->in('goods_id', $goodsId);
        $fields = array('goods_id', 'cate');
        $spec = $this->select($fields)->where($whereString)->findAll();
        $result = array();
        foreach ($spec as $value) {
            $result[$value['goods_id']] = $value;
        }
        return $result;
    }

    public function getCartGoodsInfo($goodsIds)
    {
        if (!is_array($goodsIds)) {
            $goodsIds = explode(',', $goodsIds);
        }
        $field = $goodsField = array('goods_id AS goodsId', 'goods_name AS goodsName', 'saletype');
        $goodsInfo = $this->select($field)->where(
            $this->queryBuilder->expr()->in('goods_id', $goodsIds)
        )->findAll();
        $goodsData = array();
        foreach ($goodsInfo as $value) {
            $goodsData[$value['goodsId']] = $value;
        }
        return $goodsData;
    }
}

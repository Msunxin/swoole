<?php


namespace SOAServer\models;


use mkf\Model;

/**
 * Class Imgfavs 图集模块
 * @package SOAServer\models
 */
class Imgfavs extends Model
{

    const IMG_COVER = 1;    //封面
    const IMG_FAVS = 2;     //图集
    const IMG_ALL = 3;      //所有图片

    /**
     * 获取商品图片
     * @param int|array $goodsId 商品编号
     * @param int $imgType 获取图片类型
     * @param string|array $field
     * @return array
     */
    public function getGoodsImgs($goodsId, $imgType = 1, $field = '*')
    {
        if (!is_array($goodsId)) {
            $goodsId = explode(',', $goodsId);
        }
        $whereString = $this->queryBuilder->expr()->in('goods_id', $goodsId);
        $whereString .= ' and is_visible = :is_visible';
        switch ($imgType) {
            case self::IMG_COVER:
                $whereString .= ' and is_cover = :is_cover';
                $this->setParameter('is_cover', 1);
                break;
            case self::IMG_FAVS:
                $whereString .= ' and favs = :favs';
                $this->setParameter('favs', 1);
                break;
        }
        $this->select($field)->where($whereString)->setParameter('is_visible', 1);
        $img = $this->findAll();
        $goodsImg = array();
        foreach ($img as $value) {
            if (!$value['img1080_url']) {
                $value['img1080_url'] = $value['img640_url'];
            }
            switch ($imgType) {
                case self::IMG_COVER:
                    $goodsImg[$value['goods_id']] = $value;
                    break;
                default:
                    $goodsImg[$value['goods_id']][] = $value;
            }
        }
//        echo $this->queryBuilder->getSQL() . "\n";
//        var_dump($goodsImg);
        return $goodsImg;
    }

    public function getCartImgs($goodsId)
    {
        if (!is_array($goodsId)) {
            $goodsId = explode(',', $goodsId);
        }
        $whereString = $this->queryBuilder->expr()->in('goods_id', $goodsId);
        $whereString .= ' and is_visible = 1';
        $whereString .= ' and is_cover = 1';
        $field = array('goods_id AS goodsId', 'img120_url AS img120Url', 'img320_url AS img320Url',
            'img640_url AS img640Url');
        $img = $this->select($field)->where($whereString)->findAll();
        $goodsImg = array();
        foreach ($img as $value) {
            $goodsImg[$value['goodsId']] = $value;
        }
        return $goodsImg;
    }
}
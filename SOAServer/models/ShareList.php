<?php

namespace SOAServer\models;

/**
 * 用户分享列表model
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ShareList extends \mkf\Model {

    public function getUserGoodsShareList($uid, $goodsIdArr, $fields = '*') {
        $this->addIn('goods_id', $goodsIdArr);
        $this->where('user_id = :user_id')->setParameter('user_id', $uid);
        $this->addIn('share', array(2, 4, 5));
        return $this->select($fields)->findAll();
    }

}

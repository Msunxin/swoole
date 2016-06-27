<?php

namespace SOAServer\models;

/**
 * 
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ShareEvent extends \mkf\Model {

    public function getShareEvent($fields = '*') {
        return $this->select($fields)->find();
    }

}

<?php

namespace App\Model;

/**
 * 商品表
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Goods extends \Swoole\Model {

    public $table = 'cbd_goods';
    
    public $primary = "goods_id";

}

<?php

namespace tests\models;

/**
 * 
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class MockShareListModel {

    private $rawTestData;

    public function __construct($rawTestData) {
        $this->rawTestData = $rawTestData;
    }

    public function getUserGoodsShareList($uid, $goodsIdArr, $fields = '*') {
        return $this->rawTestData['shareList'];
    }

}

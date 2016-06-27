<?php

namespace tests\models;

/**
 * 
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class MockShareEventModel {

    private $rawTestData;

    public function __construct($rawTestData) {
        $this->rawTestData = $rawTestData;
    }
    
    public function getShareEvent($fields = '*') {
        return $this->rawTestData['shareEvent'];
    }

}

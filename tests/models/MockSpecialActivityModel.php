<?php

namespace tests\models;

/**
 * 模拟的 SpecialActivity model
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class MockSpecialActivityModel {

    private $rawTestData;

    public function __construct($rawTestData) {
        $this->rawTestData = $rawTestData;
    }

    public function getSpecialActivityBySpecialId($specialId) {
        return $this->rawTestData['specialActivity'];
    }

}

<?php

namespace tests\models;

/**
 * 模拟的mkfset model
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class MockMkfsetModel {

    private $rawTestData;

    public function __construct($rawTestData) {
        $this->rawTestData = $rawTestData;
    }

    public function getFullReduction() {
        return $this->rawTestData['fullReductions'];
    }

}

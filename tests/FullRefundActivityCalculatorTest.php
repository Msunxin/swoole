<?php

namespace tests;

use \SOAServer\business\cart\activity\FullRefundActivityCalculator;

/**
 * 全场满额减测试
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class FullRefundActivityCalculatorTest extends SpecialCalculatorTestBase {

    protected function setUp() {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/full-refund-data.json');
    }

    public function testCalculate() {
        $mkfsetModel = new models\MockMkfsetModel($this->rawTestData);

        $calculator = new FullRefundActivityCalculator;
        $calculator->setMkfsetModel($mkfsetModel);
        $result = $calculator->calculate($this->activityCalculatorData);
        $this->assertNotNull($result);
    }

}

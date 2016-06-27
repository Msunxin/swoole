<?php

namespace tests;

use \SOAServer\business\cart\activity\LimitedTimeDiscountActivityCalculator;

/**
 * 限时折扣测试
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class LimitedTimeDiscountActivityCalculatorTest extends SpecialCalculatorTestBase {

    protected function setUp() {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/limited-time-discount-data.json');
    }

    public function testCalculate() {
        $mockSpecialActivityModel = new models\MockSpecialActivityModel($this->rawTestData);

        $calculator = new LimitedTimeDiscountActivityCalculator();
        $calculator->setSpecialActivityModel($mockSpecialActivityModel);
        $result = $calculator->calculate($this->activityCalculatorData);
        $this->assertNotNull($result);
    }

}

<?php

namespace tests;

use SOAServer\business\cart\activity\SpecialFullOffActivityCalculator;

/**
 * 专区满额减 测试
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class SpecialFullOffActivityCalculatorTest extends SpecialCalculatorTestBase {

    protected function setUp() {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/special-fulloff-data.json');
    }

    public function testCalculate() {
        $calculator = new SpecialFullOffActivityCalculator();
        $result = $calculator->calculate($this->activityCalculatorData);
        $this->assertNotNull($result);
    }

}

<?php

namespace tests;

use \SOAServer\business\cart\activity\SaleGradientActivityCalculator;

/**
 * 多件阶梯测试
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class SaleGradientActivityCalculatorTest extends SpecialCalculatorTestBase {

    protected function setUp() {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/sale-gradient-data.json');
    }

    public function testCalculate() {
        $saleGradientActivityCalculator = new SaleGradientActivityCalculator();
        $result = $saleGradientActivityCalculator->calculate($this->activityCalculatorData);
        $this->assertNotNull($result);
    }

}

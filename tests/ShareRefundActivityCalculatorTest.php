<?php

namespace tests;

use SOAServer\business\cart\activity\ShareRefundActivityCalculator;

/**
 * 分享立减测试
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ShareRefundActivityCalculatorTest extends SpecialCalculatorTestBase {

    protected function setUp() {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/share-refund-data.json');
    }

    public function testCalculate() {
        $mockShareEventModel = new models\MockShareEventModel($this->rawTestData);
        $mockShareListModel = new models\MockShareListModel($this->rawTestData);

        $calculator = new ShareRefundActivityCalculator;
        $calculator->setShareEventModel($mockShareEventModel);
        $calculator->setShareListModel($mockShareListModel);
        
        $result = $calculator->calculate($this->activityCalculatorData);
        $this->assertNotNull($result);
    }

}

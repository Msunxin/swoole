<?php

namespace tests;

use SOAServer\business\cart\activity\CouponActivityCalculator;

class CouponActivityCalculatorTest extends SpecialCalculatorTestBase
{
    protected function setUp()
    {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/coupon-data.json');
    }

    public function testCalculate()
    {
        $activityCalculator = new CouponActivityCalculator();
        $activityCalculator->setCouponCodeModel(new \tests\models\MockCouponCodeModel());
        $activityCalculator->setCouponModel(new \tests\models\MockCouponModel());
        $result = $activityCalculator->calculate($this->activityCalculatorData);
        $array = $result->toArray();
        $discountAmount = 0;
        foreach ($array['goodsData'] as $goodsId => $goods) {
            foreach ($goods as $specId => $spec) {
                $discountAmount += $spec['discount_fee'];
            }
        }

        $this->assertNotNull($array);
        $this->assertEquals($discountAmount, 11);
    }
}

<?php

namespace tests;


use SOAServer\business\cart\activity\BundActivityCalculator;

class BundActivityCalculatorTest extends SpecialCalculatorTestBase
{
    protected function setUp()
    {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/bund-data.json');
    }

    public function testCalculate()
    {
        $activityCalculator = new BundActivityCalculator();
        $result = $activityCalculator->calculate($this->activityCalculatorData);
        $array = $result->toArray();
        $discountAmount = 0;
        foreach ($array['goodsData'] as $goodsId => $goods) {
            foreach ($goods as $specId => $spec) {
                $discountAmount += $spec['discount_fee'];
            }
        }

        $this->assertNotNull($array);
        $this->assertEquals($discountAmount, 46);
    }
}

<?php

namespace tests;

use SOAServer\business\cart\activity\TogetherActivityCalculator;

class TogetherActivityCalculatorTest extends SpecialCalculatorTestBase
{
    protected function setUp()
    {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/together-data.json');
    }

    public function testCalculate()
    {
        $togetherActivityCalculator = new TogetherActivityCalculator();
        $togetherActivityCalculator->setTogetherModel(new \tests\models\MockTogetherModel());
        $result = $togetherActivityCalculator->calculate($this->activityCalculatorData);
        $array = $result->toArray();
        $discountAmount = 0;
        foreach ($array['goodsData'] as $goodsId => $goods) {
            foreach ($goods as $specId => $spec) {
                $discountAmount += $spec['discount_fee'];
            }
        }
        $this->assertNotNull($array);
        $this->assertEquals($array['goodsData'][1][1]['discount_fee'], 4.4);
    }
}

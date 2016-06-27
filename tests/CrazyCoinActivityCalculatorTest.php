<?php
namespace tests;

use \SOAServer\business\cart\activity\CrazyCoinActivityCalculator;

/**
 * Class CrazyCoinActivityCalculatorTest
 *
 */
class CrazyCoinActivityCalculatorTest extends SpecialCalculatorTestBase
{
    protected function setUp()
    {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/crazy-coin-data.json');
    }

    public function testCalculate()
    {
        $crazyCoinActivityCalculator = new CrazyCoinActivityCalculator();
        $crazyCoinActivityCalculator->setUserAccountModel(new \tests\models\MockUserAccountModel());
        $result = $crazyCoinActivityCalculator->calculate($this->activityCalculatorData);
        $array = $result->toArray();
        $discountAmount = 0;
        foreach ($array['goodsData'] as $goodsId => $goods) {
            foreach ($goods as $specId => $spec) {
                $discountAmount += $spec['discount_fee'];
            }
        }

        $this->assertNotNull($array);
        $this->assertEquals($discountAmount, 10);
    }
}
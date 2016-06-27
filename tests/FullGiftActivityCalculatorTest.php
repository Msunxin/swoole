<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/7
 * Time: 18:07
 */

namespace tests;


use SOAServer\business\cart\activity\FullGiftActivityCalculator;

class FullGiftActivityCalculatorTest extends SpecialCalculatorTestBase
{
    protected function setUp()
    {
        $this->initActivityCalculatorData(TEST_RESOURCES_DIR . '/full-gift-data.json');
    }

    public function testCalculate()
    {
        $activityCalculator = new FullGiftActivityCalculator();
        $activityCalculator->setSpecModel(new \tests\models\MockSpecModel());
        $result = $activityCalculator->calculate($this->activityCalculatorData);
        $array = $result->toArray();

        $this->assertNotNull($array);
        $this->assertCount(1, $array['childGoods']);
    }
}

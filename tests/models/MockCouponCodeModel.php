<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/7
 * Time: 19:43
 */

namespace tests\models;


class MockCouponCodeModel
{
    public function getCouponCode($code)
    {
        return json_decode(file_get_contents(TEST_RESOURCES_DIR . '/coupon-code-model-data.json'), true);
    }
}
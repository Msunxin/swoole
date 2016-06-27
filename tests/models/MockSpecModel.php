<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/7
 * Time: 18:32
 */

namespace tests\models;


class MockSpecModel
{
    public function getGoodsStock($goodsId)
    {
        $array = json_decode(file_get_contents(TEST_RESOURCES_DIR . '/spec-data.json'), true);
        return $array;
    }
}
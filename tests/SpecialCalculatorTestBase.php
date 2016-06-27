<?php

namespace tests;

use \SOAServer\business\cart\activity\ActivityCalculatorData;

/**
 * 专区活动测试基类
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class SpecialCalculatorTestBase extends \PHPUnit_Framework_TestCase {

    /**
     * @var array 原生测试数据
     */
    protected $rawTestData;

    /**
     * @var ActivityCalculatorData
     */
    protected $activityCalculatorData;

    protected function initActivityCalculatorData($dataFilePath) {
        $testData = json_decode(file_get_contents($dataFilePath), true);

        $this->rawTestData = $testData;

        $this->activityCalculatorData = new ActivityCalculatorData();
        $this->activityCalculatorData->doNotDoInit();
        if (isset($testData['userId'])) {
            $this->activityCalculatorData->setUserId($testData['userId']);
        }
        if (isset($testData['couponCode'])) {
            $this->activityCalculatorData->setCouponCode($testData['couponCode']);
        }
        $this->activityCalculatorData->setActivityId($testData['special']['special_id']);
        $this->activityCalculatorData->setActivity($testData['special']);
        $this->activityCalculatorData->setActivityGoods($testData['specialGoodsIdList']);

        //添加 商品 - 规格 - 数量
        foreach ($testData['goodsSpecNumList'] as $goodsSpecNumInfo) {
            $goodsId = $goodsSpecNumInfo['goodsId'];
            $specId = $goodsSpecNumInfo['specId'];
            $num = $goodsSpecNumInfo['num'];
            $this->activityCalculatorData->addGoodsData($goodsId, $specId, $num);
        }

        //设置规格列表
        foreach ($testData['specList'] as $specInfo) {
            $goodsId = $specInfo['goods_id'];
            $specId = $specInfo['spec_id'];
            foreach ($specInfo as $fieldName => $fieldValue) {
                $this->activityCalculatorData->setSpecData($goodsId, $specId, $fieldName, $fieldValue);
            }
        }
    }

}

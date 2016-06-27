<?php

namespace SOAServer\business\cart\activity;

/**
 * 活动计算接口
 * Interface IActivityCalculator
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
interface IActivityCalculator
{

    /**
     * 计算购物车商品价格
     * @param ActivityCalculatorData $activityCalculatorData
     * @return Boolean
     */
    public function calculate(ActivityCalculatorData $activityCalculatorData);
    


}
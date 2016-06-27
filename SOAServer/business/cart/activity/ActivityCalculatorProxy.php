<?php

namespace SOAServer\business\cart\activity;

/**
 * 活动计算入口
 * Class ActivityCalculator
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
class ActivityCalculatorProxy extends ActivityBase
{

    /**
     * 活动计算
     * @param ActivityCalculatorData $activityCalculatorData
     * @return ActivityCalculatorResult
     */
    public function calculate(ActivityCalculatorData $activityCalculatorData)
    {
        $className = "\\SOAServer\\business\\cart\\activity\\" . $activityCalculatorData->getActivityType() . "ActivityCalculator";
        if (!class_exists($className)) {
            return false;
        }
        $this->activityCalculatorResult->setActivityId($activityCalculatorData->getActivityId());
        $this->activityCalculatorResult->setActivityType($activityCalculatorData->getActivityType());
        $class = new $className($this->activityCalculatorResult);
        $result = $class->calculate($activityCalculatorData);

//        var_dump($result, json_decode(json_encode($result), true));
        return $result;
    }


}

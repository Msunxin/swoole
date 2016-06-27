<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/6
 * Time: 21:26
 */

namespace SOAServer\business\cart\activity;


abstract class ActivityBase implements IActivityCalculator
{
    /**
     * @var ActivityCalculatorResult
     */
    protected $activityCalculatorResult = null;

    public function __construct(ActivityCalculatorResult $activityCalculatorResult = null)
    {
        if ($activityCalculatorResult) {
            $this->activityCalculatorResult = $activityCalculatorResult;
        } else {
            $this->activityCalculatorResult = new ActivityCalculatorResult();
        }
    }
}
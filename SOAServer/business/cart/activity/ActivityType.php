<?php

namespace SOAServer\business\cart\activity;

/**
 * 商品类型
 * Class ActivityType
 * @package SOAServer\business\cart\activity
 * @author guizhiming <zhiming.gui@baozun.cn>
 */
class ActivityType
{
    /**
     * 捆绑销售
     */
    const ACTIVITY_BUND_GOODS = 'Bund';

    /**
     * 全场满减
     */
    const ACTIVITY_FULL_REFUND = 'FullRefund';

    /**
     * 满额赠
     */
    const ACTIVITY_FULL_GIFT = 'FullGift';

    /**
     * 分享立减
     */
    const ACTIVITY_SHARE_REFUND = 'ShareRefund';

    /**
     * 疯狂币
     */
    const ACTIVITY_CRAZY_COIN = 'CrazyCoin';

    /**
     * 多件起售
     */
    const ACTIVITY_MULT_SELL = 'MultSell';

    /**
     * 累计折扣
     */
    const ACTIVITY_CUMU_DISCOUNT = 'CumuDiscount';

    /**
     * 优惠券
     */
    const ACTIVITY_COUPON = 'Coupon';

    /**
     * 专区满减
     */
    const ACTIVITY_SPECIAL_FULL_OFF = 'SpecialFullOff';

    /**
     * 群批活动
     */
    const ACTIVITY_TOGETHER = 'Together';

    /**
     * 多件阶梯
     */
    const ACTIVITY_SALE_GRADIENT = 'SaleGradient';
}
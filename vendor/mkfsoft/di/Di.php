<?php

namespace mkf\di;

/**
 * 依赖注入接口类
 *
 * @author Lumeng <zhengb302@163.com>
 */
class Di {

    /**
     *
     * @var ServiceContainer
     */
    private static $serviceContainer;

    public static function initialize($configs) {
        self::$serviceContainer = new ServiceContainer($configs);
    }

    public static function get($serviceName) {
        return self::$serviceContainer->get($serviceName);
    }

}

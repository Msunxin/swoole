<?php

/*
 * 应用配置 - 服务对象容器（依赖注入）配置
 */

return array(
    'doctrine' => array(
        'class' => 'mkf\db\Doctrine',
    ),
    'connectionManager' => array(
        'class' => 'mkf\db\ConnectionManager',
    ),
    'cache' => array(
        'class' => 'mkf\caching\RedisCache',
    ),
    'logger' => array(
        'class' => 'mkf\log\Logger',
        'arguments' => array(function() {
                $targetClass = 'mkf\log\ConsoleTarget';
                return new $targetClass();
        }),
    ),
);

<?php
$redis['master'] = array(
    'redis_id' => 'master',
    'host'    => array(
        "10.8.4.218:6379",
        "10.8.4.218:6380",
        "10.8.4.218:6381",
        "10.8.4.218:6382",
        "10.8.4.218:6383",
        "10.8.4.218:6384",
    ),
    'timeout' => 0.25,
    'read_timeout' => 0.5,
);
return $redis;

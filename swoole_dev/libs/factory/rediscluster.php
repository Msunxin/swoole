<?php
global $php;

$config = $php->config['rediscluster'][$php->factory_key];
var_dump('rediscluster config', $config);
if (empty($config) or empty($config['host']))
{
    throw new Exception("require redis[$php->factory_key] config.");
}

if (empty($config['timeout']))
{
    $config['timeout'] = 1;
}

if (empty($config['read_timeout']))
{
    $config['read_timeout'] = 1;
}

$connect_array = $config['host'];
$connect_array[] = $config['timeout'];
$connect_array[] = $config['read_timeout'];

$redis = new RedisCluster(NULL, $connect_array);

return $redis;
<?php

namespace mkf\di;

use Closure;

/**
 * 服务容器<br />
 * <b>不要直接使用此类！！！</b>
 *
 * @author Lumeng <zhengb302@163.com>
 */
class ServiceContainer {

    /**
     *
     * @var array 
     */
    private $configs;

    /**
     *
     * @var Map 
     */
    private $services;

    public function __construct($configs) {
        $this->configs = $configs;
        $this->services = array();
    }

    private function buildService($serviceName) {
        $serviceConfig = $this->configs[$serviceName];
        $parsedArgs = isset($serviceConfig['arguments'])
                ? $this->parseArgs($serviceConfig['arguments']) : null;

        if (empty($parsedArgs)) {
            $serviceInstance = new $serviceConfig['class']();
        } else {
            $ref = new \ReflectionClass($serviceConfig['class']);
            $serviceInstance = $ref->newInstanceArgs($parsedArgs);
        }

        $this->services[$serviceName] = $serviceInstance;
    }

    /**
     * 解析参数列表
     * @param array $rawArgs 此处的原始参数列表必须为数组，否则会报错
     * @return null|array
     */
    private function parseArgs($rawArgs) {
        if (!is_array($rawArgs)) {
            trigger_error('service arguments must be array!', E_USER_ERROR);
        }

        if (empty($rawArgs)) {
            return null;
        }

        $args = array();
        foreach ($rawArgs as $rawArg) {
            $args[] = $this->parseArg($rawArg);
        }
        return $args;
    }

    /**
     * 解析单个参数
     * @param mixed $rawArg <br/>
     * 参数示例：
     * <ul>
     *   <li>0、null、对象、长度小于或等于1的字符串,etc  返回原参数</li>
     *   <li>@bar  则表示传入一个名称为 bar 的服务对象</li>
     *   <li>\@HelloKity  则实际传入的是字符串 @HelloKity</li>
     *   <li>匿名函数  则传入此匿名函数的返回结果</li>
     *   <li>其他任何字符串  返回原参数</li>
     * </ul>
     * @return mixed 可能的返回值：服务对象、字符串,etc
     */
    private function parseArg($rawArg) {
        if ($rawArg instanceof Closure) {
            return $rawArg();
        }

        $rawArgLen = strlen($rawArg);

        if (!is_string($rawArg) || $rawArgLen <= 1) {
            return $rawArg;
        }

        //以 @ 开头，则表示传入一个服务对象
        //如 @bar，则表示传入一个名称为 bar 的服务对象
        if ($rawArg[0] == '@') {
            $serviceName = substr($rawArg, 1, $rawArgLen - 1);
            return $this->get($serviceName);
        }

        //以 \@ 开头，则表示传入一个以@开头的字符串，反斜杠作为转义符
        //如 \@HelloKity，则实际传入的是 @HelloKity
        if ($rawArg[0] == '\\' && $rawArg[1] == '@') {
            return substr($rawArg, 1, $rawArgLen - 1);
        }

        //其他字符串
        return $rawArg;
    }

    public function get($serviceName) {
        if (!isset($this->configs[$serviceName])) {
            trigger_error('service not found!', E_USER_ERROR);
        }

        if (!isset($this->services[$serviceName])) {
            $this->buildService($serviceName);
        }

        return $this->services[$serviceName];
    }

}

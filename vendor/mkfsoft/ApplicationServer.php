<?php

namespace mkf;

use \Swoole\Network\Server as SwooleServer;
use \Swoole\Protocol\SOAServer;

/**
 * 
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ApplicationServer extends SOAServer {

    /**
     * @var array
     */
    private $configs;

    /**
     * @var SwooleServer 
     */
    public $server;

    public function __construct($configs) {
        $this->configs = $configs;
        Mkf::$app = new Application($configs);

        $this->initialize();
    }

    protected function initialize() {
        $this->buildServer();
    }

    private function buildServer() {
        $host = $this->configs['serverAddress'];
        $port = $this->configs['serverPort'];
        $server = SwooleServer::autoCreate($host, $port);
        $server->setProtocol($this);
        if ($this->configs['serverDaemonize']) {
            $server->daemonize();
        }
    }

    public function run() {
        $runConfigs = array(
            'worker_num' => $this->configs['serverWorkerNum'],
            'max_request' => $this->configs['serverMaxRequestPerWorker'],
            'dispatch_mode' => 3,
            'open_length_check' => 1,
            'package_max_length' => $this->packet_maxlen,
            'package_length_type' => 'N',
            'package_body_offset' => \Swoole\Protocol\SOAServer::HEADER_SIZE,
            'package_length_offset' => 0,
        );
        $this->server->run($runConfigs);
    }

    protected function call($request) {
        if (empty($request['call']) || $request['call'][0] != '/') {
            return array('errno' => self::ERR_PARAMS);
        }
        $callable = $callable = $this->parseApi($request['call']);
        if (!is_callable($callable)) {
            return array('errno' => self::ERR_NOFUNC);
        }
        $ret = call_user_func_array($callable, $request['params']);
        if ($ret === false) {
            return array('errno' => self::ERR_CALL);
        }
        return array('errno' => 0, 'data' => $ret);
    }

    protected function parseApi($api) {
        list(, $controller, $method) = explode('/', $api);
        if (!isset($this->cachedControllers[$controller])) {
            $controllerNamespace = "{$this->configs['appName']}\\controllers";
            $controllerClass = "{$controllerNamespace}\\" . ucfirst($controller) . 'Controller';
            $this->cachedControllers[$controller] = new $controllerClass;
        }
        return array($this->cachedControllers[$controller], $method);
    }

}

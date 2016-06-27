<?php

namespace mkf;

/**
 * 应用服务器 for 单元测试
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ApplicationTestServer {

    /**
     * @var array
     */
    private $configs;

    public function __construct($configs) {
        $this->configs = $configs;
        Mkf::$app = new Application($configs);
    }

    public function run() {
        //do nothing
    }

}

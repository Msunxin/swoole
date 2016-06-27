<?php

namespace mkf;

/**
 * Request 请求封装
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Request {

    private $params = array();

    public function setParams($params) {
        $this->params = $params;
    }

    /**
     * 返回全部参数
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * 取得参数值
     * @param string $paramName 参数名称
     * @return mixed 参数值。如果没有此参数，则返回null
     */
    public function getParam($paramName) {
        return isset($this->params[$paramName]) ? $this->params[$paramName] : null;
    }

}

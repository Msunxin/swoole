<?php

namespace mkf\db;

/**
 * 自定义QueryBuilder
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class QueryBuilder extends \Doctrine\DBAL\Query\QueryBuilder {

    /**
     * Add a collection of query parameters for the query being constructed.
     * 
     * @param array $params The query parameters to add.
     * 
     * @return \mkf\db\QueryBuilder
     */
    public function addParameters($params) {
        foreach ($params as $placeholder => $paramValue) {
            $this->setParameter($placeholder, $paramValue);
        }
        return $this;
    }

}

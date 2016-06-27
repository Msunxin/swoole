<?php

namespace mkf\db;

/**
 * 数据库连接
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Connection extends \Doctrine\DBAL\Connection {

    /**
     * 创建并返回QueryBuilder实例
     * @return QueryBuilder
     */
    public function createQueryBuilder() {
        return new QueryBuilder($this);
    }

}

<?php

namespace mkf\db;

use mkf\Mkf;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

/**
 * 数据库连接管理器
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ConnectionManager {

    private $connection;

    /**
     * 返回数据库连接
     * @return Connection 数据库连接
     */
    public function getConnection() {
        if (is_null($this->connection)) {
            $this->buildConnection();
        }
        return $this->connection;
    }

    private function buildConnection() {
        $appConfigs = Mkf::$app->configs;
        $dbParams = array(
            'driver' => $appConfigs['dbDriver'],
            'host' => $appConfigs['dbHost'],
            'port' => $appConfigs['dbPort'],
            'user' => $appConfigs['dbUser'],
            'password' => $appConfigs['dbPassword'],
            'dbname' => $appConfigs['dbName'],
            'charset' => $appConfigs['dbCharset'],
        );
        $dbParams['wrapperClass'] = 'mkf\db\Connection';
        $this->connection = DriverManager::getConnection($dbParams);
    }

}

<?php

namespace tests;

use mkf\Mkf;

/**
 * 单元测试数据库测试基类
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
abstract class GenericDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

    /**
     * only instantiate pdo once for test clean-up/fixture load
     * @var PDO
     */
    static private $pdo = null;

    /**
     * only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection 
     */
    private $conn = null;

    /**
     * 
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection() {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                $host = Mkf::$app->configs['dbHost'];
                $dbName = Mkf::$app->configs['dbName'];
                $dsn = "mysql:dbname={$dbName};host={$host}";
                $user = Mkf::$app->configs['dbUser'];
                $password = Mkf::$app->configs['dbPassword'];
                self::$pdo = new \PDO($dsn, $user, $password);

                $dbCharset = Mkf::$app->configs['dbCharset'];
                self::$pdo->query("SET NAMES {$dbCharset}");
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, Mkf::$app->configs['dbName']);
        }

        return $this->conn;
    }

    /**
     * 创建组合的Mysql Xml DataSet
     * @param array $xmlFileArray mysql xml文件路径（绝对路劲）数组
     * @return PHPUnit_Extensions_Database_DataSet_CompositeDataSet
     */
    protected function createCompositeMySQLXMLDataSet($xmlFileArray) {
        $compositeDataSet = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet();
        foreach ($xmlFileArray as $xmlFile) {
            $dataSet = $this->createMySQLXMLDataSet($xmlFile);
            $compositeDataSet->addDataSet($dataSet);
        }
        return $compositeDataSet;
    }

}

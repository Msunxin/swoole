<?php

namespace mkf;

use mkf\db\QueryBuilder;
use mkf\db\Connection;
use mkf\helpers\Inflector;
use PDO;
use mkf\Mkf;
use mkf\helpers\StringHelper;

/**
 * Model
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Model {

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function __construct() {
        $this->queryBuilder = $this->createQueryBuilder();
    }

    /**
     * 创建并返回一个QueryBuilder实例
     * @return QueryBuilder
     */
    public function createQueryBuilder() {
        return $this->getConnection()->createQueryBuilder()->from(self::getTableName());
    }

    /**
     * 返回数据库连接
     * @return Connection 数据库连接
     */
    public function getConnection() {
        return Mkf::$app->connection;
    }

    /**
     * 取得model表所对应的表名，带表前缀<br />
     * 示例：
     * Goods --> pre_goods
     * OrderInfo --> pre_order_info
     * 
     * @return string
     */
    public static function getTableName() {
        return Mkf::$app->configs['dbTablePrefix']
                . Inflector::camel2id(StringHelper::basename(get_called_class()), '_');
    }

    /**
     * 增加 in 条件<br />
     * 示例：
     * $this->addIn('uid', array(23, 25, 87));
     * 
     * @param string $fieldName 字段名称
     * @param array $fieldValues 字段值数组
     * @return Model
     */
    public function addIn($fieldName, $fieldValues) {
        $placeholders = array();
        $params = array();
        $randomStr = substr(uniqid(), -4);
        foreach ($fieldValues as $k => $fieldValue) {
            $placeholder = "a{$randomStr}_{$k}";
            $placeholders[] = ":{$placeholder}";
            $params[$placeholder] = $fieldValue;
        }
        $where = $fieldName . ' IN (' . implode(',', $placeholders) . ')';
        $this->queryBuilder->andWhere($where)->addParameters($params);
        return $this;
    }

    /**
     * 返回一条记录
     * @return mixed 成功返回一个关联数组；失败返回false
     */
    public function find() {
        $stmt = $this->queryBuilder->execute();

        $this->resetQueryBuilder();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 返回查询结果的所有记录
     * @return array
     */
    public function findAll() {
        $stmt = $this->queryBuilder->execute();

        $this->resetQueryBuilder();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 添加单条记录
     * @param array $data 数据（关联数组）
     * @return int 返回新插入数据的id
     */
    public function add($data) {
        $placeholders = array();
        foreach (array_keys($data) as $key) {
            $placeholders[$key] = ":$key";
        }

        $this->queryBuilder->insert(self::getTableName())
                ->values($placeholders)->setParameters($data);

        $this->queryBuilder->execute();

        $this->resetQueryBuilder();

        return $this->getConnection()->lastInsertId();
    }

    public function save($data) {
        $this->queryBuilder->update(self::getTableName());

        $parameters = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->queryBuilder->set($key, $this->parseValue($value));
                continue;
            }

            $placeholder = $key;
            $this->queryBuilder->set($key, ":$placeholder");
            $parameters[$placeholder] = $value;
        }
        $this->queryBuilder->addParameters($parameters);

        $result = $this->queryBuilder->execute();

        $this->resetQueryBuilder();

        return $result;
    }

    private function parseValue($value) {
        $type = strtoupper($value[0]);
        if ($type == 'SQL') {
            return $value[1];
        }
    }

    public function remove() {
        $this->queryBuilder->delete(self::getTableName());

        $result = $this->queryBuilder->execute();

        $this->resetQueryBuilder();

        return $result;
    }

    private function resetQueryBuilder() {
        unset($this->queryBuilder);
        $this->queryBuilder = $this->createQueryBuilder();
    }

    public function __call($methodName, $arguments) {
        if (method_exists($this->queryBuilder, $methodName)) {
            call_user_func_array(array($this->queryBuilder, $methodName), $arguments);
            return $this;
        }
        trigger_error("$methodName not found", E_USER_ERROR);
    }

}

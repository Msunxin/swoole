<?php

namespace tests\mkfsoft;

/**
 * model层测试
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ModelTest extends \tests\GenericDatabaseTestCase {

    /**
     * @var \SOAServer\models\HelloWorld
     */
    private $helloWorldMode;

    protected function setUp() {
        parent::setUp();
        $this->helloWorldMode = new \SOAServer\models\HelloWorld();
    }

    protected function getDataSet() {
        return $this->createMySQLXMLDataSet(TEST_RESOURCES_DIR . '/hello-world-fixture.xml');
    }

    /**
     * 测试addIn方法
     */
    public function testAddIn() {
        $idList = array(1, 2);
        $hwList = $this->helloWorldMode->getHelloWorldsByIdList($idList);
        $this->assertCount(2, $hwList);
    }

    /**
     * 测试addIn方法和where方法混合调用
     */
    public function testAddInWithWhere() {
        $idList = array(2, 3);
        $hwList = $this->helloWorldMode->getHelloWorldsByIdListWithoutDeleted($idList);
        $this->assertCount(1, $hwList);

        $firstRow = $hwList[0];
        $this->assertEquals('wangwu', $firstRow['name']);
    }

    /**
     * 测试where方法和addIn方法混合调用
     */
    public function testWhereWithAddIn() {
        $idList = array(2, 3);
        $hwList = $this->helloWorldMode->select('*')->where('name = :name')
                        ->setParameter('name', 'wangwu')->addIn('hw_id', $idList)->findAll();
        $this->assertCount(1, $hwList);

        $firstRow = $hwList[0];
        $this->assertEquals('wangwu', $firstRow['name']);
    }

    /**
     * 测试SQL语法错误，会抛出异常<br />
     * SQL: SELECT  FROM cbd_hello_world WHERE name = :name
     * @expectedException \Doctrine\DBAL\Exception\SyntaxErrorException
     */
    public function testFindWhenSyntaxError() {
        $this->helloWorldMode->where('name = :name')
                ->setParameter('name', 'zhuyuanzhang')->find();
    }

    /**
     * 测试find方法，没有满足条件的记录，返回false
     */
    public function testFindWhenNoRowReturns() {
        $result = $this->helloWorldMode->select('*')->where('name = :name')
                        ->setParameter('name', 'zhuyuanzhang')->find();
        $this->assertFalse($result);
    }

    /**
     * 测试findAll方法，没有满足条件的记录，返回空数组
     */
    public function testFindAllWhenNoRowReturns() {
        $result = $this->helloWorldMode->select('*')
                        ->addIn('hw_id', array(4, 5, 6))->findAll();
        $this->assertTrue(is_array($result));
        $this->assertCount(0, $result);
    }

    /**
     * 测试正常的插入数据<br />
     * 正常插入数据，会返回新插入数据的id
     */
    public function testAdd() {
        $data = array(
            'name' => 'mengshabi',
            'msg' => '大家好，我是孟傻逼',
            'add_time' => time(),
        );
        $lastInsertId = $this->helloWorldMode->add($data);
        $this->assertEquals(4, $lastInsertId);
    }

    /**
     * 测试插入数据，缺少必填字段，会抛出异常<br />
     * add_time 是必填字段，且没有默认值
     * @expectedException \Doctrine\DBAL\Exception\DriverException
     */
    public function testAddWhenLackOfFieldRequired() {
        $data = array(
            'name' => 'mengshabi',
            'msg' => '大家好，我是孟傻逼',
                //'add_time' => time(),
        );
        $this->helloWorldMode->add($data);
    }

    /**
     * 测试插入数据，字段名不存在或拼写错误，会抛出异常<br />
     * 表中不存在 country 字段
     * @expectedException \Doctrine\DBAL\Exception\InvalidFieldNameException
     */
    public function testAddWhenFieldNameIsInvalid() {
        $data = array(
            'name' => 'mengshabi',
            'msg' => '大家好，我是孟傻逼',
            'add_time' => time(),
            'country' => 'the USA',
        );
        $this->helloWorldMode->add($data);
    }

    /**
     * 测试正常修改一条记录
     */
    public function testSave() {
        $data = array(
            'msg' => '大家好，我是李四',
            'view_count' => array('sql', 'view_count + 1'),
            'is_deleted' => 0,
            'add_time' => time(),
        );
        $result = $this->helloWorldMode->where('hw_id = :hw_id')
                        ->setParameter('hw_id', 2)->save($data);
        $this->assertEquals(1, $result);

        $updatedRow = $this->helloWorldMode->select('*')->where('hw_id = :hw_id')
                        ->setParameter('hw_id', 2)->find();
        $this->assertNotNull($updatedRow);
        $this->assertEquals('大家好，我是李四', $updatedRow['msg']);
        $this->assertEquals(251, $updatedRow['view_count']);
        $this->assertEquals(0, $updatedRow['is_deleted']);
    }

    /**
     * 测试正常删除一条记录
     */
    public function testRemove() {
        $result = $this->helloWorldMode->where('hw_id = :hw_id')
                        ->setParameter('hw_id', 2)->remove();
        $this->assertEquals(1, $result);
        $this->assertTableRowCount('cbd_hello_world', 2);
    }

    /**
     * 测试删除不存在的记录
     */
    public function testRemoveWhenRowIsNotExist() {
        $result = $this->helloWorldMode->where('hw_id = :hw_id')
                        ->setParameter('hw_id', 5)->remove();
        $this->assertEquals(0, $result);
        $this->assertTableRowCount('cbd_hello_world', 3);
    }

    /**
     * 测试删除多条记录
     */
    public function testRemoveMultiRows() {
        $result = $this->helloWorldMode->addIn('hw_id', array(1, 2, 3))->remove();
        $this->assertEquals(3, $result);
        $this->assertTableRowCount('cbd_hello_world', 0);
    }

}

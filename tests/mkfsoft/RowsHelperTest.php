<?php

namespace tests\mkfsoft;

use \mkf\helpers\RowsHelper;

/**
 * RowsHelper 测试
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class RowsHelperTest extends \PHPUnit_Framework_TestCase {

    private $rows;

    protected function setUp() {
        $this->rows = array(
            array('name' => 'zhangsan', 'age' => 18),
            array('name' => 'lisi', 'age' => 17),
            array('name' => 'wangwu', 'age' => 25),
        );
    }

    /**
     * 测试正序排序
     */
    public function testSortByFieldAsc() {
        RowsHelper::sortRowsByField($this->rows, 'age');

        $firstRow = $this->rows[0];
        $this->assertEquals('lisi', $firstRow['name']);
        $this->assertEquals(17, $firstRow['age']);

        $secondRow = $this->rows[1];
        $this->assertEquals('zhangsan', $secondRow['name']);
        $this->assertEquals(18, $secondRow['age']);

        $lastRow = $this->rows[2];
        $this->assertEquals('wangwu', $lastRow['name']);
        $this->assertEquals(25, $lastRow['age']);
    }

    /**
     * 测试逆序排序
     */
    public function testSortByFieldDesc() {
        RowsHelper::sortRowsByField($this->rows, 'age', SORT_DESC);

        $firstRow = $this->rows[0];
        $this->assertEquals('wangwu', $firstRow['name']);
        $this->assertEquals(25, $firstRow['age']);

        $secondRow = $this->rows[1];
        $this->assertEquals('zhangsan', $secondRow['name']);
        $this->assertEquals(18, $secondRow['age']);

        $lastRow = $this->rows[2];
        $this->assertEquals('lisi', $lastRow['name']);
        $this->assertEquals(17, $lastRow['age']);
    }

}

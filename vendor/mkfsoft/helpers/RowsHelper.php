<?php

namespace mkf\helpers;

/**
 * 数据库表记录(多行)帮助类
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class RowsHelper {

    /**
     * 按某个字段对多行记录进行排序
     * @param array $rows 多行记录
     * @param string $fieldName 排序依据的字段名
     * @param int $sortType SORT_ASC，正序；SORT_DESC，逆序
     */
    public static function sortRowsByField(&$rows, $fieldName, $sortType = SORT_ASC) {
        usort($rows, function($rowA, $rowB) use ($fieldName, $sortType) {
            if ($rowA[$fieldName] == $rowB[$fieldName]) {
                return 0;
            }
            $retVal = $sortType == SORT_ASC ? -1 : 1;
            return $rowA[$fieldName] < $rowB[$fieldName] ? $retVal : -$retVal;
        });
    }

}

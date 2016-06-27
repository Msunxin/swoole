<?php

namespace SOAServer\models;

/**
 * HelloWorld model
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class HelloWorld extends \mkf\Model {

    public function addHelloWorld() {
        $now = time();
        $data = array(
            'name' => 'hw' . $now,
            'msg' => 'hello world' . $now,
            'add_time' => $now,
        );
        return $this->add($data);
    }

    public function updateHelloWorld() {
        return $this->where('hw_id = :hw_id')->setParameter("hw_id", 2)->save(array('add_time' => time()));
    }

    public function removeHelloWorld() {
        return $this->where('hw_id = :hw_id')->setParameter("hw_id", 1)->remove();
    }

    public function getHelloWorldsByIdList($idList, $fields = '*') {
        return $this->select($fields)->addIn('hw_id', $idList)->findAll();
    }

    public function getHelloWorldsByIdListWithoutDeleted($idList, $fields = '*') {
        return $this->select($fields)->addIn('hw_id', $idList)
                        ->where('name = :name')->setParameter('name', 'wangwu')->findAll();
    }

}

<?php

namespace tests\models;

class MockUserAccountModel
{
    /**
     * 获取用户疯狂币数量
     * @param $userId
     * @return mixed
     */
    public function getUserCoin($userId)
    {
        $coin = 0;
        switch ($userId) {
            case 49 :
                $coin = 200;
                break;
        }
        return $coin;
    }
}
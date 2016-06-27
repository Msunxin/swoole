<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/2
 * Time: 18:56
 */

namespace SOAServer\models;

use mkf\Model;

class Shielding extends Model
{
    /**
     * 是否过滤用户
     * @param $userId
     * @return array
     */
    public function isFilter($userId)
    {
        return $this->select('*')->where('shield_content = :shield_content AND type = 3')
            ->setParameter('shield_content', $userId)->find();
    }
}
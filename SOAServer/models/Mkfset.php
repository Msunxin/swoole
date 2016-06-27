<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/2
 * Time: 19:51
 */

namespace SOAServer\models;

use mkf\Model;

class Mkfset extends Model
{
    public function getSetting($type, $field = '*')
    {
        if (!$type) {
            return false;
        }
        return $this->select($field)->where('type = :type')->setParameter('type', $type)->find();
    }
    
    public function getSettings($type, $fields = '*') {
        return $this->select($fields)->where('type = :type')->setParameter('type', $type)->findAll();
    }

    public function getFullReduction() {
        return $this->getSettings(6);
    }
}
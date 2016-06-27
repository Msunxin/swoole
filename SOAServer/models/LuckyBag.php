<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/12
 * Time: 11:22
 */

namespace SOAServer\models;

use mkf\Model;

class LuckyBag extends Model
{
    public function getLuckBag($bagSpecId)
    {
        return $this->select("*")
            ->where('bag_spec_id = :bag_spec_id')
            ->setParameter('bag_spec_id', $bagSpecId)
            ->findAll();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2015/9/12
 * Time: 11:26
 */

namespace SOAServer\models;

use mkf\Model;

class LuckyBagDetail extends Model
{
    public function getLuckyBagDetail($bagIds)
    {
        $r = $this->select('*')
            ->where($this->queryBuilder->expr()->in('bag_id', $bagIds))
            ->findAll();
        return $r;
    }
}
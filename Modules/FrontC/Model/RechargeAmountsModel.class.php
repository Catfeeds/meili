<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/11
 * Time: 9:43
 */

namespace FrontC\Model;


class RechargeAmountsModel extends FrontBaseModel
{
    /**
     * 获取充值金额
     * */
    public function getRows()
    {
        $result=$this->field('amounts')->order('id asc')->select();
        if(empty($result))
            return array();
        return $result;
   }
}
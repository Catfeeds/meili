<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/8
 * Time: 17:42
 */

namespace FrontC\Model;


class BespeakReasonModel extends FrontBaseModel
{
    /**
     * [获取取消预约理由]
     * */
    public function getRows()
    {
       $reasons=$this->where(array('status'=>1))->field('id reason_id,reason')->select();
       if(empty($reasons)){
           return array();
       }else{
           return $reasons;
       }
   }
}
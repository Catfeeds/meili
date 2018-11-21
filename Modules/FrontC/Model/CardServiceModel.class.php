<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/19
 * Time: 9:08
 */

namespace FrontC\Model;


class CardServiceModel extends FrontBaseModel
{
    /**
     * 获取服务
     * */
    public function getService($param=array())
    {
        $servers=$this
            ->where($param)
            ->field('service_id,service_name,count,cover,service_short_desc')
            ->select();
        if(!empty($servers)){
            return $servers;
        }else{
            return array();
        }
   }
}
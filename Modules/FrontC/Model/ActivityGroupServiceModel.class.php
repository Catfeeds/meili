<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/9
 * Time: 16:17
 */

namespace FrontC\Model;


class ActivityGroupServiceModel extends FrontBaseModel
{
    /**
     * [开团详情获取团购商品信息调用、]
     * */
    public function getRows($param=array())
    {
        $group_services=$this
            ->where($param)
            ->field('id group_service_id,service_id,group_price,people_limit')
            ->select();
        foreach ($group_services as $k=>$v){
            $service_info=M('Service')->field('service_name,cover')->find($v['service_id']);
            $cover_path=M('File')->field('abs_url')->find($service_info['cover']);
            $v['service_name']=$service_info['service_name'];
            $v['cover']=$cover_path['abs_url'];
            $result[]=$v;
        }
        if(!empty($result)){
            return $result;
        }else{
            return array();
        }

   }

   /**
    * 获取单条信息
    * */
    public function getRow($param=array())
    {
        $row=$this->where($param)->field('service_id,group_price,service_type')->find();
        if(!empty($row)){
            return $row;
        }else{
            return array();
        }
   }
}
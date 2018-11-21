<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/10
 * Time: 20:27
 */

namespace FrontC\Model;


class ActivityGroupListModel extends FrontBaseModel
{
    /**
     * [团购详情调用]
     * */
    public function findRow($param=array())
    {
        $group=$this
            ->field('id group_list_id,group_sn,status,group_service_id,end_time')
            ->where($param)
            ->find();
        //获取该团购商品信息
        $group_service_info=M('ActivityGroupService')->field('service_id,group_price,people_limit,number')->find($group['group_service_id']);
        $group['group_service_info']=$group_service_info;
        //获取商品信息
        $parm=array('service.id'=>$group_service_info['service_id']);
        $service_info=D('FrontC/Service','Model')->getRowSe($parm);
        $group['service_info']=$service_info;
        if(empty($group))
            return array();
        return $group;
    }
    /**
     * [我的团购列表调用、]
     * */
    public function getRows($param=array())
    {
        $groups=$this
            ->field('id group_list_id,group_service_id,group_sn,status')
            ->where($param)
            ->select();
        //获取团购商品相关信息
        foreach ($groups as $k=>$v){
            $group_service_info=M('ActivityGroupService')->field('service_id,group_price,people_limit,number')->find($v['group_service_id']);
            $v['group_service_info']=$group_service_info;
            //获取商品信息
            $parm=array('service.id'=>$group_service_info['service_id']);
            $service_info=D('FrontC/Service','Model')->getRowSe($parm);
            $v['service_info']=$service_info;
            $result[]=$v;
        }
        if(empty($result))
            return array();
        return $result;

  }
}
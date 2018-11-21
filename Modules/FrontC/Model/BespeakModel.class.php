<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/20
 * Time: 15:15
 */

namespace FrontC\Model;


class BespeakModel extends FrontBaseModel
{

    /**
     * [获取预约记录调用、]
     * */
    public function getRows($param=array())
    {
        $bespeaks=$this->where($param)->field('id bespeak_id,service_id,shop_id,start_time,end_time')->select();
        foreach ($bespeaks as $k=>$v){
            $service_info=M('Service')->field('service_name,service_short_desc,cover')->find($v['service_id']);
            $cover_info=M('File')->field('abs_url cover')->find($service_info['cover']);
            $shop_name=M('Shop')->where(array('id'=>$v['shop_id']))->getField('name');
            $v['service_name']=$service_info['service_name'];
            $v['service_short_desc']=$service_info['service_short_desc'];
            $v['cover']=$cover_info['cover'];
            //时间处理
            $v['start_time']=date('Y-m-d H:i',$v['start_time']);
            $v['end_time']=date('-H:i',$v['end_time']);
            $v['time']=$v['start_time'].$v['end_time'];
            $v['shop_name']=$shop_name;
            $result[]=$v;
        }
        $Page = new \Think\Page(count($result), 9, $_REQUEST);
        $result_final = array_slice($result,$Page->firstRow,$Page->listRows);
        if(!empty($result_final)){
            return $result_final;
        }else{
            return array();
        }
  }
}
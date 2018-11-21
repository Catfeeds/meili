<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/10
 * Time: 17:26
 */

namespace FrontC\Model;


class ServiceModel extends FrontBaseModel
{
    /**
     * [service获取指定分类商品调用]
     * */
    public function getSer($param=array())
    {
        $service = $this->alias('service')
            ->field('service.id service_id,service.service_name,service.price,service.market_price,service.service_cate_id,service.sales,service.service_short_desc,file.abs_url cover')
            ->where($param)
            ->order(array('service.id desc'))
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = service.cover',
            ))
            ->select();
        if(empty($service))
            return array();
        return $service;
    }


    /**
     * [service获取服务商品详情调用]
     * */
    public function getService($param=array())
    {
        $goods = $this->alias('service')
            ->field('service.id service_id,service.service_name,service.service_short_desc,service.price,service.market_price,service.sales,service.service_desc,file.abs_url cover')
            ->where($param)
            ->order(array('service.id desc'))
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = service.cover',
            ))
            ->select();
        $result = $goods[0];
        $result['service_desc']=path2abs($result['service_desc']);
        if(empty($result)){
            return array();
        }else{
            return $result;
        }
   }
    public function getRow($parm=array())
    {
        $service = $this->alias('service')
            ->field('service.id service_id,service.service_name,service.service_short_desc,service.price,service.market_price,service.sales,service.service_desc,file.abs_url cover')
            ->where($parm)
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = service.cover',
            ))
            ->find();
        $service['service_desc']=path2abs($service['service_desc']);
        if(empty($service)){
            return array();
        }else{
            return $service;
        }
    }

    /**
     * [服务商品确认订单调用、团购提交订单调用、我的团购列表调用、]
     * */
    public function getRowSe($parm=array())
    {
        $service = $this->alias('service')
            ->field('service.id,service.service_name name,service.service_short_desc short_desc,service.price,service.market_price,service.sales,file.abs_url cover')
            ->where($parm)
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = service.cover',
            ))
            ->find();
//        $service['service_desc']=path2abs($service['service_desc']);
        if(empty($service)){
            return array();
        }else{
            return $service;
        }
    }


    public function getRowSeS($parm=array())
    {
        $service = $this->alias('service')
            ->field('service.service_name name,service.service_desc,service.service_short_desc short_desc,service.price,service.market_price,service.sales,file.abs_url cover')
            ->where($parm)
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = service.cover',
            ))
            ->find();
//        $service['service_desc']=path2abs($service['service_desc']);
        if(empty($service)){
            return array();
        }else{
            return $service;
        }
    }

    /**
     * [首页搜索调用、]
     */
    function getListSearch($param = array()) {
        $service = $this->alias('service')
            ->field('service.id,service.service_name name,service.price,service.market_price,service.sales,service.service_short_desc short_desc,file.abs_url cover')
            ->where($param)
            ->order(array('service.id desc'))
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = service.cover',
            ))
            ->select();
        //返回数据
        if(empty($service))
            return array();
        foreach ($service as $k=>$v){
            $v['flag']='1';
            $result[]=$v;
        }
        return $result;
    }
}
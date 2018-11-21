<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/4
 * Time: 16:51
 */

namespace FrontC\Logic;


class ServiceLogic extends FrontBaseLogic
{

    /**
     * [service获取分类商品调用]
     * 获取指定一级分类及分类下的服务商品
     * */
    public function getCateService($request = array())
    {
        if (empty($request['cate_id'])) {
            $this->setLogicInfo('参数错误！');
            return false;
        }
        //获取该类所有子分类和第一个分类下的商品
        $cates = M('ServiceCategory')->where(array('parent_id' => $request['cate_id']))->field('id category_id,name')->select();
        if (isset($request['category_id'])) {
            $param = array('service.status' => 1, 'service_cate_id' => $request['category_id']);
        } else {
            $param = array('service.status' => 1, 'service_cate_id' => $cates[0]['category_id']);
        }
        $service =D('FrontC/Service','Model')->getSer($param);
        $result = array(
            'cates' => $cates,
            'service' => $service
        );
        return $result;
    }

    /**
     * 商品详情
     * */
    public function serviceDetail($request = array())
    {
        if (empty($request['service_id'])) {
            $this->setLogicInfo('服务商品不存在！');
            return false;
        }
        $param = array('service.id' => $request['service_id']);
        $result = D('FrontC/Service', 'Model')->getService($param);
        if (empty($result))
            return $this->setLogicInfo('服务商品不存在！', false);
        $result['service_desc'] = path2abs($result['service_desc']);
        return $result;
    }

    /**
     * 套餐详情
     * */
    public function packageDetail($request = array())
    {
        if (empty($request['package_id'])) {
            $this->setLogicInfo('套餐不存在！');
            return false;
        }
        $parm = array('package.id' => $request['package_id'],'package.status'=>1);
        $result = D('FrontC/Package', 'Service')->getRow($parm);
        if (empty($result))
            return $this->setLogicInfo('套餐不存在！', false);
        $result['package_desc'] = path2abs($result['package_desc']);
        return $result;
    }

    /**
     * 单张一卡通详情
     * */
    public function cardDetail($request = array())
    {
        //判断参数
        if (empty($request['card_id'])) {
            $this->setLogicInfo('该卡不存在！');
            return false;
        }
        $parm = array('id' => $request['card_id']);
        $card = D('FrontC/Card', 'Model')->getCard($parm);
        if (empty($card))
            return $this->setLogicInfo('该卡不存在！', false);
        //获取该卡下的服务商品
        //获取套餐服务表中的服务商品ID 根据ID查询服务商品信息
        $arr_ids = D('FrontC/Card', 'Model')->getSer(array('card_id' => $request['card_id']));
        $service = array();
        foreach ($arr_ids as $k => $v) {
            $ser = M('Service')->field('id service_id,service_name,price,count')->find($v);
            $service[] = $ser;
        }
        $card['service']=$service;
        return $card;
    }

    /**
     * 获取开通实体店的城市
     * */
    public function getCity($request = array())
    {
        //判断参数
        if (empty($request['m_id'])) {
            $this->setLogicInfo('请登陆！');
            return false;
        }
        //循环获取开通点的省市区
        $position_province=M('ShopRegion')->where(array('region_type'=>1))->field('id province_id,all_name province_name')->select();
        foreach ($position_province as $k1=>&$v1){
            $position_city=M('ShopRegion')->where(array('region_type'=>2,'parent_id'=>$v1['province_id']))->field('id city_id,all_name city_name')->select();
            $v1['city']=$position_city;
            foreach ($v1['city'] as $k2=>&$v2){
                $position_area=M('ShopRegion')->where(array('region_type'=>3,'parent_id'=>$v2['city_id']))->field('id area_id,all_name area_name')->select();
                $v2['area']=$position_area;
            }
        }
        if(!empty($position_province)){
            return $position_province;
        }else{
            return array();
        }
    }

    /**
     * 获取指定位置下的多个店铺
     * */
    public function getShop($request = array())
    {
        if (empty($request['area_id'])) {
            $this->setLogicInfo('参数错误哦！');
            return false;
        }
        $parm = array('shop.area_id' => $request['area_id']);
        $shop = D('FrontC/Shop', 'Model')->getShop($parm);
        if(empty($request['lng']) || empty($request['lat'])){
            foreach ($shop as $k=>$v){
                $v['distance']='暂无法获取距离';
                $shops[]=$v;
            }
        }else{
            $max_distance=C('MAX_DISTANCE');
            if(empty($max_distance)){
                $max_distance=3;
            }
            $distanceArr=$this->calculateDistance($request['lng'],$request['lat'],$request['area_id']);
            foreach ($shop as $k=>$v){
                if($distanceArr[0]['distance_address'] > $max_distance){
                    $v['distance']='>'.$max_distance.'km';
                }else{
                    $v['distance']=$distanceArr[0]['distance_address'].'km';
                }
                $shops[]=$v;
            }
        }
        if (empty($shops)){
            return $this->setLogicInfo('该地区暂未开通店铺哦！', false);
        }else{
            return $shops;
        }
    }
    /**
     *利用四个经纬度计算两个地址距离
     */
    function calculateDistance($request_lng = 0,$request_lat = 0,$request_area_id){
        $data ="ROUND((6371 * ACOS(COS(RADIANS(lat)) * COS(RADIANS($request_lat)) * COS(RADIANS($request_lng) - RADIANS(lng)) + SIN(RADIANS(lat)) * SIN(RADIANS($request_lat)))),1)";
        $station_result = M("Region")->where(array('status'=>1,'id'=>$request_area_id))->field("id,lng,lat,$data AS distance_address")->select();
        $in_area = [];
        foreach ($station_result as &$value){
            if($value['distance']<=C("FAREST_SEND_DISTANCE")){
                $in_area[]=$value;
            }
        }
        return $in_area;
    }
}
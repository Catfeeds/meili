<?php

namespace FrontC\Service;

/**
 * Class AddressService
 * @package FrontC\Service
 * 用户地址服务层
 */
class AddressService extends FrontBaseService
{

    /**
     * [center获取单条地址调用]
     * @param $custom_param
     * @return array
     */
    function getList($custom_param)
    {
        //排序
        $param['order'] = 'id DESC';
        //字段
        $param['field'] = 'id adr_id,contacts,mobile,province_id,province_name,city_id,city_name,area_id,area_name,address,is_default,ress';
        //是否有外部其他自定义条件  如果有替换条件
        if (!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/Address')->getList($param);
        //如果没有数据返回空数组
        if (empty($result))
            return array();

        //处理列表数据
        array_walk($result, 'FrontC\Service\AddressService::dataFactory', $extra);

        return $result;
    }

    /**
     * @param $value
     * @param $key
     * @param $extra
     * 数据加工
     */
    public static function dataFactory(&$value, $key, $extra)
    {
//        //性别名称
//        $value['gender_name']   = D('FrontC/Address', 'Service')->getGenderName($value['gender']);
//        if($extra['get'] != 'one') {
//            $value['address']   = D('FrontC/Address', 'Service')->getAddress($value); //地址
//        }
    }

    /**
     * @param $address
     * @return string
     * 获取详细地址  地址拼接
     */
    public function getAddress($address)
    {
        return $address['province_name'] . $address['city_name'] . $address['area_name'] . $address['address'];
    }
}
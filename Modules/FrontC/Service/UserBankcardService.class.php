<?php
namespace FrontC\Service;

/**
 * Class UserBankcardService
 * @package FrontC\Service
 * 用户银行卡信息服务层
 */
class UserBankcardService extends FrontBaseService {

    /**
     * @param $custom_param
     * @return array
     */
    function getList($custom_param) {
        //排序
        $param['order'] = 'id DESC';
        //字段
        //$param['field'] = 'id u_card_id,open_name,card_number,mobile,bank_name,bank_short,bank_logo';
        $param['field'] = 'id u_card_id,card_number,bank_name,bank_logo';
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/UserBankcard')->getList($param);
        //如果没有数据返回空数组
        if(empty($result))
            return array();

        //处理列表数据
        array_walk($result, 'FrontC\Service\UserBankcardService::dataFactory', $extra);

        return $result;
    }

    /**
     * @param $value
     * @param $key
     * @param $extra
     * 数据加工
     */
    public static function dataFactory(&$value, $key, $extra) {
        //处理卡号
        $value['card_number'] = format_card_number($value['card_number'], 2) . '储蓄卡';
    }
}
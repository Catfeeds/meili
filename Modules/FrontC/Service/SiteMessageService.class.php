<?php
namespace FrontC\Service;

/**
 * Class SiteMessageService
 * @package FrontC\Service
 * 站内消息服务层
 */
class SiteMessageService extends FrontBaseService {

    /**
     * @param array $custom_param
     * @return array
     * 消息列表
     */
    function messages($custom_param = array()) {
        //每页数量
        $param['page_size'] = 10;
        //排序
        $param['order']     = 'site_msg.id DESC';
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/SiteMessage')->getList($param);
        if(empty($result['list']))
            return array();
        //处理数据
        foreach($result['list'] as &$value) {
            //时间处理
            $value['create_time'] = fuzzy_date($value['create_time']);
        }
        return $result;
    }
}
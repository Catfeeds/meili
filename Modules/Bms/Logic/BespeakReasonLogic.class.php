<?php
namespace Bms\Logic;

/**
 * Class RefundReasonLogic
 * @package Bms\Logic
 * 退款原因 逻辑层
 */
class BespeakReasonLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'create_time DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //返回数据
        return D('BespeakReason')->getList($param);
    }

}
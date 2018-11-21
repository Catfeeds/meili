<?php
namespace ShopBms\Logic;

/**
 * Class RefundReasonLogic
 * @package Bms\Logic
 * 退款原因 逻辑层
 */
class RefundReasonLogic extends BmsBaseLogic {

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
        return D('RefundReason')->getList($param);
    }

    /**
     * @param $result
     * @param array $request
     * @return boolean
     * 更新后执行
     */
    protected function afterUpdate($result = 0, $request = array()) {
        //更新缓存
        S('RefundReason_Cache', M('RefundReason')->field('reason')->select());
        return true;
    }
}
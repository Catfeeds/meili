<?php
namespace Bms\Logic;

/**
 * Class IntegralLogLogic
 * @package Bms\Logic
 * 积分记录 逻辑层
 */
class IntegralLogLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        $param['where'] = array();
        //ID查找
        if(!empty($request['m_id'])) {
            $param['where']['itg_log.m_id']  = $request['m_id'];
        }
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['itg_log.m_id']  = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //动向
        if(!empty($request['trend'])) {
            $param['where']['itg_log.trend']  = $request['trend'];
        }
        //时间段查询
        if(!empty($request['start_time']) && !empty($request['end_time'])) {
            $param['where']['itg_log.create_time'] = array('between', strtotime($request['start_time']) . "," . strtotime($request['end_time'] . '23:59'));
        }
        //排序条件
        $param['order'] = 'itg_log.id DESC';
        //返回数据
        $result = D('IntegralLog')->getList($param);

        foreach($result['list'] as &$value) {
            $value['symbol_name'] = D('FrontC/Finance', 'Service')->symbol2str($value['symbol']);
            $value['trend_name']  = D('FrontC/Finance', 'Service')->trend2str($value['trend']);
        }

        //统计信息
        $get_total = M('IntegralLog')->where(array('symbol'=>1))->sum('number');
        $spend_total = M('IntegralLog')->where(array('symbol'=>2))->sum('number');
        $now_get_total = M('IntegralLog')->alias('itg_log')->where(array_merge(array('itg_log.symbol'=>1),$param['where']))->sum('number');
        $now_spend_total = M('IntegralLog')->alias('itg_log')->where(array_merge(array('itg_log.symbol'=>2),$param['where']))->sum('number');
        $result['get_total'] = $get_total;
        $result['spend_total'] = $spend_total;
        $result['now_get_total'] = $now_get_total;
        $result['now_spend_total'] = $now_spend_total;
        return $result;
    }
}
<?php
namespace Bms\Logic;

/**
 * Class CashTurnoverLogic
 * @package Bms\Logic
 * 第三方支付记录 逻辑层
 */
class CashTurnoverLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        $param['where'] = array();
        //ID查找
        if(!empty($request['m_id'])) {
            $param['where']['cash.m_id']  = $request['m_id'];
        }
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['cash.m_id']  = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //支付方式
        if(!empty($request['payment'])) {
            $param['where']['cash.payment']  = $request['payment'];
        }
        //金额动向
        if(!empty($request['trend'])) {
            $param['where']['cash.trend']  = array('IN', $request['trend']);
        }
        //状态
        if(empty($request['status'])) {
            $param['where']['cash.status']  = 0;
        } else {
            $param['where']['cash.status']  = 1;
        }
        //时间段查询
        if(!empty($request['start_time']) && !empty($request['end_time'])) {
            $param['where']['cash.create_time'] = array('between', strtotime($request['start_time']) . "," . strtotime($request['end_time'] . '23:59'));
        }
        //排序条件
        $param['order'] = 'cash.id DESC';
        //返回数据
        $result = D('CashTurnover')->getList($param);

        foreach($result['list'] as &$value) {
            $value['trend_name']  = $this->trend2str($value['trend']);
        }

        //统计信息
        $total_1 = M('CashTurnover')->where(array('status'=>0))->sum('amounts');
        $total_2 = M('CashTurnover')->where(array('status'=>1,'trend'=>array('IN', '1,2,3')))->sum('amounts');
        $total_3 = M('CashTurnover')->where(array('status'=>1,'trend'=>10))->sum('amounts');
        $now_total_1 = M('CashTurnover')->alias('cash')->where(array_merge(array('cash.status'=>0),$param['where']))->sum('amounts');
        $now_total_2 = M('CashTurnover')->alias('cash')->where(array_merge(array('cash.status'=>1,'trend'=>array('IN', '1,2,3')),$param['where']))->sum('amounts');
        $now_total_3 = M('CashTurnover')->alias('cash')->where(array_merge(array('cash.status'=>1,'trend'=>10),$param['where']))->sum('amounts');
        $result['total_1'] = $total_1;
        $result['total_2'] = $total_2;
        $result['total_3'] = $total_3;
        $result['now_total_1'] = $now_total_1;
        $result['now_total_2'] = $now_total_2;
        $result['now_total_3'] = $now_total_3;
        return $result;
    }

    function trend2str($trend) {
        switch($trend) {
            case 1: return '购买商品'; break;
            case 2: return '直接充值'; break;
            case 3: return '购买充值卡'; break;
            case 10: return '退款'; break;
            default: return ''; break;
        }
    }
}
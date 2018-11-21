<?php
namespace Bms\Logic;

/**
 * Class BalanceTurnoverLogic
 * @package Bms\Logic
 * 用户余额变动逻辑层
 */
class BalanceTurnoverLogic extends BmsBaseLogic {	

	/**
	 * @param array $request
	 * @return array
	 * 获取用户余额变动记录
	 */
	public function getList($request = array()){
        $param['where'] = array();
        //ID查找
        if(!empty($request['m_id'])) {
            $param['where']['bal_t.user_id']  = $request['m_id'];
        }
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['bal_t.user_id']  = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //金额动向
        if(!empty($request['trend'])) {
            $param['where']['bal_t.trend']  = array('IN', $request['trend']);
        }
        //收支状况
        if(!empty($request['symbol'])) {
            $param['where']['bal_t.symbol']  = $request['symbol'];
        }
        //状态
        if(empty($request['status'])) {
            $param['where']['bal_t.status']  = 0;
        } else {
            $param['where']['bal_t.status']  = 1;
        }
        //时间段查询
        if(!empty($request['start_time']) && !empty($request['end_time'])) {
            $param['where']['bal_t.create_time'] = array('between', strtotime($request['start_time']) . "," . strtotime($request['end_time'] . '23:59'));
        }
        //排序条件
        $param['order'] = 'bal_t.id DESC';
        //返回数据
        $result = D('BalanceTurnover')->getList($param);

        foreach($result['list'] as &$value) {
            //动向
            $value['trend_name']  = $this->trend2str($value['trend']);
            //收支符号
            $value['amounts_format']  = $this->amountsFormat($value['symbol'], $value['amounts']);
        }

        //统计信息
        $total_1 = M('BalanceTurnover')->where(array('status'=>1,'trend'=>array('IN', '2,3')))->sum('amounts');
        $total_2 = M('BalanceTurnover')->where(array('status'=>1,'trend'=>array('IN', '4')))->sum('amounts');
        $total_3 = M('BalanceTurnover')->where(array('status'=>1,'trend'=>array('IN', '5')))->sum('amounts');
        $total_4 = M('BalanceTurnover')->where(array('status'=>1,'trend'=>array('IN', '1')))->sum('amounts');
        $now_total_1 = M('BalanceTurnover')->alias('bal_t')->where(array_merge(array('bal_t.status'=>1,'trend'=>array('IN', '2,3')),$param['where']))->sum('amounts');
        $now_total_2 = M('BalanceTurnover')->alias('bal_t')->where(array_merge(array('bal_t.status'=>1,'trend'=>array('IN', '4')),$param['where']))->sum('amounts');
        $now_total_3 = M('BalanceTurnover')->alias('bal_t')->where(array_merge(array('bal_t.status'=>1,'trend'=>array('IN', '5')),$param['where']))->sum('amounts');
        $now_total_4 = M('BalanceTurnover')->alias('bal_t')->where(array_merge(array('bal_t.status'=>1,'trend'=>array('IN', '1')),$param['where']))->sum('amounts');
        $result['total_1'] = $total_1;
        $result['total_2'] = $total_2;
        $result['total_3'] = $total_3;
        $result['total_4'] = $total_4;
        $result['now_total_1'] = $now_total_1;
        $result['now_total_2'] = $now_total_2;
        $result['now_total_3'] = $now_total_3;
        $result['now_total_4'] = $now_total_4;
        return $result;
	}

    function trend2str($trend) {
        switch($trend) {
            case 1: return '余额支付订单'; break;
            case 2: return '在线充值'; break;
            case 3: return '充值卡充值'; break;
            case 4: return '邀请收益'; break;
            case 5: return '提现'; break;
            case 9: return '余额退款'; break;
            case 10: return '后台调整'; break;
            default: return ''; break;
        }
    }

    /**
     * @param int $symbol
     * @param $amounts
     * @return string
     */
    function amountsFormat($symbol = 0, $amounts) {
        switch($symbol) {
            case 1: return '<span class="green-1">+￥ '.$amounts.' 元</span>'; break;
            case 2: return '<span class="red-1">-￥ '.$amounts.' 元</span>'; break;
            default: return ''; break;
        }
    }
}
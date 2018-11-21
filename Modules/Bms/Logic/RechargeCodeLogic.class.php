<?php
namespace Bms\Logic;

/**
 * Class RechargeCodeLogic
 * @package Bms\Logic
 * 充值码逻辑层
 */
class RechargeCodeLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        $param['where'] = array();
        //用户ID查找
        if(!empty($request['rec_card_id'])) {
            $param['where']['rec_code.rec_card_id']  = $request['rec_card_id'];
        }
        //用户ID查找
        if(!empty($request['m_id'])) {
            $param['where']['rec_code.user_id']  = $request['m_id'];
        }
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['rec_code.user_id']  = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //状态
        if($request['status'] != '') {
            $param['where']['rec_code.status']  = $request['status'];
        }
        //充值时间段查询
        if(!empty($request['start_time']) && !empty($request['end_time'])) {
            $param['where']['rec_code.recharge_time'] = array('between', strtotime($request['start_time']) . "," . strtotime($request['end_time'] . '23:59'));
        }
        //排序条件
        $param['order'] = 'rec_code.id DESC';
        //返回数据
        $result = D('RechargeCode')->getList($param);

        //foreach($result['list'] as &$value) {}

        //统计信息
        $total_1 = M('RechargeCode')->where(array('user_id'=>0))->sum('face_value');
        $total_2 = M('RechargeCode')->where(array('user_id'=>array('exp','>0')))->sum('face_value');
        $now_total_1 = M('RechargeCode')->alias('rec_code')->where(array_merge(array('rec_code.status'=>1,'user_id'=>0),$param['where']))->sum('face_value');
        $now_total_2 = M('RechargeCode')->alias('rec_code')->where(array_merge(array('rec_code.status'=>0,'user_id'=>0),$param['where']))->sum('face_value');
        $now_total_3 = M('RechargeCode')->alias('rec_code')->where(array_merge(array('rec_code.status'=>1,'user_id'=>array('exp','>0')),$param['where']))->sum('face_value');
        $now_total_4 = M('RechargeCode')->alias('rec_code')->where(array_merge(array('rec_code.status'=>0,'user_id'=>array('exp','>0')),$param['where']))->sum('face_value');
        $result['total_1'] = $total_1;
        $result['total_2'] = $total_2;
        $result['now_total_1'] = $now_total_1;
        $result['now_total_2'] = $now_total_2;
        $result['now_total_3'] = $now_total_3;
        $result['now_total_4'] = $now_total_4;

        return $result;
    }
}
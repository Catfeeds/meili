<?php
namespace Bms\Logic;

/**
 * Class MemberCouponLogic
 * @package Bms\Logic
 * 用户优惠券 逻辑层
 */
class MemberCouponLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        $param['where'] = array();
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['m_cpn.m_id']  = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //动向
        if($request['status'] != '') {
            $param['where']['m_cpn.status']  = $request['status'];
        }
        //时间段查询
        if(!empty($request['start_time']) && !empty($request['end_time'])) {
            $param['where']['m_cpn.create_time'] = array('between', strtotime($request['start_time']) . "," . strtotime($request['end_time'] . '23:59'));
        }
        //排序条件
        $param['order'] = 'm_cpn.id DESC';
        //返回数据
        $result = D('MemberCoupon')->getList($param);

        //统计信息
        $total_1 = M('MemberCoupon')->where(array('status'=>0))->sum('face_value');
        $total_2 = M('MemberCoupon')->where(array('status'=>1))->sum('face_value');
        $now_total_1 = M('MemberCoupon')->alias('m_cpn')->where(array_merge(array('m_cpn.status'=>0),$param['where']))->sum('face_value');
        $now_total_2 = M('MemberCoupon')->alias('m_cpn')->where(array_merge(array('m_cpn.status'=>1),$param['where']))->sum('face_value');
        $result['total_1'] = $total_1;
        $result['total_2'] = $total_2;
        $result['now_total_1'] = $now_total_1;
        $result['now_total_2'] = $now_total_2;
        return $result;
    }
}
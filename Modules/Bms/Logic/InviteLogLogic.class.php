<?php
namespace Bms\Logic;

/**
 * Class InviteLogLogic
 * @package Bms\Logic
 */
class InviteLogLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取日志列表
     */
    function getList($request = array()) {
        if(!empty($request['account'])) {
            $param['where']['invite.user_id'] = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        if(!empty($request['m_id'])) {
            $param['where']['invite.user_id'] = $request['m_id'];
        }
        //排序条件
        $param['order'] = 'invite.id DESC';
        //返回数据
        return D('InviteLog')->getList($param);
    }
}
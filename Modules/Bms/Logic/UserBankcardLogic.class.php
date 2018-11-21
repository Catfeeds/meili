<?php
namespace Bms\Logic;

/**
 * Class UserBankcardLogic
 * @package Bms\Logic
 * 用户银行卡
 */
class UserBankcardLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //用户ID 查找
        if(!empty($request['m_id'])) {
            $param['where']['u_card.user_id']  = $request['m_id'];
        }
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['u_card.user_id']  = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //排序
        $param['order'] = 'u_card.id DESC';
        //返回数据
        return D('UserBankcard')->getList($param);
    }
}
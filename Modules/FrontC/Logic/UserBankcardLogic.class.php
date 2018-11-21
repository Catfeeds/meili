<?php

namespace FrontC\Logic;

/**
 * Class UserBankcardLogic
 * @package FrontC\Logic
 * 用户银行卡信息逻辑层
 * 黑暗中的武者
 */
class UserBankcardLogic extends FrontBaseLogic
{

    /**
     * [center我的银行卡调用]
     * @param array $request
     * @return bool
     * 黑暗中的武者
     */
    function getList($request = array())
    {
        //用户信息
        $param['where']['user_id'] = $request['m_id']; //用户ID
        //是否只返回默认地址
        if (!empty($request['default']))
            $param['where']['is_default'] = 1; //返回默认地址
        //获取列表  返回数据
        return D('FrontC/UserBankcard', 'Service')->getList($param);
    }

    /**
     * [center我的银行卡详情调用]
     * @param array $request
     * @return bool
     * 黑暗中的武者
     */
    function getRow($request = array())
    {
        //判断地址主键ID参数
        if (empty($request['u_card_id'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        $param['where']['id'] = $request['u_card_id'];
        //地址信息
        $list = D('FrontC/UserBankcard', 'Service')->getList($param);
        //是否查到信息
        if (empty($list[0]))
            return $this->setLogicInfo('未查到信息！', false);
        return $list[0];
    }

    /**
     * @param array $request
     * @return boolean
     * 更新前执行
     * 黑暗中的武者
     */
    protected function beforeUpdate($request = array())
    {
        //如果设置当前地址为默认地址的话  先设置已有默认地址为非默认
        //if($request['is_default'] == 1)
        //    M('Address')->where(array('m_id'=>$request['m_id']))->setField('is_default',0);
        return true;
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     * 黑暗中的武者
     */
    protected function processData($data = array(), $request = array())
    {
        $data['user_id'] = $request['m_id'];
        return $data;
    }

    /**
     * @param array $request
     * @return boolean
     * 设为默认
     * 黑暗中的武者
     */
    function setDefault($request = array())
    {
        if (empty($request['u_card_id']))
            return $this->setLogicInfo('参数错误！', false);
        //默认置0
        if (M('UserBankcard')->where(array('user_id' => $request['m_id']))->setField('is_default', 0) === false) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        if (!M('UserBankcard')->where(array('user_id' => $request['m_id'], 'id' => $request['u_card_id']))->setField('is_default', 1)) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('设置成功！', true);
    }
}
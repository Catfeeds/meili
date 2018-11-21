<?php
namespace MsC\Logic;

/**
 * Class AdjustLogic
 * @package MsC\Logic
 * 调整用户资料逻辑层
 */
class AdjustLogic extends MscBaseLogic {

    /**
     * @param array $request
     * @return bool
     * 执行调整
     */
    function doAdjust($request = array()) {
        //隐藏参数判空
        if(empty($request['model']) || empty($request['ids'])) {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //判断必填参数是否填写
        if(empty($request['field'])) {
            $this->setLogicInfo('请选择要调整的资料！'); return false;
        } if(empty($request['adjust_value'])) {
            $this->setLogicInfo('请输入调整额度！'); return false;
        } if(empty($request['rule'])) {
            $this->setLogicInfo('请选择调整规则！'); return false;
        } if(empty($request['reason'])) {
            $this->setLogicInfo('请输入调整原因！'); return false;
        }
        //根据model 判断要修改的表
        switch($request['model']) {
            case 'Member'   : return $this->_adjustM($request); break;
        }
    }

    /**
     * @param array $param
     * @return bool
     * 普通用户调整
     */
    private function _adjustM($param = array()) {
        //判断用户是否存在该资料
        //获取用户资料
        $info = M('Member')->where(array('id'=>$param['ids']))->field($param['field'])->find();
        if(!$info) {
            $this->setLogicInfo('调整失败！-Code:1'); return false;
        }
        //修改用户积分 或者 余额
        if(!$this->_rule($param)) {
            $this->setLogicInfo('调整失败！-Code:2'); return false;
        }
        //获取订单信息
        $order = $this->_getOrder($param['order_sn']);
        //判断添加余额记录还是积分记录
        if($param['field'] == 'balance') {
            //添加余额记录
            D('FrontC/Finance', 'Service')->addBalanceTurnover($param['ids'],1,$param['rule'],10,$param['adjust_value'],$info['balance'],1,$order['order_id'],$order['order_sn'],$order['order_type'],$param['reason']);
        } elseif($param['field'] == 'integral') {
            //添加积分记录
            D('FrontC/Finance', 'Service')->addItgLog($param['ids'], $param['rule'], 6, $param['adjust_value'], $param['reason']);
        }
        //发送站内信

        return true;
    }

    /**
     * @param $param
     * @return bool
     * 根据调整规则修改用户资料
     */
    private function _rule($param) {
        //修改用户积分 或者 余额
        if($param['rule'] == 1)
            $result = M($param['model'])->where(array('id'=>$param['ids']))->setInc($param['field'], $param['adjust_value']);
        else
            $result = M($param['model'])->where(array('id'=>$param['ids']))->setDec($param['field'], $param['adjust_value']);
        //是否修改成功
        if(!$result) {
            return false;
        }
        return true;
    }

    /**
     * @param $order_sn
     * @return array|mixed
     * 获取订单信息
     */
    private function _getOrder($order_sn) {
        $order_tmp = array('order_id'=>0,'order_sn'=>'','order_type'=>0);
        if(empty($order_sn))
            return $order_tmp;
        $order = M('OrderInfo')->where(array('order_sn'=>$order_sn))->field('id order_id,order_sn')->find();
        if(!$order)
            return $order_tmp;
        return array_merge($order, array('order_type'=>1));
    }
}
<?php
namespace ShopBms\Logic;

/**
 * Class OrderInfoLogic
 * @package Bms\Logic
 * 订单管理 逻辑层
 */
class OrderInfoLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //订单号 查找
        if(!empty($request['order_sn'])) {
            $param['where']['oi.order_sn'] = array('LIKE', '%' . $request['order_sn'] . '%');
        }
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['oi.m_id']  = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //不同订单类型查询条件
        $param = $this->_getStatusWhereByType($request['type'], $param);
        //状态 查找
        if(!empty($request['status'])) {
            $param['where']['oi.status']  = array('IN', $request['status']);
        }
        //时间查找
        if(!empty($request['start_time'])) {
            $param['where']['m.create_time']  = array('between', strtotime($request['start_time']) . "," . strtotime($request['start_time'] . '23:59'));
        }
        $param['where']['oi.shop_id'] =session('admin.a_id');
        //排序
        $param['order'] = 'oi.id DESC';
        //自定义排序
        if(!empty($request['sort'])) {
            $param['order'] = str_replace(':',' ',$request['sort']);
        }
        //返回数据
        $result = D('OrderInfo')->getList($param);

        foreach($result['list'] as &$value) {
            //提醒发货
            //$value['urge'] = M('UrgeLog')->where(array('order_id'=>$value['id']))->count();
            //状态名称
            $value['status_name'] = D('FrontC/OrderInfo', 'Service')->getStatusName($value['status']);
        }

        return $result;
    }

    /**
     * @param int $type
     * @param array $param
     * @return array
     * 根据订单类型获取查询条件
     */
    private function _getStatusWhereByType($type = 1, $param = array()) {
        if($type == 1) {
            $param['where']['oi.status'] = array('IN', '1,2,3,4,5');
        } elseif($type == 2) {
            $param['where']['oi.status'] = array('IN', '7,8,9');
        } elseif($type == 3) {
            $param['where']['oi.status'] = array('IN', '10');
        }
        return $param;
    }


    /**
     * @param array $request
     * @return mixed
     * 详情
     */
    function findRow($request = array()) {
        //参数判断
        if(!empty($request['id'])) {
            $param['where']['oi.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //获取数据
        $row = D('OrderInfo')->findRow($param);

        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取物流信息
        if(!empty($row['logistics_number']))
            $row['logistics'] = D('FrontC/OrderInfo', 'Service')->getLogistics($row['logistics_number']);
        //商品清单
        $row['goods_list']    = D('FrontC/OrderInfo', 'Service')->getOrderGoods($row['id']);
        //状态
        $row['status_name']   = D('FrontC/OrderInfo', 'Service')->getStatusName($row['status']);
        //前状态名称
        if($row['before_status'] != 0)
            $row['before_status_name'] = D('FrontC/OrderInfo', 'Service')->getStatusName($row['before_status']);

        return $row;
    }

    /**
     * @param array $request
     * @return bool
     * 发货
     */
    function delivery($request = array()) {
        //参数判断
        if(empty($request['ids'])) {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //验证实时状态 是否可进行此操作
        if(M('OrderInfo')->where(array('id'=>array('IN',$request['ids']),'status'=>array('exp', '<>2')))->count()) {
            $this->setLogicInfo('当前状态不能进行此操作！'); return false;
        }
        $data['status']        = 3; //3 发货
        $data['delivery_time'] = NOW_TIME; //发货时间
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>array('IN',$request['ids'])))->data($data)->save()) {
            $this->setLogicInfo('系统繁忙，稍后重试！');return false;
        }
        $this->setLogicInfo('操作成功！'); return true;
    }

    /**
     * @param array $request
     * @return bool
     * 收货
     */
    function receiving($request = array()) {
        //参数判断
        if(empty($request['ids'])) {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('id'=>array('IN',$request['ids'])))->field('id order_id,order_sn,m_id,pay_amounts,status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(3,7), $order)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        $data['status']         = 4; //4 收货
        $data['receiving_time'] = NOW_TIME; //收货时间
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>array('IN',$request['ids'])))->data($data)->save()) {
            $this->setLogicInfo('系统繁忙，稍后重试！');return false;
        }
        //TODO 完成订单后续操作
        D('FrontC/OrderInfo', 'Service')->afterSignFor(0, $order);

        $this->setLogicInfo('操作成功！'); return true;
    }

    /**
     * @param array $request
     * @return bool
     * 取消
     */
    function cancel($request = array()) {
        //参数判断
        if(empty($request['ids'])) {
            $this->setLogicInfo('参数错误！'); return false;
        } if(empty($request['cancel_order_reason'])) {
            $this->setLogicInfo('请输入取消原因！'); return false;
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('id'=>$request['ids']))->field('m_id,order_sn,status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(1), $order)) {
            $this->setLogicInfo('当前状态不能进行此操作！'); return false;
        }
        $data['status']             = 10; //10 取消
        $data['cancel_order_time']  = NOW_TIME; //取消时间
        $data['who_cancel_order']   = AID; //谁取消的订单
        $data['cancel_order_reason'] = $request['cancel_order_reason']; //取消原因
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>array('IN',$request['ids'])))->data($data)->save()) {
            $this->setLogicInfo('系统繁忙，稍后重试！');return false;
        }
        //TODO-SEND 给用户发送站内信
        $param = array(
            'receiver'      => $order['m_id'],
            'unique_code'   => 'bms_cancel_order_notify',
            'replaces'      => array('order_sn'=>$order['order_sn'],'reason'=>$request['cancel_order_reason']),
        );
        api('Send/sendMsg',array($param));
        //TODO 更新订单商品库存
        D('FrontC/OrderInfo', 'Service')->updOrderGoodsStock($request['ids']);
        $this->setLogicInfo('操作成功！'); return true;
    }

    /**
     * @param array $request
     * @return bool
     * 取消申请退款相关操作
     * 黑暗中的武者
     */
    function cancelRefund($request = array()) {
        //判断参数空
        if(empty($request['ids'])) {
            $this->setLogicInfo('参数错误！');return false;
        } if(empty($request['cancel_refund_reason'])) {
            $this->setLogicInfo('请输入取消原因！'); return false;
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('id'=>$request['ids']))->field('m_id,order_sn,status,before_status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(7), $order)) {
            $this->setLogicInfo('当前状态不能进行此操作！'); return false;
        }
        $data['status']                 = $order['before_status']; //回归原状态
        $data['who_cancel_refund']      = AID; //谁取消的订单
        $data['cancel_refund_reason']   = $request['cancel_refund_reason']; //取消原因
        $data['cancel_refund_time']     = NOW_TIME; //取消退款时间
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>array('IN',$request['ids'])))->data($data)->save()) {
             $this->setLogicInfo('系统繁忙，取消失败！'); return false;
        }
        //TODO-SEND 发送站内信给用户
        $param = array(
            'receiver'      => $order['m_id'],
            'unique_code'   => 'bms_cancel_refund_notify',
            'replaces'      => array('order_sn'=>$order['order_sn'],'reason'=>$request['cancel_refund_reason']),
        );
        api('Send/sendMsg',array($param));

        $this->setLogicInfo('取消成功！'); return true;
    }

    /**
     * @param array $request
     * @return bool
     * 执行退款
     */
    function refund($request = array()) {
        if(empty($request['payment']) || empty($request['order_id'])) {
            $this->setLogicInfo('参数错误！'); return false;
        }
        if(empty($request['refund_amounts'])) {
            $this->setLogicInfo('请输入退款金额！'); return false;
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('id'=>$request['order_id']))->field('id order_id,m_id,order_sn,pay_amounts,status')->find();
        //判断退款金额 是否大于支付金额
        if($request['refund_amounts'] > $order['pay_amounts']) {
            $this->setLogicInfo('退款金额超限！'); return false;
        }
        //更新退款金额
        if(M('OrderInfo')->where(array('id'=>$request['order_id']))->data(array('refund_amounts'=>$request['refund_amounts']))->save() === false) {
            $this->setLogicInfo('操作失败！'); return false;
        }
        //添加第三方支付流水
        //如果还没有记录则添加  添加 用户第三方支付流水记录
        if($request['payment'] != 3 && !M('CashTurnover')->where(array('order_id'=>$request['order_id'], 'trend'=>10, 'payment'=>$request['payment']))->count())
            D('FrontC/Finance', 'Service')->addCashTurnover($order['m_id'],$request['order_id'],$request['payment'],10,$request['refund_amounts'],$order['order_sn']);
        //判断支付方式
        if($request['payment'] == 1) {
            api('Ali/refund', array($request['turnover_number'], $request['refund_amounts'], C('REFUND_ALI_NOTIFY_URL')));
        } elseif($request['payment'] == 2) {
            $result = api('WeChat/refund', array($request['turnover_number'], $request['refund_amounts'], $order['pay_amounts'], $request['module'], array(new \Bms\Logic\OrderInfoLogic(), 'wxRefundCallback')));
            if($result['status'] == 0) {
                $this->setLogicInfo($result['info']); return false;
            }
        } elseif($request['payment'] == 3) {
            $this->balanceRefund($order, $request['refund_amounts']);
        }
    }

    /**
     * @param array $order 订单信息
     * @param int $refund_fee  退款金额
     * @return bool
     * 余额退款
     */
    function balanceRefund($order = array(), $refund_fee = 0) {
        if($order['status'] != 7) {
            $this->setLogicInfo('该状态不能进行此操作！'); return false;
        }
        //获取用户余额
        $info = M('Member')->where(array('id'=>$order['m_id']))->field('account,balance')->find();
        //修改订单状态
        $data['status']             = 8;
        $data['finish_refund_time'] = NOW_TIME;
        $data['refund_amounts']     = $refund_fee;
        if(!M('OrderInfo')->where(array('id'=>$order['order_id']))->data($data)->save()) {
            $this->setLogicInfo('系统繁忙，稍后重试！'); return false;
        }
        //修改用户余额
        M('Member')->where(array('id'=>$order['m_id']))->setInc('balance', $refund_fee);
        //添加余额记录
        D('FrontC/Finance', 'Service')->addBalanceTurnover($order['m_id'],1,1,9,$refund_fee,$info['balance'],1,$order['order_id'],$order['order_sn'],1);
        $param = array(
            'receiver'      => $info['account'],
            'unique_code'   => 'refund_success_notify',
            'replaces'      => array('order_sn'=>$order['order_sn'],'date'=>date('Y年m月d日 H时i分')),
        );
        api('Send/sendMsg',array($param));
        return true;
    }

    /**
     * @param array $request
     * 支付宝退款回调
     */
    function aliRefundCallback($request = array()) {
        //转化退款详情  这里的退款金额 是 用户输入多少退款成功后就返回多少  与实际退款金额不同
        $details_array = explode('^', $request['result_details']);
        //获取支付信息
        $param = M('cash_turnover')->where(array('turnover_number'=>$details_array[0]))->field('payment,order_id,turnover_number')->find();   //本站订单号
        $this->_successRefundDo(array_merge($param, array('refund_amounts'=>$details_array[1])));
    }

    /**
     * @param array $request
     * 微信退款回调
     */
    function wxRefundCallback($request = array()) {
        //获取支付信息
        $param = M('cash_turnover')->where(array('turnover_number'=>$request['transaction_id']))->field('payment,order_id,turnover_number')->find();   //本站订单号
        $this->_successRefundDo(array_merge($param, array('refund_amounts'=>$request['refund_fee'] / 100)));
    }

    /**
     * @param $param
     * 退款成功后操作
     */
    private function _successRefundDo($param) {
        M('Feedback')->data(array('content'=>json_encode($param)))->add();
        //获取订单信息
        $order = M('OrderInfo')->where(array('id'=>$param['order_id']))->field('m_id,order_sn,status')->find();
        //根据订单状态 判断业务逻辑是否已经执行过  执行过则不再进行处理 
        if($order['status'] != 7)
            return false;
        //修改订单状态
        $data = array(
            'status' => 8,
            'refund_amounts' => $param['refund_amounts'],
            'finish_refund_time' => NOW_TIME,
        );
        //修改订单相关信息
        M('OrderInfo')->where(array('id'=>$param['order_id']))->data($data)->save();
        //修改用户第三方支付流水记录status为1 成功支付  第三方流水号
        $ct_data['status']          = 1;
        $ct_data['amounts']         = $param['refund_amounts'];
        $ct_data['turnover_number'] = $param['turnover_number'];
        M('CashTurnover')->where(array('order_id'=>$param['order_id'], 'trend'=>10, 'payment'=>$param['payment']))->data($ct_data)->save();
        //TODO-SEND 给用户发送短信
        $account = M('Member')->where(array('id'=>$order['m_id']))->getField('account');
        $param = array(
            'receiver'      => $account,
            'unique_code'   => 'refund_success_notify',
            'replaces'      => array('order_sn'=>$order['order_sn'],'date'=>date('Y年m月d日 H时i分')),
        );
        api('Send/sendMsg',array($param));
    }
}
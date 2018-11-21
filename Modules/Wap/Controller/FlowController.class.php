<?php
namespace Wap\Controller;

/**
 * Class FlowController
 * @package Wap\Controller
 * 下单流程控制器
 */
class FlowController extends WapBaseController {

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //判断登陆
        $this->checkLogin();
    }

    /**
     * 确认订单接口
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) cart_ids(购物车ID串 ",") uer_itg(是否使用积分) m_cpn_id(用户优惠券ID)
     */
    function confirmOrder() {
        cookie('__forward__', U('' . CONTROLLER_NAME . '/' . ACTION_NAME . '', $_REQUEST));
        $result = D('FrontC/Flow', 'Logic')->confirmOrder(I('request.'));
        $this->assign('result', $result);
        $list = D('FrontC/Address', 'Logic')->getList(I('request.'));
        $this->assign('addresses', $list);
        $this->display('confirmOrder');
    }

    /**
     * 提交订单
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(用户地址ID) cart_ids(购物车ID串 ",") uer_itg(是否使用积分 0 1) m_cpn_id(用户优惠券ID) remark(备注)
     */
    function submitOrder() {
        $result = D('FrontC/Flow', 'Logic')->submitOrder(I('request.'));
        if ($result === false)
            $this->error(D('FrontC/Flow', 'Logic')->getLogicInfo());
        else
            $this->success('下单成功！', U('Flow/pay', array('order_id'=>$result['order_id'])));
    }

    /**
     * 进入支付页面
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     */
    function pay() {
        $result = D('FrontC/Pay', 'Logic')->pay(I('request.'));
        $this->assign('order', $result);
        $pay_param = D('FrontC/Pay', 'Logic')->doPay(array_merge(I('request.'), array('payment'=>2)));
        $this->assign('pay_param', $pay_param);
        $this->assign('forward', U('OrderInfo/myOrders', array('status'=>1)));
        $this->display('pay');
    }

	 function repairPay() {
        $result = D('FrontC/Pay', 'Logic')->pay(I('request.'));
        $this->assign('order', $result);
        $pay_param = D('FrontC/Pay', 'Logic')->doPay(array_merge(I('request.'), array('payment'=>2)));
        $this->assign('pay_param', $pay_param);
        $this->assign('forward', U('OrderInfo/myOrders', array('status'=>4)));
        $this->display('repairPay');
    }



    /**
     * 在线支付
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *payment(支付方式)
     */
    function doPay() {
        $result = D('FrontC/Pay', 'Logic')->doPay(I('request.'));
        if(!$result)
           $this->error(D('FrontC/Pay', 'Logic')->getLogicInfo());
        if($_REQUEST['payment'] == 4)
           $this->success('支付成功！', U('OrderInfo/myRepairOrders', array('status'=>'4')));
    }
}
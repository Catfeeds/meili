<?php
namespace Wap\Controller;

/**
 * Class OrderInfoController
 * @package Wap\Controller
 * 用户订单相关管理控制器
 */
class OrderInfoController extends WapBaseController {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //判断登陆
        $this->checkLogin();
    }

    /**
     * 订单列表页面
     */
    function myOrders() {
        $this->display('myOrders');
    }

	function myRepairOrders() {
        $this->display('myRepairOrders');
    }

    /**
     * 我的订单列表
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) status(订单状态 -1全部 1待支付 2待发货 3待收货 4待评价 5已评价 6 7申请退款中 8退款完成 9取消退款 10取消订单) *p(页号)
     * 黑暗中的武者
     */
    function getOrders() {
		if($_REQUEST['status'] == "")
			unset($_REQUEST['status']);
        $result = D('FrontC/OrderInfo', 'Logic')->orderList(I('request.'));
        if(empty($result['list']))
            $this->error('');
        $this->success('', '', true, $result['list']);
    }

    /**
     * 订单详情
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function orderDetail() {
        $result = D('FrontC/OrderInfo', 'Logic')->orderDetail(I('request.'));
        $this->assign('result', $result);
        $this->display('orderDetail');
    }

	  /**
     * 输入付款金额
     * 详细描述：
     * 特别注意：
     * POST参数： *order_id(订单ID)
     * baoyuan
     */
    function repairPrepay() {
		 if(!IS_POST) {
            $result = D('FrontC/OrderInfo', 'Logic')->orderDetail(I('request.'));
            $this->assign('result', $result);
            $this->display('repairPrepay');
		 }
		 else{
            $result = D('FrontC/OrderInfo', 'Logic')->prePay(I('request.'));
            if(!$result)
                $this->error(D('FrontC/OrderInfo','Logic')->getLogicInfo());
            $this->success(D('FrontC/OrderInfo','Logic')->getLogicInfo(),U('Flow/repairPay', array('order_id'=>$_REQUEST['order_id'])));
		 }
    }

    /**
     * 取消订单
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function cancelOrder() {
        $result = D('FrontC/OrderInfo', 'Logic')->cancelOrder(I('request.'));
        if(!$result)
           $this->error(D('FrontC/OrderInfo','Logic')->getLogicInfo());
        $this->success(D('FrontC/OrderInfo','Logic')->getLogicInfo());
    }

    /**
     * 提醒发货
     * 详细描述：
     * 特别注意：提醒发货间隔时间 后台设置
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function urge() {
        $result = D('FrontC/OrderInfo', 'Logic')->urge(I('request.'));
        if(!$result)
            $this->error(D('FrontC/OrderInfo','Logic')->getLogicInfo());
        $this->success(D('FrontC/OrderInfo','Logic')->getLogicInfo());
    }

    /**
     * 确认收货
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function signFor() {
        $result = D('FrontC/OrderInfo', 'Logic')->signFor(I('request.'));
        if(!$result)
            $this->error(D('FrontC/OrderInfo','Logic')->getLogicInfo());
        $this->success(D('FrontC/OrderInfo','Logic')->getLogicInfo());
    }

    /**
     * 评价
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *data(评价数据 json格式 '[{"goods_id":"","level":"","content":""},{"goods_id":"","level":"","content":""}]')
     *           file_(goods_id)_1、2、3图片
     * 黑暗中的武者
     */
    function comment() {
        if(!IS_POST) {
            //获取订单商品列表
            $this->assign('goods_list', D('FrontC/OrderInfo', 'Service')->getOrderGoods(I('request.order_id'), 1));
            $this->display('comment');
        } else {
            $result = D('FrontC/OrderInfo', 'Logic')->commentWap($_REQUEST);
            if(!$result)
                $this->error(D('FrontC/OrderInfo','Logic')->getLogicInfo());
            $this->success(D('FrontC/OrderInfo','Logic')->getLogicInfo(), U('OrderInfo/myOrders', array('status'=>4)));
        }
    }

	    /**
     * 评价
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *data(评价数据 json格式 '[{"id":"","level":"","content":""},{"id":"","level":"","content":""}]')
     *           file_(goods_id)_1、2、3图片
     * 黑暗中的武者
     */
    function masterComment() {
        if(!IS_POST) {
            //获取订单商品列表
			$order = D('FrontC/OrderInfo', 'Logic')->orderDetail(I('request.'));
            $this->assign('master', D('FrontC/OrderInfo', 'Service')->getOrderMaster($order['ms_id']));
            $this->display('masterComment');
        } else {
            $result = D('FrontC/OrderInfo', 'Logic')->masterCommentWap($_REQUEST);
            if(!$result)
                $this->error(D('FrontC/OrderInfo','Logic')->getLogicInfo());
            $this->success(D('FrontC/OrderInfo','Logic')->getLogicInfo(), U('OrderInfo/myRepairOrders', array('status'=>4)));
        }
    }

    /**
     * 上传文件
     */
    function upload() {
        $result = api('UpDownLoad/upload',array(I('request.')));
        $this->ajaxReturn(array_dimension($result) == 1 ? $result : $result['fileData']);
    }

    /**
     * 申请退款页面
     */
    function refund() {
        $list = D('FrontC/System','Service')->getReasons(array('type'=>1));
        $this->assign('list', $list);
        $this->display('refund');
    }

    /**
     * 申请退款
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *reason(退款原因)
     * 黑暗中的武者
     */
    function applyRefund() {
        $result = D('FrontC/OrderInfo', 'Logic')->applyRefund(I('request.'));
        if(!$result)
            $this->error(D('FrontC/OrderInfo','Logic')->getLogicInfo());
        $this->success(D('FrontC/OrderInfo','Logic')->getLogicInfo(), U('OrderInfo/orderDetail',array('order_id'=>I('request.order_id'))));
    }

    /**
     * 取消申请退款
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function cancelRefund() {
        $result = D('FrontC/OrderInfo', 'Logic')->cancelRefund(I('request.'));
        if(!$result)
            $this->error(D('FrontC/OrderInfo','Logic')->getLogicInfo());
        $this->success(D('FrontC/OrderInfo','Logic')->getLogicInfo());
    }
}
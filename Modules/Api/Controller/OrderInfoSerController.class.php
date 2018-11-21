<?php
namespace Api\Controller;

/**
 * Class OrderInfoController
 * @package Api\Controller
 * 用户订单相关管理控制器
 */
class OrderInfoSerController extends ApiBaseController {

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
     * 删除商城订单
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function delOrder() {
        $result = D('FrontC/OrderInfoSer', 'Logic')->delOrder(I('request.'));
        if(!$result)
            api_response('error', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
        api_response('success', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
    }

    /**
     * 我的订单列表
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) status(订单状态 -1全部 1待支付 2待发货 3待收货 4已完成 7申请退款中 8退款完成 10取消订单) *p(页号)
     * flag 0服务商品 1套餐 2一卡通
     * 黑暗中的武者
     */
    function myOrders() {
        $result = D('FrontC/OrderInfoSer', 'Logic')->orderList(I('request.'));
        if(!$result)
            api_response('error', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
        api_response('success', '', $result['list']);
    }

    /**
     * 订单详情
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function orderDetail() {
        $result = D('FrontC/OrderInfoSer', 'Logic')->orderDetail(I('request.'));
        if(!$result)
            api_response('error', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
        api_response('success', '', null2str($result));
    }

    /**
     * 获取物流状态
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *logistics_number(物流单号) 810097586321
     * 黑暗中的武者
     */
    function getLogistics() {
        $result = D('FrontC/OrderInfo', 'Service')->logistics(I('request.logistics_number'));
        if($result['status'] != 200)
            api_response('error', '未查到物流信息！');
        //TODO 是否派件中  拨打电话
        //获取最近的物流信息中是否有手机号   /\b(0|86)?1(([35][0-9])|(47)|[8][01236789])\d{8}\b/i
        preg_match_all('/\b(0|86)?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[037])\d{8}\b/i', $result['data'][0]['context'], $match);
        if(!empty($match[0][0]) && preg_match(C('MOBILE'), $match[0][0])) {
            $info['can_urge'] = '1';
            $info['mobile']   = ''.$match[0][0].'';
        } else {
            $info['can_urge'] = '0';
            $info['mobile']   = '0';
        }
        api_response('success', '', array('list'=>null2str($result['data']),'info'=>$info));
    }

    /**
     * 取消订单
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function cancelOrder() {
        $result = D('FrontC/OrderInfoSer', 'Logic')->cancelOrder(I('request.'));
        if(!$result)
            api_response('error', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
        api_response('success', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
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
            api_response('error', D('FrontC/OrderInfo','Logic')->getLogicInfo());
        api_response('success', D('FrontC/OrderInfo','Logic')->getLogicInfo());
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
            api_response('error', D('FrontC/OrderInfo','Logic')->getLogicInfo());
        api_response('success', D('FrontC/OrderInfo','Logic')->getLogicInfo());
    }

    /**
     * 订单评价
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *data(评价数据 json格式 '[{"goods_id":"","level":"","content":""},{"goods_id":"","level":"","content":""}]')
     *           file_(goods_id)_1、2、3图片
     * 黑暗中的武者
     */
    function comment() {
        $_REQUEST['save_path'] = 'Goods';
        $result = D('FrontC/OrderInfo', 'Logic')->comment(I('request.'));
        if(!$result)
            api_response('error', D('FrontC/OrderInfo','Logic')->getLogicInfo());
        api_response('success', D('FrontC/OrderInfo','Logic')->getLogicInfo());
    }

    /**
     * 申请退款
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *reason(退款原因)
     * 黑暗中的武者
     */
    function applyRefund() {
        $result = D('FrontC/OrderInfoSer', 'Logic')->applyRefund(I('request.'));
        if(!$result)
            api_response('error', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
        api_response('success', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
    }

    /**
     * 取消申请退款
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     * 黑暗中的武者
     */
    function cancelRefund() {
        $result = D('FrontC/OrderInfoSer', 'Logic')->cancelRefund(I('request.'));
        if(!$result)
            api_response('error', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
        api_response('success', D('FrontC/OrderInfoSer','Logic')->getLogicInfo());
    }
}
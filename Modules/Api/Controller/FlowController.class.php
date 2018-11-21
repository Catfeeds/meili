<?php

namespace Api\Controller;

/**
 * Class FlowController
 * @package Api\Controller
 * 下单流程控制器
 */
class FlowController extends ApiBaseController
{

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize()
    {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //判断登陆
        $this->checkLogin();
    }

    /**
     * 商城确认订单接口
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) cart_ids(购物车ID串 ",") uer_itg(是否使用积分) m_cpn_id(用户优惠券ID)
     */
    function confirmOrder()
    {
        $result = D('FrontC/Flow', 'Logic')->confirmOrder(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Flow', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 服务商品一卡通确认订单接口
     * 详细描述：
     * 特别注意：
     * POST参数：
     *  m_id(用户ID)
     * "package_id": "套餐ID",
     * "service_id": "服务商品ID",
     *
     * 注意：
     * 一卡通直接购买 没有确认订单 需要时传递以下参数
     * "card_id": "一卡通ID",type-1季卡 2年卡 shop_id-实体店ID
     */
    function confirmOrderSer()
    {
        $result = D('FrontC/Flow', 'Logic')->confirmOrderSer(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Flow', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 提交订单
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(用户地址ID) cart_ids(购物车ID串 ",") uer_itg(是否使用积分 0 1) m_cpn_id(用户优惠券ID) remark(备注)
     */
    function submitOrder()
    {
        $result = D('FrontC/Flow', 'Logic')->submitOrder(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Flow', 'Logic')->getLogicInfo());
        else
            api_response('success', '订单提交成功哦！', $result);
    }

    /**
     * 提交订单
     * 详细描述：
     * 特别注意：
     * POST参数
     *  m_id(用户ID)
     * "package_id": "套餐ID",
     * "service_id": "服务商品ID",
     * 注意：
     * 一卡通直接购买 没有确认订单 需要时传递以下参数
     * "card_id": "一卡通ID",type-1季卡 2年卡 shop_id-实体店ID
     */
    function submitOrderSer()
    {
        $result = D('FrontC/Flow', 'Logic')->submitOrderSer(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Flow', 'Logic')->getLogicInfo());
        else
            api_response('success', '订单提交成功哦！', $result);
    }

    /**
     * 进入支付页面
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     */
    function pay()
    {
        $result = D('FrontC/Pay', 'Logic')->pay(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Pay', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 在线支付
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *payment(支付方式)
     */
    function doPay()
    {
        $result = D('FrontC/Pay', 'Logic')->doPay(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Pay', 'Logic')->getLogicInfo());
        if ($_REQUEST['payment'] == 1)
            api_response('success', '', $result);
        elseif ($_REQUEST['payment'] == 2)
            print stripslashes(json_encode(array('flag' => 'success', 'message' => '', 'data' => $result)));
        else
            api_response('success', '支付成功！');
    }

    /**
     * 在线支付
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *payment(支付方式)
     */
    function doPaySer()
    {
        $result = D('FrontC/Pay', 'Logic')->doPaySer(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Pay', 'Logic')->getLogicInfo());
        if ($_REQUEST['payment'] == 1)
            api_response('success', '', $result);
        elseif ($_REQUEST['payment'] == 2)
            print stripslashes(json_encode(array('flag' => 'success', 'message' => '', 'data' => $result)));
        else
            api_response('success', '支付成功！');
    }




    /**
     * 服务商品APP同步回调
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     */
    function appCallbackSer()
    {
        $result = D('FrontC/Pay', 'Logic')->appCallbackSer(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Pay', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Pay', 'Logic')->getLogicInfo());
    }

    /**
     * APP同步回调
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     */
    function appCallback()
    {
        $result = D('FrontC/Pay', 'Logic')->appCallback(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Pay', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Pay', 'Logic')->getLogicInfo());
    }
}
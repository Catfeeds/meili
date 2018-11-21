<?php

namespace Api\Controller;


class ActivityGroupController extends ApiBaseController
{
    /**
     * 团购商品列表
     * post参数：无
     * */
    function goodsList()
    {
        $result = D('FrontC/ActivityGroup', 'Logic')->goodsList();
        if ($result === false)
            api_response('error', D('FrontC/ActivityGroup', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * [首页团购商品调用、]
     * 团购商品列表
     * post参数：无
     * */
    function goodsListC()
    {
        $result = D('FrontC/ActivityGroup', 'Logic')->goodsList();
        if(empty($result))
            return array();
        return $result;
    }

    /**
     * 开团详情
     * POST参数：group_service_id团购商品ID  m_id用户ID
     * */
    public function groupDetail()
    {
        $result = D('FrontC/ActivityGroup', 'Logic')->groupDetail(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/ActivityGroup', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 团购确认订单
     * POST参数：group_service_id 团购商品ID
     */
    function confirmGroup()
    {
        $result = D('FrontC/ActivityGroup', 'Logic')->confirmGroup(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/ActivityGroup', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 参/开团
     * POST：m_id用户ID  group_service_id团购商品ID  可选：group_list_id团购记录ID参团需要
     * */
    public function ojGroup()
    {
        $result = D('FrontC/ActivityGroup', 'Logic')->ojGroup(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/ActivityGroup', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 参/开团
     * POST：m_id用户ID  group_list_id团购记录ID
     * */
    public function delGroup()
    {
        $result = D('FrontC/ActivityGroup', 'Logic')->delGroup(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/ActivityGroup', 'Logic')->getLogicInfo());
        else
            api_response('success', '删除成功哦', $result);
    }

    /**
     * 我的单个团购详情
     * POST参数：group_list_id团购ID m_id
     * */
    public function detail()
    {
        $result = D('FrontC/ActivityGroup', 'Logic')->detail(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/ActivityGroup', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 我的团购列表
     * post参数：m_id   status不传查看所有 1成团中 2已经成团 3 团购失败
     * */
    public function myGroup()
    {
        $result = D('FrontC/ActivityGroup', 'Logic')->myGroup(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/ActivityGroup', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 团购在线支付
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID) *payment(支付方式)
     */
    function doPayGroup()
    {
        $result = D('FrontC/Pay', 'Logic')->doPayGroup(I('request.'));
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
     * 团购APP同步回调
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_id(订单ID)
     */
    function appCallbackGroup()
    {
        $result = D('FrontC/Pay', 'Logic')->appCallbackGroup(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Pay', 'Logic')->getLogicInfo());
        //获取该团购记录ID
        $group_info=M('OrderInfoSer')->Field('flag_id group_list_id')->find($_REQUEST['order_id']);
        api_response('success', '支付成功！', $group_info);
    }
}
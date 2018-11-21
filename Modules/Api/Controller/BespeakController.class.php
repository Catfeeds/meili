<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/18
 * Time: 11:16
 */

namespace Api\Controller;


class BespeakController extends ApiBaseController
{
    /**
     * 我的一卡通
     * POST参数：m_id用户ID
     * */
    public function myCard()
    {
        $result = D('FrontC/Bespeak', 'Logic')->myCard(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Bespeak', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }
    /**
     * 去预约
     * POST参数：m_id用户ID
     * card_id  不传表示品项预约 传值表示卡包预约
     * order_card_id 传card_id就必须传order_card_id
     * shop_id 购买之后就去预约需要传
     * */
    public function goBespeak()
    {
        $result = D('FrontC/Bespeak', 'Logic')->goBespeak(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Bespeak', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 确认预约
     * POST参数：m_id用户ID name预约人名字 mobile电话号 shop_id店铺ID time_id预约时间段ID  now_time预约年月日时间戳   service_id项目ID
     * order_service_id 品项预约时传递
     * card_id  不传表示品项预约 传值表示卡包预约
     * order_card_id 传card_id就必须传order_card_id
     * */
    public function confirmBespeak()
    {
        $result = D('FrontC/Bespeak', 'Logic')->confirmBespeak(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Bespeak', 'Logic')->getLogicInfo());
        else
            api_response('success', '预约成功哦！', $result);
    }

    /**
     * 获取预约时间
     * POST参数：m_id   now_time指定时间
     */
    public function getTimes()
    {
        $result = D('FrontC/Bespeak', 'Logic')->getTimes(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Bespeak', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 获取预约记录
     * POST参数：m_id  status 1已预约 2已服务 3已过期
     */
    public function getBespeak()
    {
        $result = D('FrontC/Bespeak', 'Logic')->getBespeak(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Bespeak', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 获取取消预约理由
     * POST参数：m_id  bespeak_id预约记录ID(可选)
     * */
    public function cancelBespeakReason()
    {
        $result = D('FrontC/Bespeak', 'Logic')->cancelBespeakReason(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Bespeak', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 取消预约
     * POST参数：m_id  bespeak_id预约记录ID reason 自定义取消理由 reason_id 选择理由ID  两者二选一
     * */
    public function cancelBespeak()
    {
        $result = D('FrontC/Bespeak', 'Logic')->cancelBespeak(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Bespeak', 'Logic')->getLogicInfo());
        else
            api_response('success', '取消预约成功哦！', $result);
    }
}
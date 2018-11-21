<?php

namespace Api\Controller;


class ActivitiesController extends ApiBaseController
{
    /**
     * 获取活动列表
     * 详细描述：
     * 特别注意：
     * POST参数：p-页码  m_id
     */
    function getActivities() {
        //文章信息
        $result = D('FrontC/Activities', 'Logic')->getActivities(I('request.'));
        if($result === false)
            api_response('error', D('FrontC/Activities', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }
    /**
     * 活动详情
     * 详细描述：
     * 特别注意：
     * POST参数：activities_id-活动ID  m_id用户ID
     */
    function activitiesDetail() {
        //文章信息
        $result = D('FrontC/Activities', 'Logic')->activitiesDetail(I('request.'));
        if($result === false)
            api_response('error', D('FrontC/Activities', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }
}
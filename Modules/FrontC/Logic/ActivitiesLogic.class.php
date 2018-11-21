<?php


namespace FrontC\Logic;


class ActivitiesLogic extends FrontBaseLogic
{
    /**
     * 获取活动列表
     * */
    public function getActivities($request = array())
    {
        if(empty($request['m_id'])) {
            return $this->setLogicInfo('请登陆哦！', false);
        }
        $param=array('activities.status'=>1);
        $activities=D('FrontC/Activities', 'Model')->getRows($param);
        if(!empty($activities)){
            return $activities;
        }else{
            return array();
        }
    }

    /**
     * 活动详情
     * */
    public function activitiesDetail($request = array())
    {
        if(empty($request['m_id']) || empty($request['activities_id'])) {
            return $this->setLogicInfo('参数错误哦！', false);
        }
        $param = array('activities.id'=>$request['activities_id'],'activities.status'=>1);
        $result = D('FrontC/Activities', 'Model')->getRow($param);
        if(!empty($result)){
            $result['content']=path2abs($result['content']);
            return $result;
        }else{
            return array();
        }
    }
}
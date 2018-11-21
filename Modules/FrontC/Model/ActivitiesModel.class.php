<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/28
 * Time: 10:27
 */

namespace FrontC\Model;


class ActivitiesModel extends FrontBaseModel
{
    /**
     * [获取活动列表调用、]
     * 获取活动列表
     * */
    public function getRows($param=array())
    {
        $activitiess=$this->alias('activities')
            ->field('activities.id activities_id,activities.title,file.abs_url cover')
            ->where($param)
            ->order(array('activities.id asc'))
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = activities.picture',
            ))
            ->select();
        $Page = new \Think\Page(count($activitiess), 9, $_REQUEST);
        $result = array_slice($activitiess,$Page->firstRow,$Page->listRows);
        if(empty($result)){
            return array();
        }else{
            return $result;
        }
    }
    /**
     * [活动详情调用、]
     * 活动详情
     */
    public function getRow($pram=array())
    {
        $activities=$this->alias('activities')
            ->field('activities.id activities_id,activities.title,activities.target_rule,activities.param,activities.content,file.abs_url cover')
            ->where($pram)
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = activities.picture',
            ))
            ->find();
        if(empty($activities)){
            return array();
        }else{
            return $activities;
        }
    }
}
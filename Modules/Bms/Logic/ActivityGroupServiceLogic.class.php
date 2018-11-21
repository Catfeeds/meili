<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/12
 * Time: 11:20
 */

namespace Bms\Logic;


class ActivityGroupServiceLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['id']))
            $param['where']['groupser.id'] = array('LIKE', '%' . $request['id'] . '%');
        //排序条件
        $param['order'] = 'groupser.id DESC';
        //返回数据
        return D('ActivityGroupService')->getList($param);
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(), $request = array()) {
        $data['group_start_time'] = strtotime($data['group_start_time']);
        $data['group_end_time']   = strtotime($data['group_end_time']);
        return $data;
    }
}
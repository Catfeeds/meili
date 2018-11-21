<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/25
 * Time: 10:52
 */

namespace Bms\Logic;


class RegionLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['all_name']))
            $param['where']['region.all_name'] = array('LIKE', '%' . $request['all_name'] . '%');
        if(!empty($request['region_name']))
            $param['where']['region.region_name'] = array('LIKE', '%' . $request['region_name'] . '%');
        //排序条件
        $param['order'] = 'region.id ASC';
        //筛选等级为3的城市
        $param['where']['region_type'] = 3;
        //返回数据
        return D('Region')->getList($param);
    }
}
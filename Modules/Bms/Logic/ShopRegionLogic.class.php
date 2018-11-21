<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/25
 * Time: 9:20
 */

namespace Bms\Logic;


class ShopRegionLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['all_name']))
            $param['where']['shopregion.all_name'] = array('LIKE', '%' . $request['all_name'] . '%');
        //排序条件
        $param['order'] = 'shopregion.id DESC';
        //筛选等级为3的城市
        $param['where']['region_type'] = 3;
        //返回数据
        return D('ShopRegion')->getList($param);
    }

    /*
     * 获取关联城市
     * */
    public function getRegions()
    {
        //返回数据
        return D('ShopRegion')->getRegions();
    }
}
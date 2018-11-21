<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/25
 * Time: 10:53
 */

namespace ShopBms\Model;


class RegionModel extends BmsBaseModel
{
    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('region')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'region');
        //获取数据
        $list  = $this->alias('region')
            ->field('region.id,region.merger_name,region.all_name,region.status')
            ->where($param['special_where'])
            ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
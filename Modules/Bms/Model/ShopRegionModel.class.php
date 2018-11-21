<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/25
 * Time: 9:23
 */

namespace Bms\Model;


class ShopRegionModel extends BmsBaseModel
{
    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('shopregion')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'shopregion');
        //获取数据
        $list  = $this->alias('shopregion')
            ->field('shopregion.id,shopregion.merger_name,shopregion.all_name,shopregion.status')
            ->where($param['special_where'])
            ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getRegions() {
        $param=array('region_type'=>3,'status'=>1);
        //获取数据
        $list  = M('ShopRegion')
            ->field('id,all_name')
            ->where($param)
            ->select();
        //返回数据
        if(!empty($list)){
                return $list;
        }else{
            return array();
        }
    }
}
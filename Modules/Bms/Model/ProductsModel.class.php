<?php
namespace Bms\Model;

/**
 * Class ProductsModel
 * @package Bms\Model
 * 货品数据层
 */
class ProductsModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array ();

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array();

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //生成ID查询条件
        $param  = $this->specialSearch($param, null, $param['alias']);
        //获取数据
        $list   = $this->alias('product')
                        ->field('product.id,product.goods_id,product.goods_attr_ids,product.product_sn,product.product_stock')
                        ->where($param['special_where'])
                        ->select();
        //返回记录 根据ID顺序排序
        //return array('list'=>sort_by_array($param['ids_for_sort'], $list));
        return sort_by_array($param['ids_for_sort'], $list);
    }

    /**
     * @param $goods_id
     * @return int
     * 获得商品的货品总库存
     */
    function productStockCount($goods_id = 0) {
        if(empty($goods_id)) {
            return -1;
        }
        $count = $this->where(array('goods_id'=>$goods_id))->sum('product_stock');
        return empty($count) ? 0 : $count;
    }
}
<?php
namespace FrontC\Model;

/**
 * Class CartModel
 * @package FrontC\Model
 * 购物车数据层
 */
class CartModel extends FrontBaseModel {


    /**
     * [查看购物车列表调用]
     * @param array $param
     * @return array
     * 基本列表
     */
    function getList($param = array()) {
        //数据总数
        //$total  = $this->alias('cart')->where($param['where'])->count();
        //创建分页对象
        //$Page   = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, null, 'cart');
        //获取数据
        $list   = $this->alias('cart')
                    ->field('cart.id cart_id,cart.goods_id,cart.goods_sn,cart.goods_name,cart.cover,cart.price,cart.number,cart.unit,
                    cart.product_id,cart.goods_attr_desc,cart.goods_attr_ids,goods.is_integral,goods.goods_cate_id,goods.status')
                    ->where($param['special_where'])
                    ->join(array(
                        'LEFT JOIN ' . C('DB_PREFIX') . 'goods goods ON goods.id = cart.goods_id',
                    ))
                    ->select();
        //返回记录 根据ID顺序排序
        //return array('list'=>sort_by_array($param['ids_for_sort'], $list, 'cart_id'), 'page'=>$Page->show());
        return array('list'=>sort_by_array($param['ids_for_sort'], $list, 'cart_id'));
    }
}
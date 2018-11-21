<?php

namespace FrontC\Service;

/**
 * Class CartService
 * @package FrontC\Service
 * 购物车数据服务层
 */
class CartService extends FrontBaseService
{

    /**
     * [查看购物车列表调用]
     * @param $custom_param
     * @param array $extra
     * @return array
     * 获取购物车商品列表
     */
    function cartList($custom_param, $extra = array())
    {
        //排序
        $param['order'] = 'cart.id DESC';
        //是否有外部其他自定义条件  如果有替换条件
        if (!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/Cart')->getList($param);
        //数据列表 //分页信息
        $list = $result['list'];
        $page = $result['page'];
        //如果没有数据返回空数组
        if (empty($list))
            return array();
        //处理列表数据
        array_walk($list, 'FrontC\Service\CartService::dataFactory', $extra);

        return $list;
    }

    /**
     * @param $value
     * @param $key
     * @param $extra
     * 数据加工
     */
    public static function dataFactory(&$value, $key, $extra)
    {
        $value['cover'] = api('File/getFiles', array($value['cover'], array('id', 'abs_url')))[0]['abs_url'];
    }

    /**
     * @param int $m_id
     * @param string $cart_ids
     * @return bool
     * 清空购物车
     */
    function clearCart($m_id = 0, $cart_ids = '')
    {
        $where['m_id'] = $m_id;
        if (!empty($cart_ids))
            $where['id'] = array('IN', $cart_ids);
        M('Cart')->where($where)->delete();
        return true;
    }

    /**
     * [购物车价格总计调用]
     * @param int $m_id
     * @param string $cart_ids
     * @return int
     * 获取某用户购物车商品总计
     */
    public function getTotalPrice($m_id = 0, $cart_ids = '')
    {
        if (empty($m_id) || empty($cart_ids))
            return array('total' => '0');
        $where['m_id'] = $m_id;
        $where['id'] = array('IN', $cart_ids);
        //$total = M('Cart')->where($where)->field('SUM(price*number) total')->select();
        $total = M('Cart')->where($where)->sum('(price*number)');
        return array('total' => "$total");
    }
}
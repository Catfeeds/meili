<?php

namespace FrontC\Logic;

/**
 * Class CartLogic
 * @package FrontC\Logic
 * 购物车逻辑处理层
 */
class CartLogic extends FrontBaseLogic
{

    /**
     * [cart添加购物车调用]
     * @param array $request
     * @return bool
     * 添加到购物车
     */
    function addToCart($request = array())
    {
        //参数判空
        if (empty($request['goods_id']) || empty($request['number']))
            return $this->setLogicInfo('参数错误！', false);
        //获取商品信息
        $goods = M('Goods')->where(array('id' => $request['goods_id'], 'status' => 1))->field('id goods_id,goods_sn,goods_name,cover,price,unit,stock')->find();
        if (empty($goods))
            return $this->setLogicInfo('商品已下架！', false);
        //判断库存
        if ($goods['stock'] != -1 && $goods['stock'] < $request['number'])
            return $this->setLogicInfo('库存不足！', false);
        //判断是否添加过该商品
        $where['m_id'] = $request['m_id'];
        $where['goods_id'] = $request['goods_id'];
        //已添加过该商品 则更新数量
        if (M('Cart')->where($where)->count()) {
            if (!M('Cart')->where($where)->setInc('number', $request['number'])) {
                return $this->setLogicInfo('系统繁忙，稍后重试！', false);
            }
            return $this->setLogicInfo('添加成功！', true);
        }
        //创建购物车数据
        $data = array(
            'm_id' => $request['m_id'],
            'goods_id' => $request['goods_id'],
            'goods_sn' => $goods['goods_sn'],
            'goods_name' => $goods['goods_name'],
            'cover' => $goods['cover'],
            'price' => $goods['price'],
            'unit' => $goods['unit'],
            'number' => $request['number'],
        );
        //加入购物车
        if (!M('Cart')->data($data)->add()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('添加成功！', true);
    }

    /**
     * [cart购物车查看列表调用]
     * @param array $request
     * @return array
     * 购物车商品列表
     */
    function cartList($request = array())
    {
        //用户ID筛选
        $param['where']['cart.m_id'] = $request['m_id'];
        if (!empty($request['cart_ids']))
            $param['where']['cart.id'] = array('IN', $request['cart_ids']);
        //获取购物车商品列表
        $list = D('FrontC/Cart', 'Service')->cartList($param, $request);
        return $list;
    }

    /**
     * [购物车删除调用]
     * @param array $request
     * @return bool
     */
    function delFromCart($request = array())
    {
        //没有数据
        if (empty($request['cart_ids']))
            return $this->setLogicInfo('请选择要删除的商品！', false);
        //执行删除
        if (!M('Cart')->where(array('m_id' => $request['m_id'], 'id' => array('IN', $request['cart_ids'])))->delete())
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        return $this->setLogicInfo('删除成功！', true);
    }

    /**
     * [购物车更新调用]
     * @param array $request
     * @return bool
     */
    function updCart($request = array())
    {
        if (empty($request['data']))
            return $this->setLogicInfo('未更新数据！', true);
        //转化为数组
        $data_array = json_decode($_REQUEST['data'], 'array');
        //判断是否转化成功 json格式是否正确
        if (empty($data_array))
            return $this->setLogicInfo('数据转化出错！', true);
        //循环更新
        foreach ($data_array as $data) {
//            $goods = M('Goods')->where(array('id'=>$data['goods_id']))->field('id goods_id,goods_name,price,stock')->find();
            $goods = M('Goods')->where(array('id' => $data['goods_id']))->field('goods_name,stock')->find();
            //获取商品/货品的库存和价格 货品ID
//            $stock_price = D('FrontC/Goods', 'Service')->getStockPrice(0, '', $goods, $data['product_id']);
            //判断库存
//            if($stock_price['stock'] != -1 && $stock_price['stock'] < $data['number'])
//                return $this->setLogicInfo($goods['goods_name'] . '--库存不足！', false);
            if ($goods['stock'] != -1 && $goods['stock'] < $data['number'])
                return $this->setLogicInfo($goods['goods_name'] . '--库存不足！', false);
            //更新条件
            $where['m_id'] = $request['m_id'];
            $where['goods_id'] = $data['goods_id'];
//            $where['product_id'] = $data['product_id'];
            //更新
            if (M('Cart')->where($where)->setField('number', $data['number']) === false)
                return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('更新成功！', true);
    }
}
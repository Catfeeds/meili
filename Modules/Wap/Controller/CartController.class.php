<?php
namespace Wap\Controller;

/**
 * Class CartController
 * @package Wap\Controller
 * 购物车控制器
 */
class CartController extends WapBaseController {

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //判断登陆
        $this->checkLogin();
    }

    /**
     * 添加到购物车
     * 详细描述：
     * 特别注意：商品属性ID串的排序 按照属性规格从上到下
     * POST参数：*m_id(用户ID) *goods_id(商品ID) *number(购买数量) goods_attr_ids(商品属性ID串，格式：12|33|65 以“|”隔开 注意排序)
     *           goods_attr_desc(商品属性文字描述，格式：尺寸：100cm 颜色：黑色)
     */
    function addToCart() {
        $result = D('FrontC/Cart', 'Logic')->addToCart(I('request.'));
        if($result === false)
            $this->error(D('FrontC/Cart', 'Logic')->getLogicInfo());
        else
            $this->success(D('FrontC/Cart', 'Logic')->getLogicInfo(), '');
    }

    /**
     * 购物车商品列表
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function cartList() {
        $result = D('FrontC/Cart', 'Logic')->cartList(I('request.'));
        //var_dump($result);
        if($result === false) {
            //api_response('error', D('FrontC/Cart', 'Logic')->getLogicInfo());
        } else {
            $this->assign('carts', $result);
        }
        $this->display('cartList');
    }

    /**
     * 从购物车删除
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *cart_ids(购物车主键ID串 逗号隔开)
     */
    function delFromCart() {
        $result = D('FrontC/Cart', 'Logic')->delFromCart(I('request.'));
        //var_dump(D('FrontC/Cart', 'Logic')->getLogicInfo());
        if($result === false)
            $this->error(D('FrontC/Cart', 'Logic')->getLogicInfo());
        else
            $this->success(D('FrontC/Cart', 'Logic')->getLogicInfo());
    }

    /**
     * 更新购物车
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *data(更新数据 json格式 '[{"goods_id":1,"product_id":2,"number":10},{"goods_id":1,"product_id":2,"number":10}]')
     */
    function updCart() {
        $result = D('FrontC/Cart', 'Logic')->updCart(I('request.'));
        if($result === false)
            $this->error(D('FrontC/Cart', 'Logic')->getLogicInfo());
        else
            $this->success(D('FrontC/Cart', 'Logic')->getLogicInfo());
    }
}
<?php
namespace Bms\Controller;

/**
 * Class CouponController
 * @package Bms\Controller
 * 优惠券控制器
 */
class CouponController extends BmsBaseController {

    /**
     * 添加时关联数据
     */
    function getAddRelation() {
        $this->assign('select', D('GoodsCategory','Logic')->getSelect('goods_cate_id',0));
    }

    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        $row = $this->get('row');
        $this->assign('select', D('GoodsCategory','Logic')->getSelect('goods_cate_id',$row['goods_cate_id']));
    }
}
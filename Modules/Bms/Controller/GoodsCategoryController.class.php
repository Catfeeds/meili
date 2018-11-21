<?php
namespace Bms\Controller;

/**
 * Class GoodsCategoryController
 * @package Bms\Controller
 * 商品分类控制器
 */
class GoodsCategoryController extends BmsBaseController {

    /**
     * 添加时关联数据
     */
    function getAddRelation() {
        $this->assign('select',D('GoodsCategory','Logic')->getSelect('parent_id',I('get.id')));
    }

    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        $this->assign('select',D('GoodsCategory','Logic')->getSelect('parent_id',I('get.parent_id')));
    }
}
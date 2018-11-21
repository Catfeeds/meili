<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/20
 * Time: 17:42
 */

namespace Bms\Controller;


class ServiceController extends BmsBaseController
{
    function getIndexRelation() {
        //$this->assign('spec_types', D('Goods')->getSpecGoodsType());
        //获取商品分类
        $this->assign('select', D('ServiceCategory','Logic')->getSelect('service_cate_id', I('request.service_cate_id')));
    }
    function getAddRelation() {
        //获取商品类型
        //$this->assign('type_list', D('GoodsType')->getList(array('field'=>'id type_id,type_name')));
        //获取商品分类
        $this->assign('select', D('ServiceCategory','Logic')->getSelect('service_cate_id', ''));
    }
    function getUpdateRelation() {
        //获取商品类型
        //$this->assign('type_list', D('GoodsType')->getList(array('field'=>'id type_id,type_name')));
        //获取商品分类
        $row = $this->get('row');
        $this->assign('select', D('ServiceCategory','Logic')->getSelect('service_cate_id', $row['service_cate_id']));
        //获取商品属性
        //$this->assign('goods_attr_form', D('Goods')->buildAttrForm($row['type_id'], $row['id']));
    }
}
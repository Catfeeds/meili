<?php
namespace Bms\Controller;

/**
 * Class AttributeController
 * @package Bms\Controller
 * 属性控制器
 */
class AttributeController extends BmsBaseController {


    function getIndexRelation() {
        //$this->assign('select',D('Article','Logic')->getSelect('art_cate_id',I('request.art_cate_id')));
    }

    function getUpdateRelation() {
        //var_dump($_REQUEST);
        //var_dump(M('GoodsTypeGroup')->where(array('type_id'=>I('request.type_id')))->field('id,group_name')->select());
        $this->assign('groups', M('GoodsTypeGroup')->where(array('type_id'=>I('request.type_id')))->field('id,group_name')->select());
    }

    function getAddRelation() {
        //$this->assign('types',M('GoodsType')->field('id'));
        $this->assign('groups', M('GoodsTypeGroup')->where(array('type_id'=>I('request.type_id')))->field('id,group_name')->select());
    }
}
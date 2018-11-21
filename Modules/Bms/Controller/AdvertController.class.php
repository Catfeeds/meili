<?php
namespace Bms\Controller;

/**
 * Class AdvertController
 * @package Bms\Controller
 * 广告管理控制器
 */
class AdvertController extends BmsBaseController {


    function getUpdateRelation() {
        $this->assign('position',C('POSITION_AD'));
        $this->assign('target_rules',C('TARGET_RULES_C'));
//        $row = $this->get('row');
//        $this->assign('select',D('GoodsCategory','Logic')->getSelect('goods_cate_id',$row['goods_cate_id'],'id',array('where'=>array('level'=>1))));
    }

    function getAddRelation() {
        $this->assign('position',C('POSITION_AD'));
        $this->assign('target_rules',C('TARGET_RULES_C'));
//        $this->assign('select',D('GoodsCategory','Logic')->getSelect('goods_cate_id',I('get.id'),'id',array('where'=>array('level'=>1))));
    }
}
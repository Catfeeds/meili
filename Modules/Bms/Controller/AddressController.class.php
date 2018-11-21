<?php
namespace Bms\Controller;

/**
 * Class AddressController
 * @package Bms\Controller
 * 用户地址控制器
 */
class AddressController extends BmsBaseController {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //逻辑层对象
        self::$logicObject = D('MsC/Address', 'Logic');
    }

    function getIndexRelation() {
        //获取城市列表
        //$this->assign('cities', D('Region','Service')
        //->select(array('where'=>array('parent_id'=>0))));
        //$this->assign('count', M('Address')->where(array('circle_id'=>0))->count());
    }

    function getUpdateRelation() {
        $this->_commonAssign();
    }

    function getAddRelation() {
        $this->_commonAssign();
    }

    private function _commonAssign() {
        //获取城市列表
        $this->assign('cities', D('Region','Service')->select(array('where'=>array('parent_id'=>0))));
        //获取区域列表
        if(!empty($_REQUEST['city_id']))
            $this->assign('areas', D('Region','Service')->select(array('where'=>array('parent_id'=>I('request.city_id')))));
        //获取商圈列表
        if(!empty($_REQUEST['area_id']))
            $this->assign('circles', D('Region','Service')->select(array('where'=>array('parent_id'=>I('request.area_id')))));
    }
}
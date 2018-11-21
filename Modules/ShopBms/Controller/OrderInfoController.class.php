<?php
namespace ShopBms\Controller;

/**
 * Class OrderInfoController
 * @package Bms\Controller
 * 订单 控制器
 */
class OrderInfoController extends BmsBaseController {

    function getIndexRelation() {
        //获取待发货单数量
        $this->assign('count_2', M('OrderInfo')->where(array('status'=>2))->count());
    }

	/**
     * 发货
     */
    function delivery() {
        //权限验证
        $this->checkRule(self::$rule);
        $result = self::$logicObject->delivery(I('request.'));
        if($result) {
            $this->success(self::$logicObject->getLogicInfo(), cookie('__forward__'));
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }

    /**
     * 收货
     */
    function receiving() {
        //权限验证
        $this->checkRule(self::$rule);
        $result = self::$logicObject->receiving(I('request.'));
        if($result) {
            $this->success(self::$logicObject->getLogicInfo(), cookie('__forward__'));
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }

    /**
     * 取消
     */
    function cancel() {
        //权限验证
        $this->checkRule(self::$rule);
        $result = self::$logicObject->cancel(I('request.'));
        if($result) {
            $this->success(self::$logicObject->getLogicInfo(), cookie('__forward__'));
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }
}
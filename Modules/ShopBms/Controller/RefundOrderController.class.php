<?php
namespace ShopBms\Controller;

/**
 * Class RefundOrderController
 * @package Bms\Controller
 * 退款单控制器
 */
class RefundOrderController extends BmsBaseController {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //逻辑层对象
        self::$logicObject = D('OrderInfo', 'Logic');
    }

    function getIndexRelation() {
        //获取未处理退款单数量
        $this->assign('count_1', M('OrderInfo')->where(array('status'=>7))->count());
    }

    /**
     * 取消退款
     */
    function cancelRefund() {
        $this->checkRule(self::$rule);
        $result = self::$logicObject->cancelRefund(I('request.'));
        if(!$result) {
            $this->error(self::$logicObject->getLogicInfo());
        }
        $this->success('操作成功！');
    }

    /**
     * 退款
     */
    function refund() {
        $this->checkRule(self::$rule);
        $result = self::$logicObject->refund(I('request.'));
        if(!$result) {
            $this->error(self::$logicObject->getLogicInfo());
        }
        $this->success('退款成功！');
    }
}
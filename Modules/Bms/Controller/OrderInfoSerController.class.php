<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/26
 * Time: 11:03
 */

namespace Bms\Controller;


class OrderInfoSerController extends BmsBaseController
{
    function getIndexRelation() {
        //获取待发货单数量
        $this->assign('count_2', M('OrderInfoSer')->where(array('status'=>2))->count());
    }
    /**
     * 取消订单
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
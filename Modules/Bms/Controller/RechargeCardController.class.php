<?php
namespace Bms\Controller;

/**
 * Class RechargeCardController
 * @package Bms\Controller
 * 充值卡控制器
 */
class RechargeCardController extends BmsBaseController {

    /**
     * 生成充值码
     */
    function createCode() {
        $this->checkRule(self::$rule);
        $result = self::$logicObject->createCode(I('request.'));
        if($result) {
            $this->success(self::$logicObject->getLogicInfo(), U('RechargeCode/index',array('rec_card_id'=>I('request.rec_card_id'))));
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }
}
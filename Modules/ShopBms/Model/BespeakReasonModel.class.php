<?php
namespace ShopBms\Model;

/**
 * Class RefundReasonModel
 * @package Bms\Model
 * 退款原因 数据层
 */
class BespeakReasonModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('reason', 'require', '请输入原因！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('reason', '1,15', '原因在15个字符以内！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
}
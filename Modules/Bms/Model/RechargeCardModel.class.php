<?php
namespace Bms\Model;

/**
 * Class RechargeCardModel
 * @package Bms\Model
 * 充值卡数据模型
 */
class RechargeCardModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '请输入充值卡名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,15', '充值卡名称长度不能超过15个字符！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('face_value', 'require', '请输入充值卡面值！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('sales_price', 'require', '请输入充值卡售价！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('bg_picture', 'require', '请上传卡背景！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );
}
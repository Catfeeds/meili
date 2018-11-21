<?php
namespace Bms\Model;

/**
 * Class CouponModel
 * @package Bms\Model
 * 优惠券数据模型
 */
class CouponModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('unique_code', 'require', '请输入唯一标识！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('unique_code', '/^[a-zA-Z]\w{0,39}$/', '唯一标识必须为英文！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('unique_code', '1,30', '标识长度不能超过30个字符！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('unique_code', 'checkUnique', '该标识已经存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('unique_code')),
        array('name', 'require', '请输入优惠券名称描述！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,30', '名称描述长度不能超过30个字符！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('face_value', 'require', '请输入优惠券面值！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH, array('unique_code')),
        array('use_condition', 'require', '请输入可用条件！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('start_use_time', 'require', '请选择优惠券开始使用时间！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('valid_term', 'require', '请输入优惠券有效期！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
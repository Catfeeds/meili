<?php
namespace FrontC\Model;

/**
 * Class UserBankcardModel
 * @package FrontC\Model
 * 用户银行卡信息数据层
 */
class UserBankcardModel extends FrontBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('open_name', 'require', '请输入持卡人姓名！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('open_name', '2,5', '持卡人姓名需要2-5个字！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('card_number', 'require', '请输入卡号！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//        array('card_number', '/^\d{14,20}$/', '卡号格式不正确！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//        array('mobile', 'require', '请输入预留手机号！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//        array('mobile', '/^0?(13[0-9]|15[012356789]|18[012356789]|14[57]|17[037])[0-9]{8}$/', '手机号格式不正确！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('bank_name', 'require', '请选择银行卡类型！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('bank_short', 'require', '请选择银行卡类型！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('address', 'require', '请输入详细地址！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('address', '6,30', '详细地址不能低于6个字，不能高于30个字！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
}
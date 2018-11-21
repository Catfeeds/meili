<?php
namespace FrontC\Model;

/**
 * Class AddressModel
 * @package FrontC\Model
 * 用户地址数据层
 */
class AddressModel extends FrontBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('contacts', 'require', '请输入联系姓名！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('contacts', '2,5', '联系姓名需要2-5个字！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('mobile', 'require', '请输入联系手机号！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mobile', '/^0?(13[0-9]|15[012356789]|18[012356789]|14[57]|17[037])[0-9]{8}$/', '联系手机号格式不正确！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('province_id', 'require', '请选择所在省！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('city_id', 'require', '请选择所在城市！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('area_id', 'require', '请选择所在区县！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('ress', 'require', '请输入定位地址！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('address', 'require', '请输入详细地址！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('address', '1,30', '详细地址不能低于1个字，不能高于30个字！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
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
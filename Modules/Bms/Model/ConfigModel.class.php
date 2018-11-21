<?php
namespace Bms\Model;

/**
 * Class ConfigModel
 * @package Bms\Model
 * 系统配置 模型
 */
class ConfigModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '配置名称必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '/^[a-zA-Z]\w{0,39}$/', '配置名称不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,30', '配置名称长度不能超过30个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', 'checkUnique', '该配置名称已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('name')),
        array('title', 'require', '配置标题必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,20', '配置标题长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('type', 'require', '配置类型未选择', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('config_group', 'require', '配置分组未选择', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('value', 'require', '配置值未填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );
}
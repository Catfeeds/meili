<?php
namespace Bms\Model;

/**
 * Class SendTemplateModel
 * @package Bms\Model
 * 发信模板模型
 */
class SendTemplateModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('unique_code', 'require', '模板标识必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('unique_code', '/^[a-zA-Z]\w{0,39}$/', '模板标识不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('unique_code', '1,45', '模板标识长度不能超过45个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('unique_code', 'checkUnique', '模板标识已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('unique_code')),
        array('type', 'require', '请选择模板类型', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('subject', 'require', '模板标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('template', 'require', '模板内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),

        //array('target_rule', 'require', '模板内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('template', 'require', '模板内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
<?php
namespace Bms\Model;

/**
 * Class ActionModel
 * @package Bms\Model
 * 行为信息 模型
 */
class ActionModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('unique_code', 'require', '行为标识必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('unique_code', '/^[a-zA-Z]\w{0,39}$/', '行为标识不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('unique_code', '1,20', '行为标识长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('unique_code', 'checkUnique', '行为标识已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('unique_code')),
        array('name', 'require', '行为名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,20', '标题长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('remark', 'require', '行为描述不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('remark', '1,120', '行为描述不能超过120个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
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
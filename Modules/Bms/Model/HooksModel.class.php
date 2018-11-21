<?php
namespace Bms\Model;

/**
 * Class HooksModel
 * @package Bms\Model
 * 钩子模型
 */
class HooksModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证
     */
    protected $_validate = array(
        array('name', 'require', '钩子名称未填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '/^[a-zA-Z]\w{0,39}$/', '钩子名称不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', 'checkUnique', '钩子名称已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('name')),
        array('name', '0,30', '名称长度在0--30位', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('description', 'require', '钩子描述未填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('description', '0,225', '描述长度在0--225位', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /**
     * 文件模型自动完成
     * @var array
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

}
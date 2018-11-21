<?php
namespace Bms\Model;

/**
 * 分组权限 模型
 */

class AuthGroupModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('title', 'require', '您未填写组名', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', 'checkUnique', '该组名已经存在', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array('title')),
        array('title', '1,15', '组名长度不能超过15个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('description', 'require', '组名描述不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('description', '1,80', '描述长度不能超过80个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
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
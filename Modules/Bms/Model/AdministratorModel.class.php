<?php
namespace Bms\Model;

/**
 * Class AdministratorModel
 * @package Bms\Model
 * 管理员模型
 */
class AdministratorModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('account', 'require', '账号未填写', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('account', '/^[a-zA-Z]\w{0,39}$/', '账号不合法', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('account', '0,15', '账号长度在0--15位', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('account', 'checkUnique', '该账号已经存在', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array('account')),
        array('password', 'require', '密码不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
        array('password', '6,18', '密码长度在6--18位', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('group_id', 'require', '您未选择分组', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array (
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );
}
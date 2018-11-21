<?php
/**
 * Created by PhpStorm.
 * User: toocms
 * Date: 2018/11/9
 * Time: 14:14
 */

namespace Bms\Model;


class ApkUpdateModel extends BmsBaseModel
{
    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('version', 'require', '{%_VERSION_NUMBER_}', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('description', 'require', '{%_VERSION_CONTENT_}', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
    );
}
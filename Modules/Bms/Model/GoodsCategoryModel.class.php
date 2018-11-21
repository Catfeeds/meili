<?php
namespace Bms\Model;

/**
 * Class GoodsCategoryModel
 * @package Bms\Model
 * 商品数据模型
 */
class GoodsCategoryModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '分类名称未填写！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', 'checkUnique', '该名称已经存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('name')),
        array('name', '1,6', '名称长度在6个字符以内！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array (
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );
}
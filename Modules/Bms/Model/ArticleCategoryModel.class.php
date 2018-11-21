<?php
namespace Bms\Model;

/**
 * Class ArticleModel
 * @package Bms\Model
 * 文章咨询分类模型
 */
class ArticleCategoryModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '分类名称未填写', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', 'checkUnique', '该名称已经存在', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array('name')),
        array('name', '0,20', '名称长度在0--20位', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('link', '/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', '连接地址非法', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
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
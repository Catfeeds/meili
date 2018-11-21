<?php
namespace Bms\Model;

/**
 * Class GoodsTypeModel
 * @package Bms\Model
 * 商品类型数据层
 */
class GoodsTypeModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('type_name', 'require', '请输入类型名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('type_name', 'checkUnique', '类型名称已经存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('type_name')),
        //array('type_name', '1,20', '行为标识长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
}
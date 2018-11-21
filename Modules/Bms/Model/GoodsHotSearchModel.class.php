<?php
namespace Bms\Model;

/**
 * Class GoodsHotSearchModel
 * @package Bms\Model
 * 商品热门搜索数据层
 */
class GoodsHotSearchModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('keywords', 'require', '请输入关键字！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('keywords', '1,9', '关键字长度不能超过9个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('keywords', 'checkUnique', '关键字已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('keywords')),
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
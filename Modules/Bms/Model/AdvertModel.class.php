<?php
namespace Bms\Model;

/**
 * Class AdvertModel
 * @package Bms\Model
 * 广告模型
 */
class AdvertModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array ();

    /**
     * 初始化处理
     */
    public function _initialize() {
        //自动验证规则
        $this->_validate = array (
            array('position', 'require', '请选择广告位置！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//            array('goods_cate_id', 'checkCateId', '请选择商品分类！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
            array('goods_cate_id', 'checkCateIdRepeat', '每个商品分类只能添加一个广告！', self::MUST_VALIDATE, 'callback', self::MODEL_INSERT),
            array('start_time', 'require', '请选择广告开始展示时间！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
            array('end_time', 'require', '请选择广告结束展示时间！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
            array('end_time', 'checkEndTime', '结束时间有误！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
            array('picture', 'require', '请上传广告图片！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
            array('target_rule', 'require', '请选择跳转规则！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
            array('target_rule', 'checkTargetRule', '启动页广告跳转规则必须为网址！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
            //array('url', '/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', '连接地址非法', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        );
    }

    /**
     * @param $value
     * @return bool
     * 验证时间段区间是否正确
     */
    function checkEndTime($value) {
        if(strtotime($value) - strtotime($_REQUEST['start_time']) <= 0) {
            return false;
        }
    }

    /**
     * @param $value
     * @return bool
     * 验证是否需要选择商品分类
     */
    function checkCateId($value) {
        if($_REQUEST['position'] == 2 && empty($value)) {
            return false;
        }
    }

    /**
     * @param $value
     * @return bool
     * 判断分类广告数量
     */
    function checkCateIdRepeat($value) {
        if($_REQUEST['position'] == 2 && M('Advert')->where(array('goods_cate_id'=>$value))->count())
            return false;
    }

    /**
     * @param $value
     * @return bool
     * 判断启动广告跳转规则
     */
    function checkTargetRule($value) {
        if($_REQUEST['position'] == 3 && $value != 1)
            return false;
    }

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array (
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );
}
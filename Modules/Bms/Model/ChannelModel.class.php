<?php
namespace Bms\Model;

/**
 * Class ChannelModel
 * @package Bms\Model
 * 首页栏目数据
 */
class ChannelModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '请输入栏目名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,4', '栏目名称不能超过4个字符！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', 'checkUnique', '栏目名称已存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('name')),
        array('icon', 'require', '请上传栏目图标！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('target_rule', 'require', '请选择跳转规则！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    /**
     * @param array $param
     * @return array
     * 获取列表 公用方法
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('cnl')->where($param['where'])->count();
        //获取分类对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //数据列表
        $list  = $this->alias('cnl')
                      ->where($param['where'])
                      ->field('cnl.id,cnl.name,cnl.sort,cnl.target_rule,cnl.remark,cnl.update_time,cnl.status,file.abs_url')
                      ->join(array(
                          'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = cnl.icon',
                      ))
                      ->order('sort DESC,id DESC')
                      ->limit($Page->firstRow, $Page->listRows)
                      ->select();

        return array('list' => $list, 'page' => $Page->show());
    }
}
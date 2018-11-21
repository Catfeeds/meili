<?php
namespace Bms\Model;

/**
 * Class SectionModel
 * @package Bms\Model
 * 首页版块数据
 */
class SectionModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '请输入整体版块名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,4', '整体版块名称不能超过4个字符！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', 'checkUnique', '整体版块名称已存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('name')),
        array('position', 'require', '请选择板块位置！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('layout', 'require', '请选择小版块布局！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        $total  = $this->alias('stn')->where($param['where'])->count();
        //获取分类对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //数据列表
        $list  = $this->alias('stn')
            ->where($param['where'])
            ->field('stn.id,stn.name,stn.layout,stn.configure,stn.sort,stn.remark,stn.update_time,stn.status,stn.position')
//            ->join(array(
//                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = cnl.icon',
//            ))
            ->order('sort DESC,id DESC')
            ->limit($Page->firstRow, $Page->listRows)
            ->select();

        return array('list' => $list, 'page' => $Page->show());
    }
}
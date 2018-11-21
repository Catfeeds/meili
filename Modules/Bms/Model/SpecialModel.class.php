<?php
namespace Bms\Model;

/**
 * Class SpecialModel
 * @package Bms\Model
 * 商品数据
 */
class SpecialModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '请输入专题名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,15', '专题名称不能超过15个字符！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', 'checkUnique', '专题名称已存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('name')),
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
        $total  = $this->alias('spe')->where($param['where'])->count();
        //获取分类对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //数据列表
        $list  = $this->alias('spe')
            ->where($param['where'])
            ->field('spe.id,spe.name,spe.remark,spe.update_time,spe.status,(SELECT COUNT(*) FROM toocms_special_goods WHERE spe_id=spe.id) goods_count')
            ->join(array(
                //'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = cnl.icon',
            ))
            ->order('id DESC')
            ->limit($Page->firstRow, $Page->listRows)
            ->select();

        return array('list' => $list, 'page' => $Page->show());
    }
}
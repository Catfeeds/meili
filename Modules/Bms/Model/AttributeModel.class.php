<?php
namespace Bms\Model;

/**
 * Class AttributeModel
 * @package Bms\Model
 * 属性数据层
 */
class AttributeModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('attr_name', 'require', '请输入属性名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('attr_index', 'require', '请选择检索类型！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('attr_type', 'require', '请选择属性类型！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('attr_input_type', 'require', '请选择输入类型！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array ();

    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('attr')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'attr');
        //获取数据
        $list  = $this->alias('attr')
            ->field('attr.id,attr.attr_name,attr.type_id,attr.attr_type,attr.attr_index,attr.sort,attr.attr_input_type,attr.status,
            type_group.group_name')
            ->where($param['special_where'])
            ->join(array(
                //'LEFT JOIN '.C('DB_PREFIX').'goods_type type ON type.id = attr.type_id',
                'LEFT JOIN '.C('DB_PREFIX').'goods_type_group type_group ON type_group.id = attr.type_group_id',
            ))
            ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
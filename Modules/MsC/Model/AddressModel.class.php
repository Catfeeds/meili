<?php
namespace MsC\Model;

/**
 * Class AddressModel
 * @package MsC\Model
 * 用户地址数据层
 */
class AddressModel extends MscBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('city_id', 'require', '请选择所在城市！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('area_id', 'require', '请选择所在区域！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('address', 'require', '详细地址不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('address', '6,30', '详细地址不能低于6个字，不能高于30个字！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array();

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total  = $this->alias($param['alias'])->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, $param['alias']);
        //获取数据
        $list   = $this->alias($param['alias'])
            ->field('adr.id,adr.m_id,adr.contacts,adr.mobile,adr.province_name,adr.city_name,adr.area_name,adr.address,
            adr.create_time,adr.is_default,m.account,m.nickname')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = adr.m_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
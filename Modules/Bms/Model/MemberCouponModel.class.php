<?php
namespace Bms\Model;

/**
 * Class MemberCouponModel
 * @package Bms\Model
 * 用户优惠券 模型
 */
class MemberCouponModel extends BmsBaseModel {

    /**
     * @param array $param  综合条件参数
     * @return array
     * 获取列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('m_cpn')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'm_cpn');
        //获取数据
        $list  = $this->alias('m_cpn')
            ->field('m_cpn.id,m_cpn.m_id,m_cpn.unique_code,m_cpn.face_value,m_cpn.use_condition,m_cpn.effective_date,
            m_cpn.invalid_date,m_cpn.create_time,m_cpn.use_time,m_cpn.status,m_cpn.order_id,m.nickname,m.account,oi.order_sn')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = m_cpn.m_id',
                'LEFT JOIN '.C('DB_PREFIX').'order_info oi ON oi.id = m_cpn.order_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
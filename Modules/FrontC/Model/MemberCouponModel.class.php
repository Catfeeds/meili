<?php
namespace FrontC\Model;

/**
 * Class MemberCouponModel
 * @package FrontC\Model
 * 用户优惠券数据模型
 */
class MemberCouponModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total = $this->alias('m_cpn')->where($param['where'])->count();
        //创建分页对象
        $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'm_cpn');
        //获取数据
        $list = $this->alias('m_cpn')
                    ->field('m_cpn.id m_cpn_id,m_cpn.goods_cate_id,m_cpn.face_value,m_cpn.use_condition,m_cpn.invalid_date,m_cpn.effective_date,
                    m_cpn.status')
                    ->where($param['special_where'])
                    ->select();
        //返回记录 根据ID顺序排序
        return array('list' => sort_by_array($param['ids_for_sort'], $list, 'm_cpn_id'), 'page' => $Page->show());
    }
}
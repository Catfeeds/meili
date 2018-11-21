<?php
namespace Bms\Model;

/**
 * Class CashTurnoverModel
 * @package Bms\Model
 * 第三方支付记录 模型
 */
class CashTurnoverModel extends BmsBaseModel {

    /**
     * @param array $param  综合条件参数
     * @return array
     * 获取列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('cash')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'cash');
        //获取数据
        $list  = $this->alias('cash')
            ->field('cash.id,cash.m_id,cash.payment,cash.trend,cash.amounts,cash.order_id,cash.order_sn,cash.order_type,
            cash.turnover_number,cash.create_time,cash.status,m.nickname,m.account')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = cash.m_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
<?php
namespace Bms\Model;

/**
 * Class IntegralLogModel
 * @package Bms\Model
 * 积分记录 模型
 */
class IntegralLogModel extends BmsBaseModel {

    /**
     * @param array $param  综合条件参数
     * @return array
     * 获取列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('itg_log')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'itg_log');
        //获取数据
        $list  = $this->alias('itg_log')
            ->field('itg_log.id,itg_log.m_id,itg_log.symbol,itg_log.trend,itg_log.number,itg_log.create_time,
            itg_log.before_number,itg_log.after_number,itg_log.reason,itg_log.order_id,m.nickname,m.account,oi.order_sn')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = itg_log.m_id',
                'LEFT JOIN '.C('DB_PREFIX').'order_info oi ON oi.id = itg_log.order_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
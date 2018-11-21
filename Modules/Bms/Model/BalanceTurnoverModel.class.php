<?php
namespace Bms\Model;

/**
 * Class BalanceTurnoverModel
 * @package Bms\Model
 * 余额记录 数据层
 */
class BalanceTurnoverModel extends BmsBaseModel {

    /**
     * @param array $param  综合条件参数
     * @return array
     * 获取列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('bal_t')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'bal_t');
        //获取数据
        $list  = $this->alias('bal_t')
            ->field('bal_t.id,bal_t.user_id,user_type,bal_t.symbol,bal_t.trend,bal_t.amounts,bal_t.order_id,bal_t.order_sn,bal_t.order_type,
            bal_t.before_amounts,bal_t.after_amounts,bal_t.reason,bal_t.create_time,bal_t.status,m.nickname,m.account')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = bal_t.user_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
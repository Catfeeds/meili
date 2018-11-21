<?php
namespace FrontC\Model;

/**
 * Class BalanceTurnoverModel
 * @package FrontC\Model
 * 余额记录
 */
class BalanceTurnoverModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total = $this->alias('bal_t')->where($param['where'])->count();
        //创建分页对象
        $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'bal_t');
        //获取数据
        $list = $this->alias('bal_t')
                    ->field('bal_t.id,bal_t.symbol,bal_t.trend,bal_t.amounts,bal_t.create_time')
                    ->where($param['special_where'])
                    ->select();
        //返回记录 根据ID顺序排序
        return array('list' => sort_by_array($param['ids_for_sort'], $list, 'id'), 'page' => $Page->show());
    }
}
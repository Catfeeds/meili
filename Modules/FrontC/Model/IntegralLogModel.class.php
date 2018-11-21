<?php
namespace FrontC\Model;

/**
 * Class IntegralLogModel
 * @package FrontC\Model
 * 积分记录数据模型
 */
class IntegralLogModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total = $this->alias('itg_log')->where($param['where'])->count();
        //创建分页对象
        $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'itg_log');
        //获取数据
        $list = $this->alias('itg_log')
                    ->field('itg_log.id,itg_log.symbol,itg_log.trend,itg_log.number,itg_log.create_time')
                    ->where($param['special_where'])
                    ->select();
        //返回记录 根据ID顺序排序
        return array('list' => sort_by_array($param['ids_for_sort'], $list, 'id'), 'page' => $Page->show());
    }
}
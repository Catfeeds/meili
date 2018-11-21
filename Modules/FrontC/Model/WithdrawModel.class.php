<?php
namespace FrontC\Model;

/**
 * Class WithdrawModel
 * @package FrontC\Model
 * 提现记录
 */
class WithdrawModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('wd')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'wd');
        //获取数据
        $list   = $this->alias('wd')
                ->field('wd.id wd_id,amounts,card_number,create_time')
                ->where($param['special_where'])
//                ->join(array(
//                    'INNER JOIN ' . C('DB_PREFIX') . 'member m ON m.id = g_comm.m_id',
//                ))
                ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list, 'wd_id'), 'page'=>$Page->show());
    }
}
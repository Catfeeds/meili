<?php
namespace FrontC\Model;

/**
 * Class GoodsCommentModel
 * @package FrontC\Model
 * 商品评价模块数据层
 */
class GoodsCommentModel extends FrontBaseModel {


    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('g_comm')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'g_comm');
        //获取数据
        $list   = $this->alias('g_comm')
                ->field('g_comm.id g_comm_id,g_comm.content,g_comm.pictures,g_comm.create_time,g_comm.level,m.head,m.nickname')
                ->where($param['special_where'])
                ->join(array(
                    'INNER JOIN ' . C('DB_PREFIX') . 'member m ON m.id = g_comm.m_id',
                ))
                ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list, 'g_comm_id'), 'page'=>$Page->show());
    }
}
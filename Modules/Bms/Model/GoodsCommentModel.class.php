<?php
namespace Bms\Model;

/**
 * Class GoodsCommentModel
 * @package Bms\Model
 * 商品评价数据层
 */
class GoodsCommentModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array ();

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
        $total  = $this->alias('g_comm')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'g_comm');
        //获取数据
        $list   = $this->alias('g_comm')
            ->field('g_comm.id,g_comm.m_id,g_comm.goods_id,g_comm.content,g_comm.pictures,g_comm.level,g_comm.create_time,g_comm.status,
            m.account,m.nickname,goods.goods_name')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = g_comm.m_id',
                'LEFT JOIN ' . C('DB_PREFIX') . 'goods goods ON goods.id = g_comm.goods_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
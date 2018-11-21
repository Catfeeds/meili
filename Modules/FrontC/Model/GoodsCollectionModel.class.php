<?php
namespace FrontC\Model;

/**
 * Class GoodsCollectionModel
 * @package FrontC\Model
 * 商品收藏模型
 */
class GoodsCollectionModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total = $this->alias('g_coll')->where($param['where'])->count();
        //创建分页对象
        $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'g_coll');
        //获取数据
        $list = $this->alias('g_coll')
            ->field('g_coll.id,g_coll.goods_id,goods.goods_name,goods.cover,goods.price')
            ->where($param['special_where'])
            ->join(array(
                'INNER JOIN ' . C('DB_PREFIX') . 'goods goods ON goods.id = g_coll.goods_id and goods.status=1',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list' => sort_by_array($param['ids_for_sort'], $list, 'id'), 'page' => $Page->show());
    }
}
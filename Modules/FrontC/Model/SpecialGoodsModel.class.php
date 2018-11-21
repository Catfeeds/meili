<?php
namespace FrontC\Model;

/**
 * Class SpecialGoodsModel
 * @package FrontC\Model
 * 专题商品模块数据层
 */
class SpecialGoodsModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    function getList($param = array()) {
        $inner_join = array(
            'INNER JOIN '.C('DB_PREFIX').'goods goods ON goods.id = spe_goods.goods_id AND goods.status=1',
        );
        //数据总数
        $total = $this->alias('spe_goods')->where($param['where'])->join($inner_join)->count();
        //创建分页对象
        $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'spe_goods', $inner_join);
        //获取数据
        $list  = $this->alias('spe_goods')
                    ->field('spe_goods.id,goods.id goods_id,goods.goods_sn,goods.goods_name,goods.cover,goods.price,goods.market_price')
                    ->where($param['special_where'])
                    ->join($inner_join)
                    ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
<?php
namespace Bms\Model;

/**
 * Class SpecialGoodsModel
 * @package Bms\Model
 * 专题商品
 */
class SpecialGoodsModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array ();

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array ();

    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('spe_goods')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'spe_goods');
        //获取数据
        $list  = $this->alias('spe_goods')
                      ->field('spe_goods.id,spe_goods.sort,goods.goods_sn,goods.goods_name,goods.price,goods.status,goods.stock,goods_cate.name goods_cate_name')
                      ->where($param['special_where'])
                      ->join(array(
                          'LEFT JOIN '.C('DB_PREFIX').'goods goods ON goods.id = spe_goods.goods_id',
                          'LEFT JOIN '.C('DB_PREFIX').'goods_category goods_cate ON goods_cate.id = goods.goods_cate_id',
                      ))
                      ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}
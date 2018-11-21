<?php

namespace FrontC\Model;

/**
 * Class GoodsModel
 * @package FrontC\Model
 * 商品模块数据层
 */
class GoodsModel extends FrontBaseModel
{

    /*
     * [商城商品详情调用]
     * */
    public function findGoods($param = array())
    {
        $goods = $this->alias('goods')
            ->field('goods.id goods_id,goods.goods_name,goods.price,goods.market_price,goods.sales,goods.goods_desc,file.abs_url cover')
            ->where($param)
            ->order(array('goods.id desc'))
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = goods.cover',
            ))
            ->select();
        if (empty($goods))
            return array();
        return $goods;
    }

    /*
     *[商城获取指定分类商品调用、]
     * */
    public function getGoods($param = array())
    {
        $goods = $this->alias('goods')
            ->field('goods.id goods_id,goods.goods_name,goods.price,goods.goods_cate_id,file.abs_url cover')
            ->where($param)
            ->order(array('goods.id desc'))
            ->limit(6)
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = goods.cover',
            ))
            ->select();
        if (empty($goods))
            return array();
        return $goods;
    }
    /*
     * 获取热门商品调用
     * */
    public function getHotGoods($param=array())
    {
        $goods = $this->alias('goods')
            ->field('goods.id goods_id,goods.goods_name,goods.price,goods.goods_cate_id,file.abs_url cover')
            ->where($param)
            ->order(array('goods.id desc'))
            ->limit(6)
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = goods.cover',
            ))
            ->select();
        $Page = new \Think\Page(count($goods), 9, $_REQUEST);
        $result = array_slice($goods, $Page->firstRow, $Page->listRows);
        if(empty($result))
            return array();
        return $result;
    }


    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    function getList($param = array())
    {
        //数据总数
        $total = $this->alias('goods')->where($param['where'])->count();
        //创建分页对象
        $Page = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'goods');
        //获取数据
        $list = $this->alias('goods')
            ->field('goods.id goods_id,goods.goods_name,goods.cover,goods.price,goods.market_price')
            ->where($param['special_where'])
//            ->join(array(
//                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = goods.m_id',
//            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list' => sort_by_array($param['ids_for_sort'], $list, 'goods_id'), 'page' => $Page->show());
    }

    /**
     * @param $relation_goods
     * @return mixed
     * 获取文章关联商品
     */
    function getRelationGoods($relation_goods = '')
    {
        if (empty($relation_goods))
            return array();
        //获取数据
        return $this->alias('goods')
            ->field('goods.id goods_id,goods.goods_name,goods.price,goods.status,file.abs_url cover')
            ->where(array('goods.id' => array('IN', $relation_goods)))
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = goods.cover',
            ))
            ->select();
    }

    /**
     * @param array $param
     * @return array|mixed
     * 查询商品详情
     */
    function findRow($param = array())
    {
        $row = $this->alias('goods')
            ->field('goods.id goods_id,goods.goods_name,goods.cover,goods.goods_sn,goods.unit,goods.pictures,goods.price,goods.market_price,goods.goods_desc')
            ->where($param['where'])
            //            ->join(array(
            //                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = goods.m_id',
            //            ))
            ->find();
        return $row;
    }

    /**
     * @param int $goods_id
     * @return array
     * 获取商品属性信息
     */
    function getGoodsAttr($goods_id = 0)
    {
        if (empty($goods_id))
            return array();
        return M()->table(C('DB_PREFIX') . 'goods_attribute goods_attr,' . C('DB_PREFIX') . 'attribute attr')
            ->where(array('_string' => 'goods_attr.goods_id=' . $goods_id . ' AND goods_attr.attr_id=attr.id AND attr.attr_type>1'))
            ->field('goods_attr.id goods_attr_id,goods_attr.attr_id,goods_attr.attr_value,goods_attr.attr_price,attr.attr_name')
            ->order('goods_attr.attr_id ASC')
            ->select();
    }
}
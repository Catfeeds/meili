<?php

namespace FrontC\Service;

/**
 * Class GoodsService
 * @package FrontC\Service
 * 商品模块服务层
 */
class GoodsService extends FrontBaseService
{

    /*
     * [mall商城首页调用、]
     * 获取商城首页展示商品
     * */
    public function getHotGoods()
    {
        $goods = M('Goods')->alias('goods')
            ->field('goods.id goods_id,goods.goods_name,goods.price,file.abs_url cover')
            ->where(array('goods.status' => 1,'goods.is_best'=>1))
            ->order(array('goods.id desc'))
            ->limit(6)
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = goods.cover',
            ))
            ->select();
        return $goods;
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     * 获取商品列表
     */
    function getGoodsList($custom_param, $extra = array())
    {
        //状态必须为正常状态
        $param['where']['goods.status'] = 1;
        //每页数量
        $param['page_size'] = 10;
        //是否有外部其他自定义条件  如果有替换条件
        if (!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/Goods')->getList($param);
        //数据列表 //分页信息
        $list = $result['list'];
        $page = $result['page'];
        //如果没有数据返回空数组
        if (empty($list))
            return array();
        //处理列表数据
        array_walk($list, 'FrontC\Service\GoodsService::dataFactory', $extra);

        return $list;
    }

    /**
     * @param int $spe_id
     * @return array
     * 获取专题商品列表
     */
    public function getSpeGoods($spe_id = 0)
    {
        if (empty($spe_id))
            return array();
        //专题ID
        $param['where']['spe_goods.spe_id'] = $spe_id;
        //排序
        $param['order'] = 'spe_goods.sort DESC,goods.sort DESC,goods.id DESC';
        //每页数量
        $param['page_size'] = 8;
        //调用数据模型层方法获取数据
        $result = D('FrontC/SpecialGoods')->getList($param);
        //数据列表 //分页信息
        $list = $result['list'];
        $page = $result['page'];
        //如果没有数据返回空数组
        if (empty($list))
            return array();
        //处理列表数据
        array_walk($list, 'FrontC\Service\GoodsService::dataFactory', $extra);

        $spe_name = M('Special')->where(array('id' => $spe_id))->getField('name');

        return array('list' => $list, 'spe_name' => $spe_name);
    }

    /**
     * @param $value
     * @param $key
     * @param $extra
     * 数据加工
     */
    public static function dataFactory(&$value, $key, $extra)
    {
        //封面
        $value['cover'] = api('File/getFiles', array($value['cover'], array('id', 'abs_url')))[0]['abs_url'];
        //forward
        //$value['forward'] = 'http://wenf-wap.toocms.com/index.php?m=Wap&c=Goods&a=detail&goods_id='.$value['goods_id'].'';
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     * 商品详情
     */
    function getDetail($custom_param, $extra = array())
    {
        //状态必须为正常状态
        $param['where']['goods.status'] = 1;
        //是否有外部其他自定义条件  如果有替换条件
        if (!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $row = D('FrontC/Goods')->findRow($param);
        if (!$row)
            return array();
        //是否收藏
        //$row['is_coll'] = $this->isColl($extra['m_id'], $extra['goods_id']);
        //图文详情处理
        $row['goods_desc'] = path2abs($row['goods_desc']);

        return $row;
    }

    /**
     * @param int $m_id
     * @param int $goods_id
     * @return int
     * 是否收藏过该商品
     */
    function isColl($m_id = 0, $goods_id = 0)
    {
        if (M('GoodsCollection')->where(array('m_id' => $m_id, 'goods_id' => $goods_id))->count())
            return 1;
        return 0;
    }

    /**
     * @param int $goods_id
     * @param string $pictures
     * @return array|mixed
     * 获取商品相册
     */
    function getGoodsPictures($goods_id = 0, $pictures = '')
    {
        if (empty($goods_id) && empty($pictures))
            return array();
        if (!empty($pictures)) {
            return api('File/getFiles', array($pictures, array('id', 'abs_url')));
        } else {

        }
    }

    /**
     * @param $goods_id
     * @return array
     * 获取商品的多选属性
     */
    function getGoodsAttr($goods_id)
    {
        //获取商品属性
        $goods_attr_array = D('FrontC/Goods')->getGoodsAttr($goods_id);
        //没有属性
        if (empty($goods_attr_array))
            return array();
        //存储商品每个属性的第一个属性值得商品属性ID goods_attr_id 该值在页面中默认显示
        //$first_goods_attr_id_arr = array();
        //同种属性数组归纳
        $_goods_attr_list = array();
        foreach ($goods_attr_array as $key => $goods_attr) {
            //属性ID
            $_goods_attr_list[$goods_attr['attr_id']]['attr_id'] = $goods_attr['attr_id'];
            //属性名称
            $_goods_attr_list[$goods_attr['attr_id']]['attr_name'] = $goods_attr['attr_name'];
            //商品属性值
            $_goods_attr_list[$goods_attr['attr_id']]['attr_values'][$key]['attr_value'] = $goods_attr['attr_value'];
            //商品属性ID
            $_goods_attr_list[$goods_attr['attr_id']]['attr_values'][$key]['goods_attr_id'] = $goods_attr['goods_attr_id'];
            //商品属性的价格
            $_goods_attr_list[$goods_attr['attr_id']]['attr_values'][$key]['goods_attr_price'] = $goods_attr['attr_price'];
        }
        //去 attr_values 键值
        foreach ($_goods_attr_list as &$_goods_attr) {
            $_goods_attr['attr_values'] = array_values($_goods_attr['attr_values']);
        }
        //获取商品每个属性的第一个属性值得商品属性ID goods_attr_id 该值在页面中默认显示
        /*foreach($goods_attr_list as $goods_attr) {
            foreach($goods_attr['attr_values'] as $attr) {
                $first_goods_attr_id_arr[]     = $attr['goods_attr_id'];
                $this->first_goods_attr_price += $attr['goods_attr_price'];
                //只获取数组的第一个值 后跳出该循环
                break;
            }
        }
        //获取该商品属性ID组合是否有库存  联合地区ID组合 查询
        $goods_attr = implode('|',$first_goods_attr_id_arr);
        //库存查询条件
        $where_1['goods_attr'] = $goods_attr;
        $where_1['goods_id']   = $goods_id;
        //查询字段
        $fields_1 = "product_id,product_stock";

        $product = M('GoodsProducts')->where($where_1)
            ->field($fields_1)
            ->find();

        if($product && $product['product_stock'] > 0) {
            $this->has_stock = true;
        }*/
        return array_values($_goods_attr_list);
    }

    /**
     * @param int $goods_id
     * @param string $goods_attr_ids 商品属性ID串
     * @param array $goods
     * @param int $product_id
     * @return array
     * 获取商品货品的库存和货品价格
     */
    function getStockPrice($goods_id = 0, $goods_attr_ids = '', $goods = array(), $product_id = 0)
    {
        if (empty($goods_id) && empty($goods))
            return $this->setServiceInfo('参数错误！', false);
        //获取商品基本价格和总库存信息
        if (empty($goods)) {
            $goods = M('Goods')->where(array('id' => $goods_id))->field('price,stock')->find();
        }
        if (empty($goods))
            return $this->setServiceInfo('商品不存在！', false);
        //如果商品属性ID串为空  返回商品基本价格和总库存
        if (empty($goods_attr_ids) && empty($product_id)) {
            return array('stock' => $goods['stock'], 'price' => $goods['price']);
        }
        //商品属性ID串不为空情况
        /** 获取货品库存 **/
        //获取商品货品ID、货品库存
        $where['goods_id'] = empty($goods_id) ? $goods['goods_id'] : $goods_id;
        if (!empty($goods_attr_ids)) {
            $where['goods_attr_ids'] = $goods_attr_ids;
        }
        if (!empty($product_id)) {
            $where['id'] = $product_id;
        }
        $fields = "id product_id,product_stock";
        $product = M('Products')->where($where)->field($fields)->find();
        //判断是否查到货品信息
        if (empty($product)) {
            //没有查到 则库存返回0 价格返回基础价格
            return array('stock' => 0, 'price' => $goods['price']);
        }
        $stock = $product['product_stock'];
        /** 获取属性价格汇总 **/
        //商品属性ID串转化为数组
        $goods_attr_id_array = explode('|', $goods_attr_ids);
        //获取商品属性列表
        unset($where);
        $where['id'] = array('IN', $goods_attr_id_array);
        $goods_attr_list = M('GoodsAttribute')->where($where)->field('attr_price')->select();
        //属性价格汇总
        $attr_price_count = 0;
        for ($i = 0; $i < count($goods_attr_list); $i++) {
            $attr_price_count += $goods_attr_list[$i]['attr_price'];
        }
        $price = $goods['price'] + $attr_price_count;
        //返回
        return array('stock' => $stock, 'price' => strval($price), 'product_id' => $product['product_id']);
    }

    /**
     * @param int $goods_id
     * @return bool
     * 判断商品是否存在单选属性
     */
    function isSpec($goods_id = 0)
    {
        //获取商品属性列表
        $goods_attr_list = M('GoodsAttribute')->where(array('goods_id' => $goods_id))->field('attr_id')->select();
        //没有设置属性则 不是规格属性商品
        if (empty($goods_attr_list))
            return false;
        //转化属性ID数组
        $attr_id_arr = array_column($goods_attr_list, 'attr_id');
        //查看是否有 attr_type=2即 单选属性  如果有则是规格属性商品
        $at2_count = M('Attribute')->where(array('id' => array('IN', $attr_id_arr), 'attr_type' => 2))->count();
        if (!empty($at2_count))
            return $at2_count;
        return false;
    }

    /**
     * @param int $goods_id
     * @param int $product_id
     * @param int $number
     * @param int $symbol 1--加  2--减
     * @return bool
     * 更新商品库存
     */
    function updStock($goods_id = 0, $product_id = 0, $number = 0, $symbol = 2)
    {
        if (empty($goods_id) || empty($number))
            return false;
        //是否存在货品
        if (empty($product_id)) {
            $symbol == 2 ? M('Goods')->where(array('id' => $goods_id))->setDec('stock', $number) : M('Goods')->where(array('id' => $goods_id))->setInc('stock', $number);
        } else {
            $symbol == 2 ? M('Products')->where(array('id' => $product_id))->setDec('product_stock', $number) : M('Products')->where(array('id' => $product_id))->setInc('product_stock', $number);
            D('Goods', 'Service')->updGoodsStockByProductStock($goods_id);
        }
        return reue;
    }
}
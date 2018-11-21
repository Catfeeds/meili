<?php
namespace Bms\Logic;

/**
 * Class ProductsLogic
 * @package Bms\Logic
 * 商品货品逻辑层
 */
class ProductsLogic extends BmsBaseLogic {

    /**
     * @param int $goods_id
     * @return mixed
     * 获取货品列表 处理货品信息
     */
    function getProducts($goods_id = 0) {
        if(empty($goods_id))
            return array();
        //取商品的货品列表
        $products = D('Products')->getList(array('where'=>array('goods_id'=>$goods_id)));
        //获取商品属性列表
        $goods_attr_list = D('Goods')->getGoodsAttrList($goods_id);
        //处理成格式
        // array
        //   goods_attr_id=> string '红木'
        //   goods_attr_id => string '50cm'
        //   goods_attr_id => string '100cm'
        foreach($goods_attr_list as $goods_attr) {
            $_goods_attr_list[$goods_attr['goods_attr_id']] = $goods_attr['attr_value'];
        }
        //转化商品属性ID字符串为属性值数组
        foreach($products as $key => &$pro) {
            //转换商品属性 12|23|34 字符串 为数组
            $goods_attr_id_arr = explode('|', $pro['goods_attr_ids']);
            //获取商品属性ID对应的属性值
            if (is_array($goods_attr_id_arr)) {
                $attr_values = array();
                foreach ($goods_attr_id_arr as $goods_attr_id) {
                    $attr_values[] = $_goods_attr_list[$goods_attr_id];
                }
                $pro['goods_attr'] = $attr_values;
            }
        }
        return $products;
    }

    /**
     * @param array $request
     * @return boolean
     * 修改字段前执行方法
     */
    protected function beforeSetField($request = array()) {
        //如果是修改货品号 判断是否重复
        if($request['field'] == 'product_sn') {
            if(M('Products')->where(array('product_sn'=>$request['value']))->count()) {
                return $this->setLogicInfo('货品号不能重复！', false);
            }
        }
        return true;
    }

    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 修改字段成功后执行
     */
    protected function afterSetField($result = 0, $request = array()) {
        //如果是修改货品库存 判断是否重复
        if($request['field'] == 'product_stock') {
            //更新商品库存
            $this->updGoodsStockByProductStock(M('Products')->where(array('id'=>$request['ids']))->getField('goods_id'));
        }
        return true;
    }

    /**
     * @param array $request
     * @return bool|mixed|void
     * 添加货品
     */
    function update($request = array()) {
        $goods_id           = $_POST['goods_id'];
        $attr_arr           = $_POST['attr']; //属性数组
        $product_sn_arr     = $_POST['product_sn']; //货品号数组
        $product_stock_arr  = $_POST['product_stock']; //货品库存数组
        //循环数组
        foreach($product_sn_arr as $key => $product_sn) {
            //判断货品号是否重复  货品号不为空
            if (!empty($product_sn) && !$this->isProductSnExist($product_sn)) {
                $this->setLogicInfo('货品号有重复！'); return false;
            }
            //获取规格在商品属性表中的id
            foreach($attr_arr as $attr_id => $attr_value) {
                if(empty($attr_value[$key])) {
                    $this->setLogicInfo('请完善必填项信息！'); return false;
                }
                $is_spec_list[$attr_id]     = 'true';
                $attr_id_list[$attr_id]     = $attr_id;
                $value_price_list[$attr_id] = $attr_value[$key] . chr(9) . ''; //chr(9)-tab空格
            }
            //获取商品属性ID数组
            $goods_attr_id_array = D('Goods', 'Logic')->handleGoodsAttr($goods_id, $attr_id_list, $is_spec_list, $value_price_list);
            //商品属性ID数组重新排序 删除不是单选属性的数据
            $goods_attr_id_array = D('Goods')->sortGoodsAttrIdArray($goods_attr_id_array);
            //商品属性ID数组 处理成字符串
            $goods_attr_ids = implode('|', $goods_attr_id_array['sort']);
            //判断商品货品是否重复
            if (!$this->isGoodsAttrExist($goods_id, $goods_attr_ids)) {
                $this->setLogicInfo('相同货品已经存在！'); return false;
            }
            //货品数据
            $data[] = array(
                'goods_id'          => $goods_id,
                'goods_attr_ids'    => $goods_attr_ids,
                'product_sn'        => $product_sn, //
                'product_stock'     => $product_stock_arr[$key],
            );
        }
        if(!M('Products')->addAll($data)) {
            $this->setLogicInfo('添加货品失败！'); return false;
        } else {
            //更新商品库存
            $this->updGoodsStockByProductStock($goods_id);
            $this->setLogicInfo('添加货品成功！'); return true;
        }
    }

    /**
     * @param $product_sn
     * @return bool
     * 判断货品号是否存在
     */
    function isProductSnExist($product_sn) {
        if (M('Products')->where(array('product_sn'=>$product_sn))->count()) {
            return false;
        }
        return true;
    }

    /**
     * @param $goods_id
     * @param $goods_attr_ids
     * @return bool
     * 判断货品的商品属性串是否存在
     */
    function isGoodsAttrExist($goods_id, $goods_attr_ids) {
        if (M('Products')->where(array('goods_id'=>$goods_id, 'goods_attr_ids'=>$goods_attr_ids))->count()) {
            return false;
        }
        return true;
    }

    /**
     * @param $goods_id
     * 根据货品总库存更新商品库存
     */
    function updGoodsStockByProductStock($goods_id) {
        //货品总库存
        $product_stock_count = D('Products')->productStockCount($goods_id);
        //更新商品库存
        D('Goods')->where(array('id'=>$goods_id))->data(array('stock'=>$product_stock_count))->save();
    }
}
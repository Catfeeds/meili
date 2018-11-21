<?php
namespace Common\Service;
use Common\Base\BaseService;

/**
 * Class GoodsService
 * @package Common\Service
 * 商品 数据服务层 公共层
 */
class GoodsService extends BaseService {

    /**
     * @param $goods_id
     * 根据货品总库存更新商品库存
     */
    function updGoodsStockByProductStock($goods_id) {
        //货品总库存
        $product_stock_count = $this->productStockCount($goods_id);
        //更新商品库存
        M('Goods')->where(array('id'=>$goods_id))->data(array('stock'=>$product_stock_count))->save();
    }

    /**
     * @param $goods_id
     * @return int
     * 获得商品的货品总库存
     */
    function productStockCount($goods_id = 0) {
        if(empty($goods_id)) {
            return -1;
        }
        $count = M('Products')->where(array('goods_id'=>$goods_id))->sum('product_stock');
        return empty($count) ? 0 : $count;
    }
}
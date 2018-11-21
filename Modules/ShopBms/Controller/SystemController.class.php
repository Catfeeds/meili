<?php
namespace ShopBms\Controller;

/**
 * Class SystemController
 * @package Bms\Controller
 * 一些系统操作控制器 清缓存 提示
 */
class SystemController extends BmsBaseController {

    /**
     * 清楚一些 缓存
     */
    function clearCache() {
        S('TotalStat_Cache', null);
        S('PropStat_Cache', null);
        S('GoodsCategory_Cache', null);
        $this->success('清除成功！');
    }

    /**
     * 未处理/未完成  提示信息
     */
    function tip() {
        $not_delivery_order = M('OrderInfo')->where(array('status'=>2))->count();
        $data = array('not_delivery_order'=>$not_delivery_order);
        $this->success('', '', true, $data);
    }
}

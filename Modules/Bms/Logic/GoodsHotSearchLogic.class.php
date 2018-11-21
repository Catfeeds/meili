<?php
namespace Bms\Logic;

/**
 * Class GoodsHotSearchLogic
 * @package Bms\Logic
 * 商品热门搜索逻辑层
 */
class GoodsHotSearchLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'id DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //返回数据
        return D('GoodsHotSearch')->getList($param);
    }

    /**
     * @param $result
     * @param array $request
     * @return boolean
     * 新增、更新、修改字段、删除后执行
     */
    protected function afterAll($result = 0, $request = array()) {
        S('GoodsHotSearch_Cache', null);
        return true;
    }
}